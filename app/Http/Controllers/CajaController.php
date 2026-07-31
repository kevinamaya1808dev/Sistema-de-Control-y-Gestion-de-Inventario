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
     * Helper para calcular totales de caja de forma estandarizada.
     */
    private function calcularTotalesCaja($movimientos, float $montoApertura = 0)
    {
        $sumaTotal = fn ($coleccion) => $coleccion->sum(fn ($m) => $m->total ?? ($m->quantity * $m->unit_price));

        $ventasTurno = $movimientos->filter(function ($m) {
            return $m->type === 'salida' && str_starts_with($m->reason ?? '', 'Venta directa');
        });

        $gastosTurno = $movimientos->filter(function ($m) {
            $esGastoOperativo = ($m->type === 'salida' && !str_starts_with($m->reason ?? '', 'Venta directa'));
            $esCompraSurtida  = ($m->type === 'entrada' && ($m->total ?? 0) > 0);
            return $esGastoOperativo || $esCompraSurtida;
        });

        // Clasificación por método de pago
        $ventasEfectivo = $ventasTurno->filter(fn ($m) => empty($m->payment_method) || $m->payment_method === 'cash');
        $ventasTarjeta  = $ventasTurno->filter(fn ($m) => $m->payment_method === 'card');
        $ventasTransfer = $ventasTurno->filter(fn ($m) => $m->payment_method === 'transfer');

        $totalVentasEfectivo      = $sumaTotal($ventasEfectivo);
        $totalVentasTarjeta       = $sumaTotal($ventasTarjeta);
        $totalVentasTransferencia = $sumaTotal($ventasTransfer);
        $totalVentasGeneral       = $sumaTotal($ventasTurno);
        $totalGastos              = $sumaTotal($gastosTurno);

        $saldoEfectivoEnCaja = $montoApertura + $totalVentasEfectivo - $totalGastos;

        return compact(
            'ventasTurno',
            'gastosTurno',
            'totalVentasEfectivo',
            'totalVentasTarjeta',
            'totalVentasTransferencia',
            'totalVentasGeneral',
            'totalGastos',
            'saldoEfectivoEnCaja'
        );
    }

    public function index()
    {
        $cajaActiva = $this->getCajaActiva();
        $movimientos = collect();

        if ($cajaActiva) {
            $movimientos = InventoryMovement::with(['product.sizes', 'user'])
                ->where('caja_id', $cajaActiva->id)
                ->latest()
                ->get();
        }

        $montoApertura = $cajaActiva ? (float) $cajaActiva->monto_apertura : 0;
        $totales = $this->calcularTotalesCaja($movimientos, $montoApertura);

        return view('caja.index', array_merge(['cajaActiva' => $cajaActiva], $totales));
    }

    public function pos()
    {
        $cajaActiva = $this->getCajaActiva();

        if (!$cajaActiva) {
            return redirect()->route('caja.index')
                ->with('error', 'Debes abrir un turno de caja antes de acceder al Punto de Venta.');
        }

        $products = Product::with(['sizes', 'category'])->get()->map(function ($p) {
            $sizes = $p->sizes->map(fn ($s) => [
                'id'    => $s->id,
                'name'  => $s->name ?? $s->size ?? $s->talla,
                'stock' => (int) ($s->pivot->stock ?? $s->stock ?? 0),
            ]);

            $totalStock = $sizes->isNotEmpty() ? $sizes->sum('stock') : (int) ($p->stock ?? 0);

            return [
                'id'         => $p->id,
                'name'       => $p->name ?? $p->nombre,
                'price'      => (float) ($p->price ?? $p->precio ?? 0),
                'barcode'    => $p->sku ?? $p->barcode ?? 'N/A',
                'image_path' => $p->image ? route('products.image', $p->image) : null,
                'stock'      => $totalStock,
                'sizes'      => $sizes,
                'category'   => ['name' => data_get($p, 'category.name', 'General')],
            ];
        });

        return view('caja.pos', [
            'products'   => $products,
            'cajaActiva' => $cajaActiva,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.id'      => 'required|exists:products,id',
            'items.*.qty'     => 'required|integer|min:1',
            'items.*.price'   => 'required|numeric|min:0',
            'items.*.size'    => 'nullable|string',
            'items.*.size_id' => 'nullable',
            'payment_method'  => 'required|in:cash,card,transfer',
            'reference'       => 'nullable|string|max:255',
            'total'           => 'required|numeric|min:0',
            'received_amount' => 'required|numeric|min:0',
            'change'          => 'nullable|numeric|min:0',
        ]);

        $cajaActiva = $this->getCajaActiva();

        if (!$cajaActiva) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No hay una caja abierta para procesar ventas.',
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

                    if ($sizeId || $sizeName) {
                        $size = $sizeId
                            ? $product->sizes->firstWhere('id', $sizeId)
                            : $product->sizes->firstWhere('name', $sizeName);

                        if (!$size) {
                            throw new Exception("La talla especificada no existe para '{$product->name}'.");
                        }

                        $currentSizeStock = (int) ($size->pivot->stock ?? $size->stock ?? 0);

                        if ($currentSizeStock < $qty) {
                            throw new Exception("Stock insuficiente para '{$product->name}' (Talla: " . ($size->name ?? $sizeName) . ").");
                        }

                        if (isset($size->pivot)) {
                            $product->sizes()->updateExistingPivot($size->id, [
                                'stock' => max(0, $currentSizeStock - $qty),
                            ]);
                        } else {
                            $size->decrement('stock', $qty);
                        }

                        if (!is_null($product->stock)) {
                            $product->decrement('stock', $qty);
                        }
                    } else {
                        if (($product->stock ?? 0) < $qty) {
                            throw new Exception("Stock insuficiente para '{$product->name}'.");
                        }
                        $product->decrement('stock', $qty);
                    }

                    InventoryMovement::create([
                        'caja_id'        => $cajaActiva->id,
                        'product_id'     => $product->id,
                        'user_id'        => auth()->id(),
                        'type'           => 'salida',
                        'quantity'       => $qty,
                        'unit_price'     => $item['price'],
                        'total'          => $qty * $item['price'],
                        'reason'         => 'Venta directa' . ($sizeName ? " (Talla: {$sizeName})" : ''),
                        'payment_method' => $request->payment_method,
                        'reference'      => $request->reference,
                        'monto_recibido' => $request->received_amount,
                        'cambio'         => $request->change ?? 0,
                    ]);

                    $productFresh = $product->fresh(['sizes']);
                    $mappedSizes = $productFresh->sizes->map(fn ($s) => [
                        'id'    => $s->id,
                        'name'  => $s->name ?? $s->size ?? $s->talla,
                        'stock' => (int) ($s->pivot->stock ?? $s->stock ?? 0),
                    ]);

                    $totalStock = $mappedSizes->isNotEmpty() ? $mappedSizes->sum('stock') : (int) ($productFresh->stock ?? 0);

                    $updatedProducts[$product->id] = [
                        'stock' => $totalStock,
                        'sizes' => $mappedSizes->toArray(),
                    ];
                }
            });

            return response()->json([
                'status'          => 'success',
                'message'         => '¡Venta procesada con éxito!',
                'updatedProducts' => $updatedProducts,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function registrarGasto(Request $request)
    {
        $cajaActiva = $this->getCajaActiva();

        if (!$cajaActiva) {
            return $this->response($request, 'error', 'No tienes una caja abierta para registrar gastos.', 422);
        }

        $request->validate([
            'monto'          => 'required|numeric|min:0.01',
            'motivo'         => 'required|string|max:255',
            'product_id'     => 'nullable|exists:products,id',
            'payment_method' => 'nullable|in:cash,card,transfer',
            'reference'      => 'nullable|string|max:255',
        ]);

        InventoryMovement::create([
            'caja_id'        => $cajaActiva->id,
            'product_id'     => $request->product_id ?? null,
            'user_id'        => auth()->id(),
            'type'           => 'salida',
            'quantity'       => 1,
            'unit_price'     => $request->monto,
            'total'          => $request->monto,
            'reason'         => 'Gasto/Salida: ' . $request->motivo,
            'payment_method' => $request->payment_method ?? 'cash',
            'reference'      => $request->reference ?? null,
        ]);

        return $this->response($request, 'success', 'Gasto/Salida de caja registrado correctamente.');
    }

    public function historial(Request $request)
    {
        $historial = CajaMovimiento::with(['user', 'movimientos'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $historial->getCollection()->transform(function ($caja) {
            $movimientos = $caja->movimientos ?? collect();
            $totales = $this->calcularTotalesCaja($movimientos, (float) $caja->monto_apertura);

            $caja->total_ventas  = $totales['totalVentasGeneral'];
            $caja->total_gastos  = $totales['totalGastos'];
            $caja->saldo_teorico = $totales['saldoEfectivoEnCaja'];

            return $caja;
        });

        return view('caja.historial', compact('historial'));
    }

    public function detalleHistorial($id)
    {
        $caja = CajaMovimiento::with(['user', 'movimientos.product'])->findOrFail($id);
        $movimientos = $caja->movimientos ?? collect();

        $totales = $this->calcularTotalesCaja($movimientos, (float) $caja->monto_apertura);

        // Mapeamos los movimientos para asegurar que tengan todos los alias que la vista/JS pueda consumir
        $movimientosMapeados = $movimientos->map(function ($m) {
            $esVenta = str_starts_with($m->reason ?? '', 'Venta directa');
            $nombreProducto = $m->product->name ?? $m->product->nombre ?? $m->reason ?? 'Movimiento sin concepto';

            return [
                'id'             => $m->id,
                'tipo'           => $esVenta ? 'Venta' : 'Gasto',
                'type'           => $m->type,
                'product'        => $m->product,
                'producto'       => $nombreProducto,
                'concept'        => $nombreProducto,
                'reason'         => $m->reason,
                'cantidad'       => $m->quantity ?? 1,
                'quantity'       => $m->quantity ?? 1,
                'precio_unit'    => (float) ($m->unit_price ?? 0),
                'total'          => (float) ($m->total ?? ($m->quantity * $m->unit_price)),
                'payment_method' => $m->payment_method ?? 'cash',
                'metodo_pago'    => $m->payment_method ?? 'cash',
                'hora'           => $m->created_at ? $m->created_at->format('H:i:s') : '--:--:--',
                'created_at'     => $m->created_at ? $m->created_at->toIso8601String() : null,
                'fecha'          => $m->created_at ? $m->created_at->format('d/m/Y H:i') : 'N/A',
            ];
        })->sortByDesc('hora')->values();

        return response()->json([
            'caja'                => $caja,
            'monto_apertura'      => (float) $caja->monto_apertura,
            'total_ventas'        => (float) $totales['totalVentasGeneral'],
            'total_efectivo'      => (float) $totales['totalVentasEfectivo'],
            'total_tarjeta'       => (float) $totales['totalVentasTarjeta'],
            'total_transferencia' => (float) $totales['totalVentasTransferencia'],
            'total_gastos'        => (float) $totales['totalGastos'],
            'saldo_esperado'      => (float) $totales['saldoEfectivoEnCaja'],
            'esperado'            => (float) $totales['saldoEfectivoEnCaja'],
            'ventas'              => $totales['ventasTurno']->values(),
            'gastos'              => $totales['gastosTurno']->values(),
            'movimientos'         => $movimientosMapeados,
        ]);
    }

    public function abrir(Request $request)
    {
        if ($this->getCajaActiva()) {
            return $this->response($request, 'error', 'Ya tienes una sesión de caja activa.', 422);
        }

        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'observaciones'  => 'nullable|string|max:500',
        ]);

        $caja = CajaMovimiento::create([
            'user_id'        => auth()->id(),
            'monto_apertura' => $request->monto_apertura,
            'fecha_apertura' => now(),
            'estado'         => 'abierta',
            'observaciones'  => $request->observaciones,
        ]);

        return $this->response($request, 'success', '¡Caja abierta exitosamente!', 200, ['caja' => $caja]);
    }

    public function cerrar(Request $request)
    {
        if (!$cajaActiva = $this->getCajaActiva()) {
            return $this->response($request, 'error', 'No se encontró ninguna caja abierta para cerrar.', 404);
        }

        $request->validate([
            'monto_cierre'  => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $movimientos = InventoryMovement::where('caja_id', $cajaActiva->id)->get();
        $totales = $this->calcularTotalesCaja($movimientos, (float) $cajaActiva->monto_apertura);

        $saldoEfectivoEnCaja = $totales['saldoEfectivoEnCaja'];
        $montoIngresado = (float) $request->monto_cierre;

        // Faltante de dinero
        if ($montoIngresado < $saldoEfectivoEnCaja) {
            $diferencia = number_format($saldoEfectivoEnCaja - $montoIngresado, 2);
            $esperadoFormatted = number_format($saldoEfectivoEnCaja, 2);

            return $this->response(
                $request, 
                'error', 
                "No es posible cerrar la caja. El dinero ingresado es menor al esperado (Faltan: $$diferencia). Se esperaban $$esperadoFormatted en físico.", 
                422
            );
        }

        // Construcción de observaciones en caso de sobrante o notas de usuario
        $obs = $cajaActiva->observaciones;
        
        if ($montoIngresado > $saldoEfectivoEnCaja) {
            $sobrante = number_format($montoIngresado - $saldoEfectivoEnCaja, 2);
            $obs .= ($obs ? ' | ' : '') . "Sobrante al cierre: $$sobrante";
        }

        if ($request->filled('observaciones')) {
            $obs .= ($obs ? ' | ' : '') . 'Cierre: ' . $request->observaciones;
        }

        $cajaActiva->update([
            'monto_cierre'  => $montoIngresado,
            'fecha_cierre'  => now(),
            'estado'        => 'cerrada',
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