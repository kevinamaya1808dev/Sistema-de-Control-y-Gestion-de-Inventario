@extends('layouts.app')

@section('title', 'SCGI - Movimientos de Stock')
@section('header_title', 'Movimientos')

@section('content')
<div class="w-full max-w-full space-y-6" 
     x-data="stockManagement" 
     data-products="{{ json_encode($products ?? []) }}">

    {{-- HEADER Y BOTÓN --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-neutral-200/80">
        <div>
            <nav class="text-[11px] font-extrabold text-neutral-400 uppercase tracking-wider mb-1">
                SCGI <span class="mx-1 text-neutral-300">/</span> <span class="text-neutral-600">Inventarios</span>
            </nav>
            <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 tracking-tight">Movimientos de Stock</h1>
            <p class="text-xs text-neutral-500 mt-0.5">Ajustes generales, entradas de almacén, bajas e insumos</p>
        </div>
        
        <button type="button" 
                @click="openCreateModal()" 
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl transition-all shadow-md shadow-orange-600/20 text-xs uppercase tracking-wider cursor-pointer shrink-0 active:scale-95">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span>Registrar Movimiento</span>
        </button>
    </div>

    {{-- KPIS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 w-full">
        
        {{-- Total Movimientos --}}
        <div class="p-5 bg-white rounded-2xl border border-neutral-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-2xl sm:text-3xl font-black text-neutral-900 tracking-tight block">{{ $totalMovements ?? 0 }}</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mt-1">Total movimientos</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-neutral-100 border border-neutral-200/80 text-neutral-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
        </div>

        {{-- Entradas --}}
        <div class="p-5 bg-white rounded-2xl border border-neutral-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 tracking-tight block">{{ $totalEntradas ?? 0 }}</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mt-1">Entradas registradas</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-200/60 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </div>
        </div>

        {{-- Salidas --}}
        <div class="p-5 bg-white rounded-2xl border border-neutral-200/80 shadow-xs flex items-center justify-between sm:col-span-2 lg:col-span-1">
            <div>
                <span class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight block">{{ $totalSalidas ?? 0 }}</span>
                <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mt-1">Salidas registradas</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-200/60 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="p-4 bg-white rounded-2xl border border-neutral-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
        <div class="relative w-full sm:max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-neutral-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" 
                   x-model="searchQuery" 
                   placeholder="Buscar por producto o motivo..." 
                   class="w-full pl-10 pr-4 py-2 text-xs sm:text-sm bg-neutral-50 border border-neutral-200 rounded-xl text-neutral-900 focus:outline-none focus:border-orange-500 focus:bg-white transition-colors">
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <select x-model="selectedTypeFilter" class="w-full sm:w-auto px-3 py-2 text-xs sm:text-sm bg-neutral-50 border border-neutral-200 rounded-xl text-neutral-700 font-medium focus:outline-none focus:border-orange-500 focus:bg-white cursor-pointer transition-colors">
                <option value="">Todos los tipos</option>
                <option value="entrada">Entradas</option>
                <option value="salida">Salidas</option>
            </select>
        </div>
    </div>

    {{-- CONTENEDOR TABLAS --}}
    <div class="w-full space-y-4">
        <div class="hidden md:block w-full overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-xs">
            @include('stock.partials.desktop-table')
        </div>

        <div class="block md:hidden w-full space-y-3">
            @include('stock.partials.mobile-list')
        </div>
    </div>

    {{-- MODAL REGISTRAR MOVIMIENTO --}}
    <x-modal name="create" title="Registrar Movimiento de Inventario" maxWidth="max-w-lg">
        <form action="{{ route('stock.store') }}" method="POST" enctype="multipart/form-data" class="relative z-10 flex flex-col flex-1 min-h-0 bg-white">
            @csrf
            <div class="p-5 sm:p-7 space-y-5 overflow-y-auto flex-1 min-h-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

                {{-- PRODUCTO --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-neutral-700">Modelo de Producto</label>
                    <select name="product_id" 
                            x-model="currentMovement.product_id" 
                            @change="onProductChange($event)"
                            required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-neutral-50 border border-neutral-200 rounded-xl text-neutral-900 font-medium focus:outline-none focus:border-orange-500 focus:bg-white transition-colors cursor-pointer">
                        <option value="" disabled selected>Selecciona un modelo...</option>
                        @foreach($products ?? [] as $product)
                            <option value="{{ $product->id }}" data-sizes="{{ json_encode($product->sizes ?? $product->tallas ?? []) }}">
                                {{ $product->name }} (Stock total: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- TALLA DINÁMICA CON STOCK BADGE --}}
                <div class="space-y-1.5" x-show="currentMovement.product_id" x-transition>
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-neutral-700">Talla (MX / CM)</label>

                        {{-- BADGE DE STOCK POR TALLA SELECCIONADA --}}
                        <template x-if="currentSizeStock !== null">
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold px-2 py-0.5 rounded-md"
                                  :class="currentSizeStock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-rose-50 text-rose-700 border border-rose-200/60'">
                                Stock actual: <span x-text="currentSizeStock"></span>
                            </span>
                        </template>
                    </div>

                    <select name="talla" 
                            x-model="currentMovement.talla" 
                            required 
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-neutral-50 border border-neutral-200 rounded-xl text-neutral-900 font-medium focus:outline-none focus:border-orange-500 focus:bg-white transition-colors cursor-pointer">
                        <option value="" disabled selected>Selecciona una talla...</option>
                        
                        <template x-for="(item, index) in availableSizes" :key="index">
                            <option :value="typeof item === 'object' ? (item.talla || item.name || item.size) : item" 
                                    x-text="typeof item === 'object' ? ((item.talla || item.name || item.size) + (item.stock !== undefined ? ' — Stock: ' + item.stock : '')) : item">
                            </option>
                        </template>

                        <template x-if="availableSizes.length === 0">
                            <option value="N/A">N/A (Sin tallas específicas)</option>
                        </template>
                    </select>
                </div>

                {{-- TIPO DE MOVIMIENTO --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-neutral-700">Tipo de Ajuste</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                               :class="currentMovement.type === 'entrada' ? 'border-emerald-500 bg-emerald-50/50' : 'border-neutral-200 bg-neutral-50 hover:bg-neutral-100/60'">
                            <input type="radio" name="type" value="entrada" x-model="currentMovement.type" class="sr-only">
                            <span class="text-xs font-bold text-neutral-900 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block shrink-0"></span> Entrada
                            </span>
                            <span class="text-[11px] text-neutral-500 mt-0.5">Surtido / Ingreso</span>
                        </label>

                        <label class="relative flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all duration-200"
                               :class="currentMovement.type === 'salida' ? 'border-rose-500 bg-rose-50/50' : 'border-neutral-200 bg-neutral-50 hover:bg-neutral-100/60'">
                            <input type="radio" name="type" value="salida" x-model="currentMovement.type" class="sr-only">
                            <span class="text-xs font-bold text-neutral-900 flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block shrink-0"></span> Salida
                            </span>
                            <span class="text-[11px] text-neutral-500 mt-0.5">Defecto / Pérdida</span>
                        </label>
                    </div>
                </div>

                {{-- CANTIDAD --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-neutral-700">Cantidad de Piezas / Pares</label>
                    <input type="number" name="quantity" x-model.number="currentMovement.quantity" min="1" required class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-neutral-50 border border-neutral-200 rounded-xl text-neutral-900 font-medium focus:outline-none focus:border-orange-500 focus:bg-white transition-colors">
                </div>

                {{-- MOTIVO DEL MOVIMIENTO --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-neutral-700">Motivo</label>
                    <select x-model="currentMovement.reason_preset" required class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-neutral-50 border border-neutral-200 rounded-xl text-neutral-900 font-medium focus:outline-none focus:border-orange-500 focus:bg-white transition-colors cursor-pointer">
                        <option value="" disabled selected>Selecciona un motivo...</option>
                        
                        <template x-if="currentMovement.type === 'entrada'">
                            <optgroup label="Motivos de Entrada">
                                <option value="Surtido de proveedor">Surtido de proveedor</option>
                                <option value="Devolución de cliente">Devolución de cliente</option>
                                <option value="Ajuste de inventario físico">Ajuste por inventario físico (+)</option>
                            </optgroup>
                        </template>
                        
                        <template x-if="currentMovement.type === 'salida'">
                            <optgroup label="Motivos de Salida">
                                <option value="Calzado con defecto / dañado">Producto con defecto / dañado</option>
                                <option value="Pérdida o extravío">Pérdida / Extravío</option>
                                <option value="Ajuste de inventario físico">Ajuste por inventario físico (-)</option>
                            </optgroup>
                        </template>
                        
                        <option value="Otro">Otro motivo (Especificar...)</option>
                    </select>

                    <div x-show="currentMovement.reason_preset === 'Otro'" class="mt-2">
                        <input type="text" x-model="currentMovement.reason_custom" :required="currentMovement.reason_preset === 'Otro'" placeholder="Escribe el motivo..." class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-orange-300 rounded-xl text-neutral-900 focus:outline-none focus:border-orange-500">
                    </div>
                    <input type="hidden" name="reason" :value="finalReason">
                </div>

                {{-- AFECTACIÓN DE CAJA Y COSTO UNITARIO --}}
                <div x-show="currentMovement.reason_preset === 'Surtido de proveedor' || currentMovement.reason_preset === 'Salida por uso de insumo interno' || currentMovement.reason_preset === 'Gasto operativo de caja'" x-transition class="p-3.5 bg-orange-50 border border-orange-200 rounded-xl space-y-3">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="pagar_con_caja" id="pagar_con_caja" value="1" x-model="currentMovement.pagar_con_caja" class="rounded border-orange-300 text-orange-600 focus:ring-orange-500 h-4 w-4">
                        <label for="pagar_con_caja" class="text-xs font-bold text-orange-900 cursor-pointer">
                            ¿Impactar costo / gasto en la Caja Abierta Activa?
                        </label>
                    </div>
                    
                    <div x-show="currentMovement.pagar_con_caja" class="space-y-1">
                        <label class="block text-[11px] font-bold text-orange-800">Costo total pagado con dinero de caja ($)</label>
                        <input type="number" step="0.01" min="0" name="unit_price" placeholder="0.00" x-model="currentMovement.unit_price" class="w-full px-3 py-1.5 text-xs bg-white border border-orange-300 rounded-lg text-neutral-900 focus:outline-none focus:border-orange-500">
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flex items-center justify-end gap-2 p-4 sm:px-7 border-t border-neutral-100 bg-neutral-50/50 shrink-0">
                <button type="button" @click="closeModal('create')" class="px-4 py-2 rounded-xl border border-neutral-200 text-neutral-600 hover:bg-neutral-100 text-xs font-bold transition-colors">Cancelar</button>
                <button type="submit" class="py-2 px-4 bg-orange-600 hover:bg-orange-700 active:scale-95 text-white font-bold rounded-xl transition duration-200 shadow-md shadow-orange-600/20 text-xs uppercase tracking-wider cursor-pointer">Guardar Movimiento</button>
            </div>
        </form>
    </x-modal>

</div>
@endsection

@push('scripts')
<script>
    window.sessionSuccess = @json(session('success'));
    window.sessionError = @json(session('error'));
</script>
<script src="{{ asset('js/components/stock-management.js') }}"></script>
@endpush