<?php

namespace App\Http\Controllers;

use App\Models\CajaMovimiento;
use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StockController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $products = Product::all();

        // Construimos la consulta base
        $query = InventoryMovement::with(['product', 'user'])->latest();

        // Si el usuario no es admin (o no tiene permiso 'manage-users'), solo ve sus movimientos
        if (Gate::denies('manage-users')) {
            $query->where('user_id', $user->id);
        }

        $movements = $query->get();

        // Calculamos métricas basadas únicamente en los registros obtenidos
        $totalMovements = $movements->count();
        $totalEntradas = $movements->where('type', 'entrada')->count();
        $totalSalidas = $movements->where('type', 'salida')->count();

        return view('stock.index', compact(
            'products',
            'movements',
            'totalMovements',
            'totalEntradas',
            'totalSalidas'
        ));
    }

    public function store(Request $request)
    {
        // 1. Validamos todos los inputs que vienen del formulario
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:entrada,salida',
            'reason' => 'required|string',
            'precio_unitario' => 'nullable|numeric|min:0',
            'monto_recibido' => 'nullable|numeric|min:0',
        ]);

        $user = auth()->user();
        $cajaActiva = null;

        // 2. Si es Venta directa, verificamos la caja abierta y que el dinero recibido alcance
        if ($request->type === 'salida' && $request->reason === 'Venta directa') {
            $cajaActiva = CajaMovimiento::where('user_id', $user->id)
                ->where('estado', 'abierta')
                ->first();

            if (! $cajaActiva) {
                return redirect()->back()->withInput()->with('error', 'Debes abrir un turno de caja antes de realizar una Venta Directa.');
            }

            $totalVenta = $request->quantity * ($request->precio_unitario ?? 0);
            if (($request->monto_recibido ?? 0) < $totalVenta) {
                return redirect()->back()->withInput()->with('error', 'El dinero recibido es menor al total a cobrar.');
            }
        }

        try {
            DB::transaction(function () use ($request, $user, $cajaActiva) {
                // 3. Obtenemos el producto PRIMERO para tener acceso a sus datos
                $product = Product::findOrFail($request->product_id);
                $cantidad = (int) $request->quantity;

                // 4. Actualizamos el stock y validamos existencias
                if ($request->type === 'salida') {
                    if ($product->stock < $cantidad) {
                        throw new \Exception("Stock insuficiente. Stock actual: {$product->stock} pzas.");
                    }
                    $product->stock -= $cantidad;
                } else {
                    $product->stock += $cantidad;
                }
                $product->save();

                // 5. Preparamos montos para el registro
                $precioUnitario = $request->precio_unitario ?? 0;
                $totalCalculado = ($request->type === 'salida' && $request->reason === 'Venta directa')
                    ? ($cantidad * $precioUnitario)
                    : 0;

                $montoRecibido = $request->monto_recibido ?? 0;
                $cambioEntregado = $montoRecibido > 0 ? ($montoRecibido - $totalCalculado) : 0;

                // 6. Guardamos el registro con variables ya definidas
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'caja_id' => $cajaActiva ? $cajaActiva->id : null,
                    'type' => $request->type,
                    'quantity' => $cantidad,
                    'reason' => $request->reason,
                    'unit_price' => $precioUnitario,
                    'total' => $totalCalculado,
                    'monto_recibido' => $montoRecibido,
                    'cambio' => $cambioEntregado,
                    'date' => now(),
                ]);
            });

            return redirect()->route('stock.index')->with('success', 'Movimiento registrado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
