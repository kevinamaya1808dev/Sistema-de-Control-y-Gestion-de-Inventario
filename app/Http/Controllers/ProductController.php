<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Muestra el catálogo de productos con sus categorías y tallas.
     */
    public function index()
    {
        // 'sizes' asume la relación en el modelo Product (hasMany(ProductSize::class, 'product_id'))
        // Si tu relación en el modelo se llama 'tallas', cambia 'sizes' por 'tallas'
        $products = Product::with(['category', 'sizes'])->latest()->get();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Método para servir la imagen del producto de forma segura.
     */
    public function showImage($path)
    {
        $cleanPath = str_replace('products/', '', $path);
        $fullPath = 'products/'.$cleanPath;

        if (! Storage::disk('public')->exists($fullPath)) {
            abort(404);
        }

        return response()->file(storage_path('app/public/'.$fullPath));
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
     * Guardar un nuevo producto con su imagen y sus tallas/stock correspondientes.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($request, &$data) {
                // 1. Manejo de la imagen si se adjunta
                if ($request->hasFile('image')) {
                    $data['image'] = $request->file('image')->store('products', 'public');
                }

                $stockTotal = 0;
                $hasSizes = false;

                // 2. Creamos el producto principal (inicialmente con el stock que venga o 0)
                $product = Product::create($data);

                // 3. Guardamos las tallas y sumamos su stock si vienen en el request
                if ($request->has('tallas') && is_array($request->tallas)) {
                    foreach ($request->tallas as $tallaData) {
                        if (! empty($tallaData['talla']) && isset($tallaData['stock']) && (int) $tallaData['stock'] > 0) {
                            $hasSizes = true;
                            $stockTalla = (int) $tallaData['stock'];

                            ProductSize::create([
                                'product_id' => $product->id,
                                'talla' => $tallaData['talla'],
                                'stock' => $stockTalla,
                            ]);

                            $stockTotal += $stockTalla;
                        }
                    }
                }

                // 4. Si se definieron tallas, el stock general pasa a ser la suma exacta de las tallas
                if ($hasSizes) {
                    $product->update(['stock' => $stockTotal]);
                }
            });

            return redirect()->route('products.index')
                ->with('success', 'Producto y tallas registrados correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al registrar el producto: '.$e->getMessage());
        }
    }

    /**
     * Mostrar un producto específico.
     */
    public function show(Product $product)
    {
        $product->load('sizes');

        return view('products.show', compact('product'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('sizes');

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Actualizar producto, sincronizar tallas y reemplazar imagen.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($request, $product, &$data) {
                // 1. Manejo de la nueva imagen
                if ($request->hasFile('image')) {
                    if ($product->image) {
                        $oldPath = str_replace(['storage/', 'public/'], '', $product->image);
                        Storage::disk('public')->delete($oldPath);
                    }
                    $data['image'] = $request->file('image')->store('products', 'public');
                } else {
                    unset($data['image']);
                }

                // 2. Actualizar datos base del producto
                $product->update($data);

                // 3. Procesar y actualizar la distribución por tallas
                if ($request->has('tallas') && is_array($request->tallas)) {
                    // Reemplazamos las tallas anteriores con la nueva estructura
                    $product->sizes()->delete();

                    $stockTotal = 0;
                    $hasSizes = false;

                    foreach ($request->tallas as $tallaData) {
                        if (! empty($tallaData['talla']) && isset($tallaData['stock']) && (int) $tallaData['stock'] > 0) {
                            $hasSizes = true;
                            $stockTalla = (int) $tallaData['stock'];

                            ProductSize::create([
                                'product_id' => $product->id,
                                'talla' => $tallaData['talla'],
                                'stock' => $stockTalla,
                            ]);

                            $stockTotal += $stockTalla;
                        }
                    }

                    // Sincronizar el stock del producto con el total actualizado
                    if ($hasSizes) {
                        $product->update(['stock' => $stockTotal]);
                    }
                }
            });

            return redirect()->route('products.index')
                ->with('success', 'Producto y tallas actualizados correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar: '.$e->getMessage());
        }
    }

    /**
     * Eliminar producto y su imagen del almacenamiento.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            $oldPath = str_replace(['storage/', 'public/'], '', $product->image);
            Storage::disk('public')->delete($oldPath);
        }

        // Eliminar registros de tallas dependientes
        $product->sizes()->delete();
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
