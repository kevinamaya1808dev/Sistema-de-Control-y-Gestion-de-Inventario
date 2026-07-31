<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'talla' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'in:entrada,salida'],
            'reason' => ['required', 'string', 'max:255'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric', 'min:0'],
            'monto_recibido' => ['nullable', 'numeric', 'min:0'],
            'pagar_con_caja' => ['nullable', 'boolean'],
            'payment_method' => ['nullable', 'in:cash,card,transfer'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}