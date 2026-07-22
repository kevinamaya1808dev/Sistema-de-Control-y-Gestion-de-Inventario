<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    // Cambiar a true para permitir que cualquier usuario conectado use esta validación
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|string|unique:products,sku|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'talla' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0', // Evita precios negativos
            'stock' => 'required|integer|min:0', // Evita stock negativo
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    // Mensajes personalizados opcionales para que se vea más profesional
    public function messages(): array
    {
        return [
            'sku.unique' => 'Este SKU ya está registrado en el inventario.',
            'price.min' => 'El precio no puede ser un número negativo.',
            'stock.min' => 'El stock inicial de productos no puede ser un número negativo.',
            'category_id.exists' => 'La categoría seleccionada no es válida.',
        ];
    }
}
