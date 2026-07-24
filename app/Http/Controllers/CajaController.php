<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use App\Models\InventoryMovement;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    /**
     * Obtiene la caja activa del usuario autenticado.
     */
    private function getCajaActiva()
    {
        return CajaMovimiento::where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->first();
    }

    /**
     * Vista principal del módulo de caja.
     */
    public function index()
    {
        $cajaActiva = $this->getCajaActiva();
        $ventasTurno = collect();
        $gastosTurno = collect();

        if ($cajaActiva) {
            $movimientos = InventoryMovement::with(['product.sizes', 'user'])
                ->where('caja_id', $cajaActiva->id)
                ->where('type', 'salida')
                ->latest()
                ->get();

            // Separa las ventas directas de otros gastos/salidas
            [$ventasTurno, $gastosTurno] = $movimientos->partition(
                fn ($m) => str_starts_with($m->reason ?? '', 'Venta directa')
            );
        }

        // Suma el total de todas las ventas registradas en el turno
        $totalVentas = $ventasTurno->sum(function ($m) {
            return $m->total ?? ($m->quantity * $m->unit_price);
        });

        return view('caja.index', [
            'cajaActiva' => $cajaActiva,
            'ventasTurno' => $ventasTurno,
            'totalVentasEfectivo' => $totalVentas,
            'gastosTurno' => $gastosTurno,
            'totalGastos' => $gastosTurno->sum('total'),
        ]);
    }

    /**
     * Vista del POS con productos mapeados.
     */
    public function pos()
    {
        $products = Product::with(['sizes', 'category'])->get()->map(function ($p) {
            $sizes = $p->sizes->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name ?? $s->size ?? $s->talla,
                'stock' => (int) ($s->pivot->stock ?? $s->stock ?? 0),
            ]);

            // Si el producto tiene variantes de talla, el stock global es la suma de sus tallas
            $totalStock = $sizes->isNotEmpty()
                ? $sizes->sum('stock')
                : (int) ($p->stock ?? 0);

            return [
                'id' => $p->id,
                'name' => $p->name ?? $p->nombre,
                'price' => (float) ($p->price ?? $p->precio ?? 0),
                'barcode' => $p->sku ?? $p->barcode ?? 'N/A',
                'image_path' => $p->image ? route('products.image', $p->image) : null,
                'stock' => $totalStock,
                'sizes' => $sizes,
                'category' => ['name' => data_get($p, 'category.name', 'General')],
            ];
        });

        return view('caja.pos', [
            'products' => $products,
            'cajaActiva' => $this->getCajaActiva(),
        ]);
    }

    /**
     * Procesa la venta e inventario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.size' => 'nullable|string',
            'items.*.size_id' => 'nullable',
            'payment_method' => 'required|in:cash,card',
            'total' => 'required|numeric|min:0',
            'received_amount' => 'required|numeric|min:0',
            'change' => 'nullable|numeric|min:0',
        ]);

        if (! $cajaActiva = $this->getCajaActiva()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes un turno de caja abierto para realizar ventas.',
            ], 422);
        }

        try {
            $updatedProducts = [];

            DB::transaction(function () use ($request, $cajaActiva, &$updatedProducts) {
                foreach ($request->items as $item) {
                    $product = Product::where('id', $item['id'])->with('sizes')->lockForUpdate()->firstOrFail();
                    $sizeName = $item['size'] ?? null;
                    $sizeId = $item['size_id'] ?? null;
                    $qty = (int) $item['qty'];

                    // 1. Descuento de stock por Talla
                    if ($sizeId || $sizeName) {
                        $size = $sizeId
                            ? $product->sizes->firstWhere('id', $sizeId)
                            : $product->sizes->firstWhere('name', $sizeName);

                        if (! $size) {
                            throw new Exception("La talla especificada no existe para '{$product->name}'.");
                        }

                        $currentSizeStock = (int) ($size->pivot->stock ?? $size->stock ?? 0);

                        if ($currentSizeStock < $qty) {
                            throw new Exception("Stock insuficiente para '{$product->name}' (Talla: ".($size->name ?? $sizeName)."). Disponible: {$currentSizeStock}");
                        }

                        // Actualizar en tabla pivote
                        if (isset($size->pivot)) {
                            $newStock = max(0, $currentSizeStock - $qty);
                            $product->sizes()->updateExistingPivot($size->id, [
                                'stock' => $newStock,
                            ]);
                        } else {
                            $size->decrement('stock', $qty);
                        }

                        // Descontar del acumulado general del producto si existe esa columna
                        if (! is_null($product->stock)) {
                            $product->decrement('stock', $qty);
                        }
                    }
                    // 2. Descuento de stock en Producto sin Talla
                    else {
                        if (($product->stock ?? 0) < $qty) {
                            throw new Exception("Stock insuficiente para '{$product->name}'. Disponible: {$product->stock}");
                        }
                        $product->decrement('stock', $qty);
                    }

                    // Registrar movimiento de inventario (Salida) con recibido y cambio
                    InventoryMovement::create([
                        'caja_id' => $cajaActiva->id,
                        'product_id' => $product->id,
                        'user_id' => auth()->id(),
                        'type' => 'salida',
                        'quantity' => $qty,
                        'unit_price' => $item['price'],
                        'total' => $qty * $item['price'],
                        'reason' => 'Venta directa'.($sizeName ? " (Talla: {$sizeName})" : ''),
                        'payment_method' => $request->payment_method,
                        'monto_recibido' => $request->received_amount,
                        'cambio' => $request->change ?? 0,
                    ]);

                    // Refrescar relaciones y datos del modelo directamente desde la BD
                    $product->unsetRelation('sizes');
                    $product->load('sizes');
                    $product->refresh();

                    // Formatear la lista de tallas con los stock actualizados
                    $mappedSizes = $product->sizes->map(fn ($s) => [
                        'id' => $s->id,
                        'name' => $s->name ?? $s->size ?? $s->talla,
                        'stock' => (int) ($s->pivot->stock ?? $s->stock ?? 0),
                    ]);

                    // Calcular nuevo total disponible del producto
                    $totalStock = $mappedSizes->isNotEmpty()
                        ? $mappedSizes->sum('stock')
                        : (int) ($product->stock ?? 0);

                    // Retornar los datos para actualizar el estado del cliente JS
                    $updatedProducts[$product->id] = [
                        'stock' => $totalStock,
                        'sizes' => $mappedSizes->toArray(),
                    ];
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => '¡Venta procesada con éxito!',
                'updatedProducts' => $updatedProducts,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Historial de turnos de caja.
     */
    public function historial()
    {
        return view('caja.historial', [
            'historial' => CajaMovimiento::with('user')->latest()->paginate(10),
        ]);
    }

    /**
     * Apertura de caja.
     */
    public function abrir(Request $request)
    {
        if ($this->getCajaActiva()) {
            return $this->response($request, 'error', 'Ya tienes una sesión de caja activa.', 422);
        }

        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $caja = CajaMovimiento::create([
            'user_id' => auth()->id(),
            'monto_apertura' => $request->monto_apertura,
            'fecha_apertura' => now(),
            'estado' => 'abierta',
            'observaciones' => $request->observaciones,
        ]);

        return $this->response($request, 'success', '¡Caja abierta exitosamente! Ya puedes realizar ventas.', 200, ['caja' => $caja]);
    }

    /**
     * Cierre de caja.
     */
    public function cerrar(Request $request)
    {
        if (! $cajaActiva = $this->getCajaActiva()) {
            return $this->response($request, 'error', 'No se encontró ninguna caja abierta para cerrar.', 404);
        }

        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $obs = $cajaActiva->observaciones;
        if ($request->filled('observaciones')) {
            $obs .= ($obs ? ' | ' : '').'Cierre: '.$request->observaciones;
        }

        $cajaActiva->update([
            'monto_cierre' => $request->monto_cierre,
            'fecha_cierre' => now(),
            'estado' => 'cerrada',
            'observaciones' => $obs,
        ]);

        return $this->response($request, 'success', 'Caja cerrada correctamente. Turno finalizado.', 200, ['caja' => $cajaActiva]);
    }

    private function response(Request $request, string $status, string $message, int $code = 200, array $extra = [])
    {
        if ($request->wantsJson()) {
            return response()->json(array_merge(['status' => $status, 'message' => $message], $extra), $code);
        }

        return redirect()->route('caja.index')->with($status, $message);
    }
}
