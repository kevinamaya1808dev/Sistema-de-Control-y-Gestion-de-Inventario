<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Si el usuario es Operador, mostrar su dashboard simplificado (ventas/perdidas)
        if ($user->role && $user->role->name === 'Operador') {
            $recentOut = InventoryMovement::with(['product'])
                ->where('user_id', $user->id)
                ->where('type', 'salida')
                ->orderByDesc('created_at')
                ->limit(12)
                ->get();

            return view('dashboard-operator', compact('recentOut', 'user'));
        }

        // Datos comunes para administrador
        $totalProducts = Product::count();
        $inventoryValue = Product::all()->sum(function ($product) {
            return $product->price * $product->stock;
        });

        // --- MÉTRICAS EXCLUSIVAS PARA EL ADMINISTRADOR ---
        $totalProducts = Product::count();

        // Valor total del almacén (Suma de precio * stock)
        $inventoryValue = Product::all()->sum(function ($product) {
            return $product->price * $product->stock;
        });

        // Productos con bajo stock (Menor o igual a 5 piezas)
        $lowStockCount = Product::where('stock', '<=', 5)->count();

        // Movimientos recientes para la auditoría
        $recentMovements = InventoryMovement::with(['product', 'user'])->orderByDesc('created_at')->limit(8)->get();

        return view('dashboard', compact(
            'totalProducts',
            'inventoryValue',
            'lowStockCount',
            'recentMovements'
        ));
    }
}