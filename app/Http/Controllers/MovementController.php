<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovementController extends Controller
{
    public function index()
    {
        $products = Product::select('id', 'name', 'sku')->orderBy('name')->get();

        $motives = [
            'Reabastecimiento proveedor',
            'Solicitud Dpto. Finanzas',
            'Mantenimiento',
            'Consumo interno',
            'Devolución',
        ];

        // Si el usuario es administrador, mostramos movimientos globales; si no, sólo los del propio usuario
        if (Auth::user() && Auth::user()->isAdmin()) {
            $movements = InventoryMovement::with(['product', 'user'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        } else {
            $movements = InventoryMovement::with(['product', 'user'])
                ->where('user_id', Auth::id())
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('movements.index', compact('products', 'motives', 'movements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entrada,salida',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($data['type'] === 'salida' && $product->stock < $data['quantity']) {
            return redirect()->route('movements.index')->with('error', 'Stock insuficiente para realizar la salida.');
        }

        // Actualizar stock
        if ($data['type'] === 'entrada') {
            $product->increment('stock', $data['quantity']);
        } else {
            $product->decrement('stock', $data['quantity']);
        }

        InventoryMovement::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => $data['type'],
            'quantity' => $data['quantity'],
            'date' => now()->toDateString(),
            'reason' => $data['reason'] ?? null,
        ]);

        return redirect()->route('movements.index')->with('success', 'Movimiento registrado correctamente.');
    }
}
