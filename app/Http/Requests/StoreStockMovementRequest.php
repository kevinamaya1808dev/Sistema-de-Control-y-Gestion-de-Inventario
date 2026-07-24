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
            'talla' => ['required', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'type' => ['required', 'in:entrada,salida'],
            'reason' => ['required', 'string'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'monto_recibido' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}
