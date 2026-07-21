@extends('layouts.app')

@section('title', 'SCGI - Productos')
@section('header_title', 'Productos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="productManagement()">

    <!-- ENCABEZADO PRINCIPAL -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 pb-6 border-b border-slate-100 dark:border-slate-800/80">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Productos
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500 mt-1 font-semibold tracking-wide">
                Catálogo completo de productos e inventario global en tiempo real.
            </p>
        </div>
        
        <button @click="openCreateModal()" 
                class="group relative px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 transition-all flex items-center gap-2.5 cursor-pointer transform hover:-translate-y-0.5 active:translate-y-0">
            <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span class="uppercase tracking-wider">Registrar Producto</span>
        </button>
    </div>

    <!-- CONTENEDOR DE LA TABLA -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/70 dark:border-slate-800/80 overflow-hidden">
        
        <div class="px-7 py-5 bg-slate-50/50 dark:bg-slate-950/40 border-b border-slate-100 dark:border-slate-800/80">
            <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Existencias Activas</h2>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-bold mt-0.5">Listado maestro en tiempo real</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 dark:bg-slate-950 text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="py-4.5 px-7 w-28">ID</th>
                        <th class="py-4.5 px-6 w-24">Imagen</th>
                        <th class="py-4.5 px-6">Producto</th>
                        <th class="py-4.5 px-6 w-48">Categoría</th>
                        <th class="py-4.5 px-6 w-32">Precio</th>
                        <th class="py-4.5 px-6 w-28">Stock</th>
                        <th class="py-4.5 px-7 text-right w-40">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300">
                    @forelse($products as $product)
                    <tr class="hover:bg-indigo-50/20 dark:hover:bg-slate-800/40 transition-colors duration-200">
                        <td class="py-5 px-7 font-bold text-slate-400 dark:text-slate-500 tabular-nums">#{{ $product->id }}</td>
                        
                        <td class="py-5 px-6">
                            <div class="w-11 h-11 rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 shadow-xs flex items-center justify-center">
                                @if($product->image)
                                    <!-- Usando la ruta nombrada del controlador para saltar restricciones y evitar 403 -->
                                    <img src="{{ route('products.image', $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                @else
                                    <span class="text-slate-300 dark:text-slate-700 text-[9px] font-black tracking-wider uppercase">N/A</span>
                                @endif
                            </div>
                        </td>
                        
                        <td class="py-5 px-6 font-black text-slate-900 dark:text-white tracking-wide text-xs sm:text-sm">{{ $product->name }}</td>
                        
                        <td class="py-5 px-6">
                            <span class="font-black px-2.5 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700 rounded-lg text-[11px] tracking-wide inline-block text-slate-800 dark:text-slate-200">
                                {{ $product->category->name ?? 'Sin Categoría' }}
                            </span>
                        </td>
                        
                        <td class="py-5 px-6 font-black text-indigo-600 dark:text-indigo-400 tabular-nums">${{ number_format($product->price, 2) }}</td>
                        
                        <td class="py-5 px-6 font-bold text-slate-700 dark:text-slate-400 tabular-nums">{{ $product->stock }} u.</td>
                        
                        <td class="py-5 px-7 text-right space-x-1.5 whitespace-nowrap">
                            <button @click="openEditModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->category_id ?? '' }}', {{ $product->price }}, {{ $product->stock }}, '{{ $product->sku ?? '' }}')" 
                                    class="p-2.5 text-amber-500 hover:text-amber-600 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center shadow-xs" title="Modificar Producto">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>

                            <button @click="openDeleteModal({{ $product->id }})" 
                                    class="p-2.5 text-rose-500 hover:text-rose-600 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center shadow-xs" title="Remover del Servidor">
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M9 3h6M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-14 text-center text-slate-400 dark:text-slate-600 font-bold tracking-wide">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="p-4 bg-slate-100 dark:bg-slate-950 rounded-2xl text-slate-300 dark:text-slate-700">
                                    <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5M14 10h1"/></svg>
                                </div>
                                <span class="text-xs uppercase tracking-wider">No hay productos registrados en la base de datos.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: CREAR REGISTRO -->
    <div x-show="modals.create" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4" x-transition style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200/40 dark:border-slate-800" @click.outside="closeModal('create')">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/20">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Crear Nuevo Producto
                </h3>
                <button type="button" @click="closeModal('create')" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center cursor-pointer font-bold">✕</button>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Nombre del Producto</label>
                        <input type="text" name="name" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:border-slate-800 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-xs font-bold focus:outline-hidden" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Categoría</label>
                        <select name="category_id" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:border-slate-800 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-xs font-bold focus:outline-hidden cursor-pointer" required>
                            <option value="">Selecciona una categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Precio Venta</label>
                            <input type="number" step="0.01" name="price" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Stock Inicial</label>
                            <input type="number" name="stock" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Código SKU</label>
                            <input type="text" name="sku" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden uppercase" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Imagen (Opcional)</label>
                        <div onclick="document.getElementById('file-upload').click()" class="border-2 border-dashed border-slate-200 hover:border-indigo-500 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 rounded-2xl w-32 h-32 flex flex-col items-center justify-center cursor-pointer relative overflow-hidden transition-colors group shadow-xs">
                            <img id="image-preview" class="absolute inset-0 w-full h-full object-cover hidden z-10">
                            <div id="upload-prompt" class="text-center p-2">
                                <svg class="mx-auto h-6 w-6 text-slate-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Subir Img</p>
                            </div>
                            <input id="file-upload" type="file" name="image" accept="image/*" class="hidden" @change="previewFile('create')">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" @click="closeModal('create')" class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-xs font-bold rounded-xl shadow-md">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR REGISTRO -->
    <div x-show="modals.edit" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4" x-transition style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200/40 dark:border-slate-800" @click.outside="closeModal('edit')">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/20">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Modificar Producto Existente
                </h3>
                <button type="button" @click="closeModal('edit')" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center cursor-pointer font-bold">✕</button>
            </div>
            <form :action="'{{ route('products.index') }}/' + formEdit.id" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Nombre del Producto</label>
                        <input type="text" x-model="formEdit.name" name="name" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Categoría</label>
                        <select x-model="formEdit.category_id" name="category_id" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden cursor-pointer" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Precio Venta</label>
                            <input type="number" step="0.01" x-model="formEdit.price" name="price" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Stock</label>
                            <input type="number" x-model="formEdit.stock" name="stock" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden" required>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Código SKU</label>
                            <input type="text" x-model="formEdit.sku" name="sku" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:text-white text-xs font-bold focus:outline-hidden uppercase" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Reemplazar Imagen (Opcional)</label>
                        <div onclick="document.getElementById('file-upload-edit').click()" class="border-2 border-dashed border-slate-200 hover:border-indigo-500 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 rounded-2xl w-32 h-32 flex flex-col items-center justify-center cursor-pointer relative overflow-hidden transition-colors group shadow-xs">
                            <img id="image-preview-edit" class="absolute inset-0 w-full h-full object-cover hidden z-10">
                            <div id="upload-prompt-edit" class="text-center p-2">
                                <svg class="mx-auto h-6 w-6 text-slate-400 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Nueva Img</p>
                            </div>
                            <input id="file-upload-edit" type="file" name="image" accept="image/*" class="hidden" @change="previewFile('edit')">
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" @click="closeModal('edit')" class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 text-white text-xs font-bold rounded-xl shadow-md">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DE CONFIRMACIÓN DE DESTRUCCIÓN -->
    <div x-show="modals.delete" class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4" x-transition style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6 border border-slate-200/40 dark:border-slate-800" @click.outside="closeModal('delete')">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4 border border-rose-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-sm font-black text-slate-900 dark:text-white mb-1.5 uppercase tracking-wide">¿Confirmar Destrucción?</h3>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mb-6 font-bold px-2 leading-relaxed">
                Esta acción eliminará de forma irreversible el producto de las existencias operativas del servidor.
            </p>
            <form :action="'{{ route('products.index') }}/' + formDelete.id" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="closeModal('delete')" class="w-1/2 py-3 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 font-black text-xs cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Abortar</button>
                <button type="submit" class="w-1/2 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl cursor-pointer transition-all shadow-md shadow-rose-600/10">Eliminar</button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/components/product-management.js') }}"></script>
@endpush