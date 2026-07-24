<?php

namespace App\Services;

use App\Models\CajaMovimiento;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductSize;
use Exception;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function processMovement(array $data, int $userId): InventoryMovement
    {
        $isVentaDirecta = ($data['type'] === 'salida' && $data['reason'] === 'Venta directa');
        $cajaActiva = null;

        if ($isVentaDirecta) {
            $cajaActiva = CajaMovimiento::where('user_id', $userId)
                ->where('estado', 'abierta')
                ->first();

            if (! $cajaActiva) {
                throw new Exception('Debes abrir un turno de caja antes de realizar una Venta Directa.');
            }

            $totalVenta = $data['quantity'] * ($data['precio_unitario'] ?? 0);
            if (($data['monto_recibido'] ?? 0) < $totalVenta) {
                throw new Exception('El dinero recibido es menor al total a cobrar.');
            }
        }

        return DB::transaction(function () use ($data, $userId, $cajaActiva, $isVentaDirecta) {
            // Lock para prevenir condiciones de carrera al restar stock
            $productSize = ProductSize::where('product_id', $data['product_id'])
                ->where('talla', $data['talla'])
                ->lockForUpdate()
                ->first();

            if (! $productSize) {
                $productSize = ProductSize::create([
                    'product_id' => $data['product_id'],
                    'talla' => $data['talla'],
                    'stock' => 0,
                ]);
            }

            $cantidad = (int) $data['quantity'];

            if ($data['type'] === 'salida') {
                if ($productSize->stock < $cantidad) {
                    throw new Exception("Stock insuficiente para la talla {$data['talla']}. Stock actual: {$productSize->stock} pzas.");
                }
                $productSize->decrement('stock', $cantidad);
            } else {
                $productSize->increment('stock', $cantidad);
            }

            // Sincronizar stock total del producto
            $totalStockGlobal = ProductSize::where('product_id', $data['product_id'])->sum('stock');
            Product::where('id', $data['product_id'])->update(['stock' => $totalStockGlobal]);

            // Finanzas
            $precioUnitario = $data['precio_unitario'] ?? 0;
            $totalCalculado = $isVentaDirecta ? ($cantidad * $precioUnitario) : 0;
            $montoRecibido = $data['monto_recibido'] ?? 0;
            $cambioEntregado = $montoRecibido > 0 ? ($montoRecibido - $totalCalculado) : 0;

            // Imagen comprobante
            $imagePath = isset($data['image']) && $data['image']
                ? $data['image']->store('movements', 'public')
                : null;

            return InventoryMovement::create([
                'product_id' => $data['product_id'],
                'user_id' => $userId,
                'caja_id' => $cajaActiva?->id,
                'type' => $data['type'],
                'quantity' => $cantidad,
                'reason' => "{$data['reason']} (Talla: {$data['talla']})",
                'unit_price' => $precioUnitario,
                'total' => $totalCalculado,
                'monto_recibido' => $montoRecibido,
                'cambio' => $cambioEntregado,
                'image' => $imagePath,
                'date' => now(),
            ]);
        });
    }
}
