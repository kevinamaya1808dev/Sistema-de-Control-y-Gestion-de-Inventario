<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Mostrar la lista de categorías.
     */
    public function index()
    {
        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    /**
     * Mostrar el formulario para crear una nueva categoría.
     * Nota: No se usa directamente si manejas todo por modales, pero se deja por compatibilidad.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Guardar una nueva categoría en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        Category::create($request->only(['name', 'description']));

        return redirect()->route('categories.index')
            ->with('success', 'Categoría creada con éxito.');
    }

    /**
     * Mostrar una categoría específica.
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Mostrar el formulario para editar una categoría existente.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Actualizar la categoría en la base de datos.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($request->only(['name', 'description']));

        return redirect()->route('categories.index')
            ->with('success', 'Categoría actualizada con éxito.');
    }

    /**
     * Eliminar una categoría de la base de datos.
     */
    public function destroy(Category $category)
    {
        // Validación de seguridad por si tiene productos asociados
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoría eliminada con éxito.');
    }
}
