<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'date',
    ];

    // El campo de fecha lo casteamos para que Laravel lo trate como objeto Carbon/Date
    protected $casts = [
        'date' => 'date',
    ];

    // Relación: El movimiento pertenece a un producto
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relación: El movimiento fue registrado por un usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
