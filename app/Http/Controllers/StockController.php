<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\CajaMovimiento;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\Gate;

class StockController extends Controller
{
    /**
     * Helper interno para obtener el ID de la caja actualmente abierta del usuario.
     */
    private function getCajaActivaId()
    {
        $caja = CajaMovimiento::where('user_id', auth()->id())
            ->where('estado', 'abierta')
            ->first();

        return $caja ? $caja->id : null;
    }

    /**
     * Vista principal del módulo de movimientos de stock / inventario.
     */
    public function index()
    {
        $user = auth()->user();

        // 1. Obtener la lista de productos para el modal de registrar movimiento
        $products = Product::with('sizes')->orderBy('name', 'asc')->get();

        // Query Base de Movimientos de Inventario
        $query = InventoryMovement::with(['product.sizes', 'user'])->latest();

        if (Gate::denies('manage-users')) {
            $query->where('user_id', $user->id);
        }

        // Métricas de Stock
        $totalMovements = (clone $query)->count();
        $totalEntradas = (clone $query)->where('type', 'entrada')->count();
        $totalSalidas = (clone $query)->where('type', 'salida')->count();

        // Paginación
        $movements = $query->paginate(20);

        return view('stock.index', compact(
            'products',
            'movements',
            'totalMovements',
            'totalEntradas',
            'totalSalidas'
        ));
    }

    /**
     * Procesa y registra un nuevo movimiento de inventario (Entrada/Salida).
     */
    public function store(StoreStockMovementRequest $request, InventoryService $inventoryService)
    {
        try {
            $validated = $request->validated();
            
            // Obtener el ID de la caja activa del usuario autenticado
            $cajaId = $this->getCajaActivaId();

            // Evaluar si la entrada/salida requiere pagarse con dinero de caja
            $pagarConCaja = $request->boolean('pagar_con_caja');

            // Determinar costo / precio unitario y total
            $quantity = (int) ($validated['quantity'] ?? 1);
            $unitPrice = (float) ($request->input('unit_price') ?? $request->input('cost') ?? 0);
            $totalCost = (float) ($request->input('total') ?? ($unitPrice * $quantity));

            // Si se marcó que el pago del reabastecimiento sale de la caja y no hay caja abierta, lanzar alerta
            if ($pagarConCaja && !$cajaId) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Debes tener un turno de caja abierto para registrar el costo de la mercancía con saldo de caja.');
            }

            // Preparar payload para el servicio de inventario
            $data = array_merge($validated, [
                'unit_price'     => $unitPrice,
                'total'          => $totalCost,
                'image'          => $request->file('image'),
                'caja_id'        => $pagarConCaja ? $cajaId : null,
                'payment_method' => $request->input('payment_method', 'cash'),
            ]);

            $inventoryService->processMovement(
                $data,
                auth()->id()
            );

            return redirect()->route('stock.index')
                ->with('success', 'Movimiento de inventario y costo registrados correctamente.');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}