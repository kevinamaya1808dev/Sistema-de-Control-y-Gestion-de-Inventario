@extends('layouts.app')

@section('title', 'SCGI - Productos')

@section('header_title', 'Productos')

@section('content')
<x-app-container>
<div class="max-w-7xl mx-auto space-y-6 w-full min-w-0 max-w-full overflow-x-hidden" x-data="productManagement()">
    {{-- ENCABEZADO PRINCIPAL --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-200/80 dark:border-slate-800/80">
        <div class="space-y-1">
            <h1 class="page-title text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Productos</h1>
            <p class="page-subtitle text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">Catálogo completo de productos e inventario global en tiempo real.</p>
        </div>
        <button @click="openCreateModal()" class="btn-primary w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Registrar Producto</span>
        </button>
    </div>

    {{-- CONTENEDOR MAESTRO --}}
    <div class="table-container w-full min-w-0 max-w-full overflow-hidden bg-white dark:bg-[#0b0f19] border border-slate-200/80 dark:border-slate-800/80 rounded-2xl shadow-sm">
        <div class="px-5 py-4 sm:px-6 sm:py-5 bg-slate-50/80 dark:bg-[#070a11]/90 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Existencias Activas</h2>
                <p class="text-[10px] sm:text-[11px] text-slate-500 dark:text-slate-400 font-bold mt-0.5">Listado maestro en tiempo real</p>
            </div>
        </div>

        {{-- VISTA MÓVIL --}}
        <div class="block md:hidden p-3 space-y-3 bg-slate-50/50 dark:bg-[#080c16]/50 w-full min-w-0 box-border">
            @forelse($products as $product)
            <div class="p-4 rounded-2xl bg-white dark:bg-[#0d121f] border border-slate-200/80 dark:border-slate-800/80 space-y-3 shadow-sm hover:shadow-md transition-shadow w-full min-w-0 box-border" x-data="{ openSizes: false }">
                <div class="flex items-start justify-between gap-2 w-full min-w-0">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-12 h-12 rounded-xl overflow-hidden border border-slate-200/80 dark:border-slate-800/80 bg-slate-100 dark:bg-slate-900 flex items-center justify-center shrink-0 shadow-inner">
                            @if($product->image)
                                <img src="{{ route('products.image', $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                            @else
                                <span class="text-slate-400 dark:text-slate-600 text-[9px] font-black uppercase">N/A</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-black text-slate-900 dark:text-white text-sm truncate tracking-wide leading-tight">{{ $product->name }}</h3>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono truncate mt-0.5">SKU: {{ $product->sku ?? 'N/A' }}</p>
                            <span class="mt-1 font-bold px-2 py-0.5 bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-lg text-[9px] text-slate-700 dark:text-slate-300 inline-block truncate max-w-full">
                                {{ $product->category->name ?? 'Sin Categoría' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <button @click="openEditModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->category_id ?? '' }}', {{ $product->price }}, {{ $product->stock }}, '{{ $product->sku ?? '' }}', '{{ $product->image ? route('products.image', $product->image) : '' }}')"
                            class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/10 active:bg-indigo-500/20 rounded-xl transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                        <button @click="openDeleteModal({{ $product->id }})" class="p-2 text-rose-500 hover:bg-rose-500/10 active:bg-rose-500/20 rounded-xl transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M9 3h6M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Solo tallas con stock > 0 --}}
                @if($product->sizes->where('stock', '>', 0)->count() > 0)
                <div class="pt-2.5 border-t border-slate-100 dark:border-slate-800/80 w-full">
                    <button @click="openSizes = !openSizes" type="button" class="w-full flex items-center justify-between px-3 py-2 bg-indigo-500/10 hover:bg-indigo-500/15 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-xl text-xs font-bold transition-all cursor-pointer">
                        <span>Ver {{ $product->sizes->where('stock', '>', 0)->count() }} Tallas Disponibles</span>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-slate-400 dark:text-slate-500">({{ $product->stock }} u.)</span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': openSizes }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>
                    <div x-show="openSizes" x-transition class="mt-2 grid grid-cols-2 gap-1.5 p-2 bg-slate-900/60 rounded-xl border border-slate-800">
                        @foreach($product->sizes->where('stock', '>', 0) as $size)
                        <div class="flex justify-between items-center px-2.5 py-1.5 bg-slate-800/50 rounded-lg text-[11px]">
                            <span class="font-bold text-slate-300">Talla {{ $size->talla }}</span>
                            <span class="font-black text-indigo-400 tabular-nums">{{ $size->stock }} u.</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-between pt-2.5 border-t border-slate-100 dark:border-slate-800/80 text-xs w-full">
                    <div>
                        <span class="text-slate-400 text-[9px] uppercase font-extrabold block">Precio</span>
                        <span class="font-black text-slate-900 dark:text-white tabular-nums text-sm">${{ number_format($product->price, 2) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 text-[9px] uppercase font-extrabold block">Stock Total</span>
                        <span class="font-black text-indigo-600 dark:text-indigo-400 tabular-nums text-sm">{{ $product->stock }} u.</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-400 dark:text-slate-600 font-bold uppercase text-xs tracking-wider">No hay productos registrados.</div>
            @endforelse
        </div>

        {{-- VISTA DESKTOP --}}
        <div class="hidden md:block overflow-x-auto w-full">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 dark:bg-[#080c16]/50 border-b border-slate-200/80 dark:border-slate-800/80 text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                        <th class="py-3.5 px-4 w-14">ID</th>
                        <th class="py-3.5 px-4 w-14">Img</th>
                        <th class="py-3.5 px-4">Producto / SKU</th>
                        <th class="py-3.5 px-4 w-32">Categoría</th>
                        <th class="py-3.5 px-4 w-44">Tallas</th>
                        <th class="py-3.5 px-4 w-28">Precio</th>
                        <th class="py-3.5 px-4 w-24">Total</th>
                        <th class="py-3.5 px-4 text-right w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($products as $product)
                    <tr class="hover:bg-indigo-50/40 dark:hover:bg-indigo-500/5 transition-colors group">
                        <td class="py-3.5 px-4 font-bold text-slate-400 dark:text-slate-500 text-xs tabular-nums">#{{ $product->id }}</td>
                        <td class="py-3.5 px-4">
                            <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-200/80 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 flex items-center justify-center shrink-0 shadow-inner">
                                @if($product->image)
                                    <img src="{{ route('products.image', $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                @else
                                    <span class="text-slate-400 dark:text-slate-600 text-[9px] font-black uppercase">N/A</span>
                                @endif
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-black text-slate-900 dark:text-white text-xs sm:text-sm tracking-wide">{{ $product->name }}</div>
                            <div class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5">SKU: {{ $product->sku ?? 'N/A' }}</div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold px-2.5 py-1 bg-slate-100 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-700/60 rounded-xl text-[10px] text-slate-800 dark:text-slate-200 inline-block shadow-2xs">
                                {{ $product->category->name ?? 'Sin Categoría' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            {{-- Solo click, se mantiene abierto hasta click fuera. Solo tallas con stock > 0 --}}
                            @if($product->sizes->where('stock', '>', 0)->count() > 0)
                            <div class="relative inline-block" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 text-indigo-600 dark:text-indigo-400 rounded-xl text-xs font-bold transition-all cursor-pointer">
                                    <span>{{ $product->sizes->where('stock', '>', 0)->count() }} Tallas</span>
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500">({{ $product->stock }} u.)</span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="open" x-transition class="absolute left-0 top-full mt-1.5 z-50 w-48 p-2.5 bg-[#0d121f] border border-slate-800 rounded-2xl shadow-xl space-y-1 text-xs">
                                    <p class="text-[10px] font-black uppercase text-slate-400 pb-1.5 border-b border-slate-800">Desglose por Talla</p>
                                    <div class="grid grid-cols-1 gap-1 pt-1 max-h-36 overflow-y-auto">
                                        @foreach($product->sizes->where('stock', '>', 0) as $size)
                                        <div class="flex justify-between items-center px-2 py-1 bg-slate-800/50 rounded-lg text-[11px]">
                                            <span class="font-bold text-slate-300">Talla {{ $size->talla }}</span>
                                            <span class="font-black text-indigo-400 tabular-nums">{{ $size->stock }} u.</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-xs text-slate-400 dark:text-slate-600 font-semibold italic">Sin tallas</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 font-black text-slate-900 dark:text-white tabular-nums text-xs sm:text-sm">${{ number_format($product->price, 2) }}</td>
                        <td class="py-3.5 px-4 font-bold text-slate-700 dark:text-slate-400 tabular-nums text-xs sm:text-sm">{{ $product->stock }} u.</td>
                        <td class="py-3.5 px-4 text-right space-x-1 whitespace-nowrap">
                            <button @click="openEditModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->category_id ?? '' }}', {{ $product->price }}, {{ $product->stock }}, '{{ $product->sku ?? '' }}', '{{ $product->image ? route('products.image', $product->image) : '' }}')"
                                class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500/10 border border-transparent hover:border-indigo-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <button @click="openDeleteModal({{ $product->id }})" class="p-2 text-rose-500 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M9 3h6M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-slate-400 dark:text-slate-600 font-bold uppercase text-xs tracking-wider">No hay productos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL CREAR --}}
    <x-modal name="create" title="Crear Nuevo Producto" maxWidth="max-w-xl">
        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="relative z-10 flex flex-col flex-1 min-h-0 bg-transparent" x-data="{ showSizes: false }">
            @csrf
            <div class="p-5 sm:p-7 space-y-4 overflow-y-auto flex-1 min-h-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <div class="space-y-1.5">
                    <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Nombre del Producto</label>
                    <input type="text" name="name" class="form-input w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors" required>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Categoría</label>
                        <select name="category_id" class="form-input w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer" required>
                            <option value="">Selecciona una categoría</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">SKU</label>
                        <input type="text" name="sku" class="form-input uppercase w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Precio Venta ($)</label>
                        <input type="number" step="0.01" name="price" class="form-input w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors" required>
                    </div>
                    <div class="space-y-1.5">
                        <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Stock Inicial / Tallas</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="stock" x-model.number="globalStock" min="0" placeholder="Ej. 10" class="form-input w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors" required>
                            <button type="button" @click="showSizes = !showSizes" class="px-3 py-2 bg-indigo-500/10 hover:bg-indigo-500/20 border border-indigo-500/30 text-indigo-600 dark:text-indigo-400 rounded-xl text-xs font-black transition-all cursor-pointer shrink-0 flex items-center gap-1.5">
                                <span x-text="showSizes ? 'Ocultar Tallas' : 'Asignar Tallas'"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': showSizes }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- DESGLOSE DE TALLAS VALIDADO EN TIEMPO REAL CON ALERTAS --}}
                <div class="space-y-2 pt-3 border-t border-slate-200/60 dark:border-slate-800/60" x-show="showSizes" x-transition>
                    <div class="flex items-center justify-between">
                        <label class="form-label text-xs font-black text-indigo-500 dark:text-indigo-400 uppercase tracking-wide">Distribución por Talla</label>
                        <span class="text-[11px] font-bold" :class="totalTallasAssigned > globalStock ? 'text-rose-500 font-black' : 'text-slate-400'">
                            Asignado: <span x-text="totalTallasAssigned"></span> / <span x-text="globalStock"></span>
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-1">
                        <template x-for="(item, index) in tallasList" :key="index">
                            <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-[#070a11] border border-slate-200 dark:border-slate-800/80 rounded-xl shadow-2xs">
                                <div class="flex items-center gap-2.5">
                                    <input type="hidden" :name="`tallas[${index}][talla]`" :value="item.talla" :disabled="!item.active">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="item.active" class="sr-only peer" @change="if(!item.active) item.stock = ''">
                                        <div class="w-8 h-4 bg-slate-300 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-indigo-600"></div>
                                    </label>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300" x-text="`${item.talla} CM`"></span>
                                </div>
                                <input type="number"
                                    :name="`tallas[${index}][stock]`"
                                    x-model.number="item.stock"
                                    @input="validateSizeInput(index)"
                                    @keyup="validateSizeInput(index)"
                                    :disabled="!item.active"
                                    min="1"
                                    :max="getMaxForSize(index)"
                                    placeholder="Pzas"
                                    class="w-16 text-center px-2 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg text-xs font-semibold focus:outline-none focus:border-indigo-500 transition-colors disabled:opacity-40" />
                            </div>
                        </template>
                    </div>
                    <div x-show="totalTallasAssigned > globalStock" class="text-[11px] font-bold text-rose-500 pt-1 text-right">
                        La suma por tallas sobrepasa el stock inicial.
                    </div>
                </div>

                <div class="space-y-1.5 pt-2">
                    <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Imagen del Producto (Opcional)</label>
                    <div onclick="document.getElementById('file-upload').click()" class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-indigo-500/80 bg-slate-50 dark:bg-slate-900/50 rounded-2xl w-28 h-28 flex flex-col items-center justify-center cursor-pointer relative overflow-hidden transition-all group">
                        <img id="image-preview" class="absolute inset-0 w-full h-full object-cover hidden z-10">
                        <div id="upload-prompt" class="text-center p-2 group-hover:scale-105 transition-transform">
                            <svg class="mx-auto h-6 w-6 text-indigo-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-[9px] font-black uppercase text-indigo-600 dark:text-indigo-400">Subir Img</p>
                        </div>
                        <input id="file-upload" type="file" name="image" accept="image/*" class="hidden" @change="previewFile($event, 'create')">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-5 sm:px-7 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-[#080c16]/50 shrink-0">
                <button type="button" @click="closeModal('create')" class="btn-secondary px-4 py-2 bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs transition-all cursor-pointer">Cancelar</button>
                <button type="submit"
                    :disabled="totalTallasAssigned > globalStock"
                    :class="{ 'opacity-50 cursor-not-allowed': totalTallasAssigned > globalStock }"
                    class="btn-primary px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs transition-all shadow-md active:scale-95 cursor-pointer">
                    Guardar Producto
                </button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL EDITAR --}}
    <x-modal name="edit" title="Modificar Producto Existente" maxWidth="max-w-lg">
        <form :action="'{{ route('products.index') }}/' + formEdit.id" method="POST" enctype="multipart/form-data" class="relative z-10 flex flex-col flex-1 min-h-0 bg-transparent">
            @csrf
            @method('PUT')
            <div class="p-5 sm:p-7 space-y-4 overflow-y-auto flex-1 min-h-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <div class="space-y-1.5">
                    <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Nombre del Producto</label>
                    <input type="text" x-model="formEdit.name" name="name" class="form-input w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors" required>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Categoría</label>
                        <select x-model="formEdit.category_id" name="category_id" class="form-input w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors cursor-pointer" required>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">SKU</label>
                        <input type="text" x-model="formEdit.sku" name="sku" class="form-input uppercase w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Precio Venta ($)</label>
                        <input type="number" step="0.01" x-model="formEdit.price" name="price" class="form-input w-full px-3.5 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs sm:text-sm focus:outline-none focus:border-indigo-500 transition-colors" required>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="form-label text-xs font-bold text-slate-700 dark:text-slate-300">Reemplazar Imagen (Opcional)</label>
                    <div onclick="document.getElementById('file-upload-edit').click()" class="border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-indigo-500/80 bg-slate-50 dark:bg-slate-900/50 rounded-2xl w-28 h-28 flex flex-col items-center justify-center cursor-pointer relative overflow-hidden transition-all group">
                        <img id="image-preview-edit" class="absolute inset-0 w-full h-full object-cover hidden z-10">
                        <div id="upload-prompt-edit" class="text-center p-2 group-hover:scale-105 transition-transform">
                            <svg class="mx-auto h-6 w-6 text-indigo-500 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-[9px] font-black uppercase text-indigo-600 dark:text-indigo-400">Nueva Img</p>
                        </div>
                        <input id="file-upload-edit" type="file" name="image" accept="image/*" class="hidden" @change="previewFile($event, 'edit')">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 p-5 sm:px-7 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-[#080c16]/50 shrink-0">
                <button type="button" @click="closeModal('edit')" class="btn-secondary px-4 py-2 bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs transition-all cursor-pointer">Cancelar</button>
                <button type="submit" class="btn-primary px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold text-xs transition-all shadow-md active:scale-95 cursor-pointer">Actualizar Producto</button>
            </div>
        </form>
    </x-modal>

    {{-- MODAL ELIMINAR --}}
    <x-modal name="delete" title="¿Confirmar Destrucción?" maxWidth="max-w-sm" dotColor="bg-rose-500">
        <div class="p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto border border-rose-500/20 shadow-inner">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Esta acción eliminará permanentemente el producto del inventario global. No se puede deshacer.</p>
            <form :action="'{{ route('products.index') }}/' + deleteId" method="POST" class="flex justify-center gap-3 pt-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="closeModal('delete')" class="btn-secondary px-4 py-2 bg-slate-200/80 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl font-bold text-xs transition-all cursor-pointer">Cancelar</button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold text-xs transition-all shadow-md shadow-rose-500/20 active:scale-95 cursor-pointer">Eliminar</button>
            </form>
        </div>
    </x-modal>
</div>
</x-app-container>
@endsection
@push('scripts')
<script src="{{ asset('js/components/product-management.js') }}"></script>
@endpush