<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Services\InventoryService;
use Exception;
use Illuminate\Support\Facades\Gate;

class StockController extends Controller
{
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
            'products', // <-- Agregado para que no truene la vista
            'movements',
            'totalMovements',
            'totalEntradas',
            'totalSalidas'
        ));
    }

    public function store(StoreStockMovementRequest $request, InventoryService $inventoryService)
    {
        try {
            $inventoryService->processMovement(
                array_merge($request->validated(), ['image' => $request->file('image')]),
                auth()->id()
            );

            return redirect()->route('stock.index')
                ->with('success', 'Movimiento registrado y stock por talla actualizado correctamente.');

        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
