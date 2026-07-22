@extends('layouts.app')

@section('title', 'SCGI - Movimientos de Stock')
@section('header_title', 'Movimientos')

@section('content')
<x-app-container>
<div class="space-y-8" x-data="stockManagement()">

    {{-- HEADER --}}
    <div class="flex flex-col gap-1">
        <nav class="text-xs font-bold text-indigo-500 tracking-wide uppercase">
            SCGI <span class="mx-1 text-slate-400 dark:text-slate-600">/</span> <span class="text-slate-600 dark:text-slate-300">Inventarios</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-1">
            <div>
                <h1 class="page-title">Movimientos de Stock</h1>
                <p class="page-subtitle">Registra entradas y salidas de mercancía para el control de la tienda</p>
            </div>
            <button type="button" @click="openCreateModal()" class="btn-primary w-full sm:w-auto uppercase tracking-wider cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Registrar Movimiento</span>
            </button>
        </div>
    </div>

    {{-- TARJETAS DE MÉTRICAS (KPIS) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div>
                    <span class="kpi-value">{{ $totalMovements ?? 0 }}</span>
                    <span class="kpi-label">Total movimientos</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
            </div>
        </div>

        <div class="kpi-card">
            <div class="flex items-center justify-between">
                <div>
                    <span class="kpi-value text-emerald-500 dark:text-emerald-400">{{ $totalEntradas ?? 0 }}</span>
                    <span class="kpi-label">Entradas registradas</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </div>
            </div>
        </div>

        <div class="kpi-card sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between">
                <div>
                    <span class="kpi-value text-rose-500 dark:text-rose-400">{{ $totalSalidas ?? 0 }}</span>
                    <span class="kpi-label">Salidas registradas</span>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-500 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- BÚSQUEDA Y FILTROS --}}
    <div class="card-base !p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:max-w-md">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" x-model="searchQuery" placeholder="Buscar por modelo de tenis o motivo..." class="form-input !pl-11">
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <select x-model="selectedTypeFilter" class="form-input !w-auto cursor-pointer w-full sm:w-auto">
                <option value="">Todos los tipos</option>
                <option value="entrada">Entradas</option>
                <option value="salida">Salidas</option>
            </select>
        </div>
    </div>

    {{-- TABLA MAESTRA Y VISTA MÓVIL (Modularizadas) --}}
    <div class="table-container">
        @include('stock.partials.desktop-table')
        @include('stock.partials.mobile-list')
    </div>

    {{-- MODAL REGISTRAR MOVIMIENTO --}}
    <x-modal name="create" title="Registrar Movimiento" maxWidth="max-w-lg">
        <form action="{{ route('stock.store') }}" method="POST" enctype="multipart/form-data" class="relative z-10 flex flex-col flex-1 min-h-0 bg-transparent">
            @csrf
            <div class="p-5 sm:p-7 space-y-4 overflow-y-auto flex-1 min-h-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

                {{-- PRODUCTO (CON ATRIBUTO DATA-IMAGE PARA EL JS) --}}
                <div class="space-y-1.5">
                    <label class="form-label">Modelo de Tenis</label>
                    <select name="product_id" 
                            x-model="currentMovement.product_id" 
                            @change="onProductChange($event)" 
                            required 
                            class="form-input cursor-pointer">
                        <option value="" disabled selected>Selecciona un modelo...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-price="{{ $product->price ?? $product->precio ?? 0 }}"
                                    data-image="{{ $product->image ? asset('storage/' . $product->image) : ($product->imagen ? asset('storage/' . $product->imagen) : '') }}">
                                {{ $product->name }} (Stock: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TALLA (Aparece dinámicamente al seleccionar un producto) --}}
                <div class="space-y-1.5" x-show="currentMovement.product_id" x-transition>
                    <label class="form-label">Talla (MX / CM)</label>
                    <select name="talla" x-model="currentMovement.talla" class="form-input cursor-pointer">
                        <option value="" disabled selected>Selecciona la numeración...</option>
                        <option value="22 CM">22 CM (4 US)</option>
                        <option value="23 CM">23 CM (5 US)</option>
                        <option value="24 CM">24 CM (6 US)</option>
                        <option value="25 CM">25 CM (7 US)</option>
                        <option value="26 CM">26 CM (8 US)</option>
                        <option value="27 CM">27 CM (9 US)</option>
                        <option value="28 CM">28 CM (10 US)</option>
                        <option value="29 CM">29 CM (11 US)</option>
                        <option value="30 CM">30 CM (12 US)</option>
                        <option value="N/A">N/A (General / Sin talla)</option>
                    </select>
                </div>

                {{-- TIPO DE MOVIMIENTO --}}
                <div class="space-y-1.5">
                    <label class="form-label">Tipo de Movimiento</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                               :class="currentMovement.type === 'entrada' ? 'border-emerald-500 bg-emerald-500/10 shadow-lg shadow-emerald-500/10' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#070a11] hover:border-slate-300 dark:hover:border-slate-700'">
                            <input type="radio" name="type" value="entrada" x-model="currentMovement.type" class="sr-only">
                            <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500"></span> Entrada
                            </span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Surtir almacén</span>
                        </label>

                        <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                               :class="currentMovement.type === 'salida' ? 'border-rose-500 bg-rose-500/10 shadow-lg shadow-rose-500/10' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#070a11] hover:border-slate-300 dark:hover:border-slate-700'">
                            <input type="radio" name="type" value="salida" x-model="currentMovement.type" class="sr-only">
                            <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-rose-500 shadow-sm shadow-rose-500"></span> Salida
                            </span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Venta o merma</span>
                        </label>
                    </div>
                </div>

                {{-- CANTIDAD --}}
                <div class="space-y-1.5">
                    <label class="form-label">Cantidad de Pares</label>
                    <input type="number" name="quantity" x-model.number="currentMovement.quantity" min="1" required class="form-input">
                </div>

                {{-- MOTIVO --}}
                <div class="space-y-1.5">
                    <label class="form-label">Motivo del Movimiento</label>
                    <select x-model="currentMovement.reason_preset" required class="form-input cursor-pointer">
                        <option value="" disabled selected>Selecciona una opción...</option>
                        <template x-if="currentMovement.type === 'entrada'">
                            <optgroup label="Motivos de Entrada">
                                <option value="Compra de insumos / piezas">Surtido de proveedor / Compra</option>
                                <option value="Devolución de cliente">Devolución de cliente</option>
                                <option value="Ajuste por inventario físico">Ajuste por inventario físico</option>
                            </optgroup>
                        </template>
                        <template x-if="currentMovement.type === 'salida'">
                            <optgroup label="Motivos de Salida">
                                <option value="Venta directa">Venta directa</option>
                                <option value="Pieza dañada o defectuosa">Calzado con defecto</option>
                                <option value="Ajuste por inventario físico">Ajuste por inventario físico</option>
                            </optgroup>
                        </template>
                        <option value="Otro">Otro motivo (Especificar...)</option>
                    </select>

                    <div x-show="currentMovement.reason_preset === 'Otro'" class="mt-2">
                        <input type="text" x-model="currentMovement.reason_custom" :required="currentMovement.reason_preset === 'Otro'" placeholder="Escribe el motivo personalizado..." class="form-input !border-indigo-500">
                    </div>
                    <input type="hidden" name="reason" :value="finalReason">
                </div>

                {{-- BLOQUE VENTA DIRECTA (CON FOTO DEL TENIS SELECCIONADO) --}}
                <div x-show="currentMovement.type === 'salida' && currentMovement.reason_preset === 'Venta directa'" class="p-4 bg-gradient-to-br from-indigo-500/10 to-purple-500/5 border border-indigo-500/20 rounded-2xl space-y-3" x-transition>
                    <div class="flex items-center justify-between border-b border-indigo-500/20 pb-2">
                        <span class="text-xs font-black text-indigo-400 uppercase tracking-wider">Detalles de Cobro</span>
                        <span class="text-xs font-black text-indigo-400">Total: $<span x-text="totalCobro.toFixed(2)"></span></span>
                    </div>

                    {{-- FOTO DEL TENIS A VENDER (Se muestra automáticamente si el producto tiene imagen) --}}
                    <div class="flex items-center gap-3 p-3 bg-slate-900/50 border border-indigo-500/20 rounded-xl" x-show="selectedProductImage">
                        <img :src="selectedProductImage" alt="Tenis a vender" class="w-14 h-14 object-cover rounded-lg border border-slate-700 shadow-md">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-200">Modelo Seleccionado</span>
                            <span class="text-[10px] text-indigo-400">Imagen cargada del inventario</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="form-label !text-[10px]">PRECIO UNITARIO ($)</label>
                            <input type="number" step="0.01" min="0" name="precio_unitario" x-model.number="currentMovement.precio_unitario" class="form-input !py-2">
                        </div>
                        <div>
                            <label class="form-label !text-[10px]">DINERO RECIBIDO ($)</label>
                            <input type="number" step="0.01" min="0" name="monto_recibido" x-model.number="currentMovement.monto_recibido" placeholder="0.00" class="form-input !py-2">
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs font-bold text-slate-300">Cambio a entregar:</span>
                        <span class="text-base font-black" :class="cambioCalculado < 0 ? 'text-rose-400' : 'text-emerald-400'">
                            $<span x-text="cambioCalculado >= 0 ? cambioCalculado.toFixed(2) : '0.00'"></span>
                        </span>
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flex items-center justify-end gap-3 p-5 sm:px-7 border-t border-slate-100 dark:border-white/5 bg-transparent shrink-0">
                <button type="button" @click="closeModal('create')" class="btn-secondary">Cancelar</button>
                <button type="submit" class="btn-primary">Registrar</button>
            </div>
        </form>
    </x-modal>

</div>
</x-app-container>
@endsection

@push('scripts')
<script>
    window.sessionSuccess = @json(session('success'));
    window.sessionError = @json(session('error'));
</script>
<script src="{{ asset('js/components/stock-management.js') }}"></script>
@endpush