@extends('layouts.app')

@section('title', 'SCGI - Movimientos de Stock')
@section('header_title', 'Movimientos')

@section('content')
<x-app-container>
    <div class="w-full max-w-full space-y-6 sm:space-y-8" x-data="stockManagement()">

        {{-- HEADER Y BOTÓN --}}
        <div class="flex flex-col gap-1 w-full">
            <nav class="text-[10px] sm:text-xs font-bold text-indigo-500 tracking-wide uppercase">
                SCGI <span class="mx-1 text-slate-400 dark:text-slate-600">/</span> <span class="text-slate-600 dark:text-slate-300">Inventarios</span>
            </nav>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-1 w-full">
                <div class="min-w-0">
                    <h1 class="page-title text-lg sm:text-2xl font-black truncate">Movimientos de Stock</h1>
                    <p class="page-subtitle text-xs text-slate-400">Ajustes generales, entradas de almacén y bajas por defecto o pérdida</p>
                </div>
                <button type="button" @click="openCreateModal()" class="btn-primary w-full sm:w-auto uppercase tracking-wider cursor-pointer justify-center text-xs py-2.5 px-4 shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Registrar Movimiento</span>
                </button>
            </div>
        </div>

        {{-- KPIS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-5 w-full">
            <div class="kpi-card p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="kpi-value text-xl sm:text-3xl font-black">{{ $totalMovements ?? 0 }}</span>
                        <span class="kpi-label block text-[11px] mt-0.5">Total movimientos</span>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                </div>
            </div>

            <div class="kpi-card p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="kpi-value text-xl sm:text-3xl font-black text-emerald-500 dark:text-emerald-400">{{ $totalEntradas ?? 0 }}</span>
                        <span class="kpi-label block text-[11px] mt-0.5">Entradas registradas</span>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                    </div>
                </div>
            </div>

            <div class="kpi-card p-4 sm:col-span-2 lg:col-span-1">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="kpi-value text-xl sm:text-3xl font-black text-rose-500 dark:text-rose-400">{{ $totalSalidas ?? 0 }}</span>
                        <span class="kpi-label block text-[11px] mt-0.5">Salidas registradas</span>
                    </div>
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTROS --}}
        <div class="card-base !p-3 sm:!p-4 flex flex-col sm:flex-row items-center justify-between gap-3 w-full">
            <div class="relative w-full sm:max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" x-model="searchQuery" placeholder="Buscar por modelo de tenis o motivo..." class="form-input !pl-10 text-xs sm:text-sm w-full">
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <select x-model="selectedTypeFilter" class="form-input cursor-pointer w-full sm:w-auto text-xs sm:text-sm">
                    <option value="">Todos los tipos</option>
                    <option value="entrada">Entradas</option>
                    <option value="salida">Salidas</option>
                </select>
            </div>
        </div>

        {{-- CONTENEDOR TABLAS --}}
        <div class="w-full">
            <div class="hidden md:block w-full overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white dark:bg-[#0b0f19]">
                @include('stock.partials.desktop-table')
            </div>

            <div class="block md:hidden w-full space-y-3">
                @include('stock.partials.mobile-list')
            </div>
        </div>

        {{-- MODAL REGISTRAR MOVIMIENTO --}}
        <x-modal name="create" title="Registrar Movimiento de Inventario" maxWidth="max-w-lg">
            <form action="{{ route('stock.store') }}" method="POST" enctype="multipart/form-data" class="relative z-10 flex flex-col flex-1 min-h-0 bg-transparent">
                @csrf
                <div class="p-4 sm:p-7 space-y-4 overflow-y-auto flex-1 min-h-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

                    {{-- PRODUCTO --}}
                    <div class="space-y-1.5">
                        <label class="form-label text-xs">Modelo de Tenis</label>
                        <select name="product_id" 
                                x-model="currentMovement.product_id" 
                                required 
                                class="form-input cursor-pointer text-xs sm:text-sm w-full">
                            <option value="" disabled selected>Selecciona un modelo...</option>
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->name }} (Stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TALLA --}}
                    <div class="space-y-1.5" x-show="currentMovement.product_id" x-transition>
                        <label class="form-label text-xs">Talla (MX / CM)</label>
                        <select name="talla" x-model="currentMovement.talla" required class="form-input cursor-pointer text-xs sm:text-sm w-full">
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
                            <option value="N/A">N/A (Sin talla específica)</option>
                        </select>
                    </div>

                    {{-- TIPO DE MOVIMIENTO --}}
                    <div class="space-y-1.5">
                        <label class="form-label text-xs">Tipo de Ajuste</label>
                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            <label class="relative flex flex-col p-3 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                                   :class="currentMovement.type === 'entrada' ? 'border-emerald-500 bg-emerald-500/10 shadow-lg shadow-emerald-500/10' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#070a11] hover:border-slate-300 dark:hover:border-slate-700'">
                                <input type="radio" name="type" value="entrada" x-model="currentMovement.type" class="sr-only">
                                <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500 shrink-0"></span> Entrada
                                </span>
                                <span class="text-[10px] text-slate-400 mt-0.5">Surtido / Ingreso</span>
                            </label>

                            <label class="relative flex flex-col p-3 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                                   :class="currentMovement.type === 'salida' ? 'border-rose-500 bg-rose-500/10 shadow-lg shadow-rose-500/10' : 'border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-[#070a11] hover:border-slate-300 dark:hover:border-slate-700'">
                                <input type="radio" name="type" value="salida" x-model="currentMovement.type" class="sr-only">
                                <span class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 shadow-sm shadow-rose-500 shrink-0"></span> Salida
                                </span>
                                <span class="text-[10px] text-slate-400 mt-0.5">Ajuste / Defecto / Pérdida</span>
                            </label>
                        </div>
                    </div>

                    {{-- CANTIDAD --}}
                    <div class="space-y-1.5">
                        <label class="form-label text-xs">Cantidad de Pares</label>
                        <input type="number" name="quantity" x-model.number="currentMovement.quantity" min="1" required class="form-input text-xs sm:text-sm w-full">
                    </div>

                    {{-- MOTIVO DEL MOVIMIENTO --}}
                    <div class="space-y-1.5">
                        <label class="form-label text-xs">Motivo</label>
                        <select x-model="currentMovement.reason_preset" required class="form-input cursor-pointer text-xs sm:text-sm w-full">
                            <option value="" disabled selected>Selecciona un motivo...</option>
                            
                            <template x-if="currentMovement.type === 'entrada'">
                                <optgroup label="Motivos de Entrada">
                                    <option value="Surtido de proveedor">Surtido de proveedor / Compra</option>
                                    <option value="Devolución de cliente">Devolución de cliente</option>
                                    <option value="Ajuste de inventario físico">Ajuste por inventario físico (+)</option>
                                </optgroup>
                            </template>
                            
                            <template x-if="currentMovement.type === 'salida'">
                                <optgroup label="Motivos de Salida">
                                    <option value="Calzado con defecto / dañado">Calzado con defecto / dañado</option>
                                    <option value="Pérdida o extravío">Pérdida / Extravío</option>
                                    <option value="Ajuste de inventario físico">Ajuste por inventario físico (-)</option>
                                </optgroup>
                            </template>
                            
                            <option value="Otro">Otro motivo (Especificar...)</option>
                        </select>

                        <div x-show="currentMovement.reason_preset === 'Otro'" class="mt-2">
                            <input type="text" x-model="currentMovement.reason_custom" :required="currentMovement.reason_preset === 'Otro'" placeholder="Escribe el motivo..." class="form-input !border-indigo-500 text-xs sm:text-sm w-full">
                        </div>
                        <input type="hidden" name="reason" :value="finalReason">
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="flex items-center justify-end gap-2 p-4 sm:px-7 border-t border-slate-100 dark:border-white/5 bg-transparent shrink-0">
                    <button type="button" @click="closeModal('create')" class="btn-secondary text-xs py-2 px-3.5">Cancelar</button>
                    <button type="submit" class="btn-primary text-xs py-2 px-4">Guardar Movimiento</button>
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