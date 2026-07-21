<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 🛑 CONTROL DE ACCESO ABSOLUTO:
        // Si es Operador (por rol) o NO tiene el permiso para ver métricas/reportes,
        // lo redirigimos de inmediato al Control de Caja.
        $esAdmin = ($user->id === 1 || $user->isAdmin());
        $esOperador = ($user->role && $user->role->name === 'Operador');

        if ($esOperador || (! $esAdmin && ! $user->hasPermission('view-reports'))) {
            return redirect()->route('caja.index')->with('info', 'Acceso al Dashboard restringido para el rol de Operador.');
        }

        // --- MÉTRICAS REALES DESDE LA BASE DE DATOS (SOLO ADMINISTRADOR) ---

        // A. Total de productos registrados en el catálogo
        $totalProductsCount = Product::count();

        // B. Total de SKUs únicos y activos
        $activeSkusCount = Product::whereNotNull('sku')
            ->where('sku', '!=', '')
            ->distinct('sku')
            ->count();

        // C. Valor económico total del almacén (Optimizado directamente en BD o por colección)
        $totalInventoryValue = Product::all()->sum(function ($product) {
            return $product->price * $product->stock;
        });

        // D. Colección de productos con Stock Crítico (Menor o igual a 5 piezas)
        $lowStockProducts = Product::where('stock', '<=', 5)->get();

        // E. Actividad Reciente del Inventario
        $recentActivities = InventoryMovement::with(['product', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalProductsCount',
            'activeSkusCount',
            'totalInventoryValue',
            'lowStockProducts',
            'recentActivities'
        ));
    }
}
