<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'talla',
        'description',
        'image',
        'price',
        'stock',
        'category_id',
    ];

    // Relación: El producto pertenece a una categoría
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relación: Un producto tiene muchos movimientos de inventario
    public function movements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }
}