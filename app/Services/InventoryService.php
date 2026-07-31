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
        $isVentaDirecta = ($data['type'] === 'salida' && ($data['reason'] ?? '') === 'Venta directa');
        $cajaActivaId = $data['caja_id'] ?? null;

        // Validación de caja abierta obligatoria solo en Venta Directa
        if ($isVentaDirecta) {
            $caja = CajaMovimiento::where('user_id', $userId)
                ->where('estado', 'abierta')
                ->first();

            if (!$caja) {
                throw new Exception('Debes abrir un turno de caja antes de realizar una Venta Directa.');
            }

            $cajaActivaId = $caja->id;

            $totalVenta = $data['quantity'] * ($data['precio_unitario'] ?? $data['unit_price'] ?? 0);
            if (($data['monto_recibido'] ?? 0) < $totalVenta) {
                throw new Exception('El dinero recibido es menor al total a cobrar.');
            }
        }

        return DB::transaction(function () use ($data, $userId, $cajaActivaId, $isVentaDirecta) {
            $cantidad = (int) $data['quantity'];
            $talla = $data['talla'] ?? 'Única';

            // Lock para prevenir condiciones de carrera al restar/sumar stock por talla
            $productSize = ProductSize::where('product_id', $data['product_id'])
                ->where('talla', $talla)
                ->lockForUpdate()
                ->first();

            if (!$productSize) {
                $productSize = ProductSize::create([
                    'product_id' => $data['product_id'],
                    'talla' => $talla,
                    'stock' => 0,
                ]);
            }

            if ($data['type'] === 'salida') {
                if ($productSize->stock < $cantidad) {
                    throw new Exception("Stock insuficiente para la talla {$talla}. Stock actual: {$productSize->stock} pzas.");
                }
                $productSize->decrement('stock', $cantidad);
            } else {
                $productSize->increment('stock', $cantidad);
            }

            // Sincronizar stock total del producto
            $totalStockGlobal = ProductSize::where('product_id', $data['product_id'])->sum('stock');
            Product::where('id', $data['product_id'])->update(['stock' => $totalStockGlobal]);

            // Finanzas y Costos
            $precioUnitario = (float) ($data['precio_unitario'] ?? $data['unit_price'] ?? $data['cost'] ?? 0);
            $totalCalculado = (float) ($data['total'] ?? ($cantidad * $precioUnitario));
            
            $montoRecibido = (float) ($data['monto_recibido'] ?? 0);
            $cambioEntregado = ($isVentaDirecta && $montoRecibido > 0) ? ($montoRecibido - $totalCalculado) : 0;

            // Imagen comprobante
            $imagePath = null;
            if (isset($data['image']) && $data['image']) {
                $imagePath = is_string($data['image']) 
                    ? $data['image'] 
                    : $data['image']->store('movements', 'public');
            }

            return InventoryMovement::create([
                'product_id' => $data['product_id'],
                'user_id' => $userId,
                'caja_id' => $cajaActivaId,
                'type' => $data['type'],
                'quantity' => $cantidad,
                'reason' => "{$data['reason']} (Talla: {$talla})",
                'unit_price' => $precioUnitario,
                'total' => $totalCalculado,
                'monto_recibido' => $montoRecibido,
                'cambio' => $cambioEntregado,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'image' => $imagePath,
                'date' => now(),
            ]);
        });
    }
}