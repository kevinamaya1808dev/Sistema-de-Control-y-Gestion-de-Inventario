@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-lg shadow-sm flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-800 p-4 rounded-lg shadow-sm flex items-center space-x-2">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Formulario Producto -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-fit">
            <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center space-x-2">
                <i data-lucide="plus-circle" class="text-indigo-600"></i>
                <span>Nuevo Producto</span>
            </h2>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">SKU / Código</label>
                        <input type="text" name="sku" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm" placeholder="Ej. PROD-101" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Nombre</label>
                        <input type="text" name="name" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm" placeholder="Ej. Martillo" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Categoría</label>
                        <select name="category_id" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm bg-white" required>
                            <option value="">Selecciona una opción</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Precio ($)</label>
                            <input type="number" step="0.01" name="price" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm" placeholder="150.00" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Stock Inicial</label>
                            <input type="number" name="stock" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm" placeholder="10" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Imagen del producto</label>
                        <input type="file" name="image" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/20 transition-all text-sm">
                        Registrar Producto
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla Inventario -->
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Inventario de Productos</h2>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full">
                    {{ count($products) }} Total
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
                            <th class="p-4 pl-6">Producto</th>
                            <th class="p-4">SKU</th>
                            <th class="p-4">Categoría</th>
                            <th class="p-4">Precio</th>
                            <th class="p-4 text-center">Existencias</th>
                            <th class="p-4 text-right pr-6">Acción de Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 pl-6 flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center border border-gray-100">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" class="object-cover w-full h-full">
                                        @else
                                            <i data-lucide="image" class="w-5 h-5 text-gray-400"></i>
                                        @endif
                                    </div>
                                    <span class="font-semibold text-gray-950">{{ $product->name }}</span>
                                </td>
                                <td class="p-4 font-mono text-gray-500 text-xs">{{ $product->sku }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="p-4 font-semibold text-gray-900">${{ number_format($product->price, 2) }}</td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 font-bold text-xs rounded-full {{ $product->stock < 5 ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ $product->stock }} pzas
                                    </span>
                                </td>
                                <td class="p-4 text-right pr-6">
                                    <form action="{{ route('products.updateStock', $product->id) }}" method="POST" class="inline-flex items-center space-x-1.5">
                                        @csrf
                                        <input type="number" name="quantity" min="1" value="1" class="w-12 text-center border border-gray-200 rounded-lg py-1 text-xs outline-none focus:border-indigo-500">
                                        <button type="submit" name="type" value="entrada" class="p-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg transition-colors" title="Registrar Entrada">
                                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <button type="submit" name="type" value="salida" class="p-1.5 bg-rose-500 hover:bg-rose-600 text-white rounded-lg transition-colors" title="Registrar Salida">
                                            <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-gray-400">
                                    <i data-lucide="package-open" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                                    <p>Tu inventario está vacío. ¡Agrega tu primer producto a la izquierda!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection