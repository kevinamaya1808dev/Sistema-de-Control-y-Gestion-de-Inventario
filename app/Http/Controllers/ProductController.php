<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Mostra el catálogo de productos con sus categorías.
     */
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Mostrar formulario de creación cargando las categorías disponibles.
     */
    public function create()
    {
        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    /**
     * Guardar un nuevo producto con su imagen utilizando StoreProductRequest.
     */
    public function store(StoreProductRequest $request)
    {
        // Obtenemos únicamente los datos que ya pasaron la validación
        $data = $request->validated();

        // Lógica para subir la imagen si el usuario la cargó
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Producto registrado correctamente.');
    }

    /**
     * Mostrar un producto específico.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Formulario de edición con el producto y las categorías para el select.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualizar producto y reemplazar imagen utilizando UpdateProductRequest.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        // Obtenemos los datos ya validados
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Borramos la imagen anterior del disco si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Subimos la nueva
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    /**
     * Eliminar producto y su imagen del almacenamiento.
     */
    public function destroy(Product $product)
    {
        // Si el producto tiene imagen asignada, la borramos del servidor
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
