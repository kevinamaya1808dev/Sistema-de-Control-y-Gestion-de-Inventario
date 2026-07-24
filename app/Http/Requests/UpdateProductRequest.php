<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID de forma segura sin importar si viene como Modelo o como String/ID
        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : $product;

        return [
            'sku' => 'required|string|max:255|unique:products,sku,'.$productId,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'talla' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'nullable|integer|min:0', // <--- Se cambia a nullable porque no viene en el modal de edición
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'sku.unique' => 'Este SKU ya pertenece a otro producto.',
            'price.min' => 'El precio no puede ser negativo.',
            'stock.min' => 'El stock disponible no puede ser negativo.',
        ];
    }
}
