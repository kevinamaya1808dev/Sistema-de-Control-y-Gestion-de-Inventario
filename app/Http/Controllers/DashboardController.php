<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Si el usuario es Operador, lo mandamos a su panel específico
        if ($user->role && $user->role->name === 'Operador') {
            return view('dashboard-operator'); // O la vista que uses para el operador
        }

        // --- MÉTRICAS EXCLUSIVAS PARA EL ADMINISTRADOR ---
        $totalProducts = Product::count();

        // Valor total del almacén (Suma de precio * stock)
        $inventoryValue = Product::all()->sum(function ($product) {
            return $product->price * $product->stock;
        });

        // Productos con bajo stock (Menor o igual a 5 piezas)
        $lowStockCount = Product::where('stock', '<=', 5)->count();

        return view('dashboard', compact(
            'totalProducts',
            'inventoryValue',
            'lowStockCount'
        ));
    }
}