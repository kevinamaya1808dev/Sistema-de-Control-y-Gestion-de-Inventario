@extends('layouts.app')

@section('title', 'SCGI - Movimientos de Stock')
@section('header_title', 'Control de Inventarios - Movimientos')

@section('content')
<div class="space-y-6" x-data="stockManagement()">

    <!-- HEADER -->
    <div class="flex flex-col gap-1">
        <nav class="text-xs font-semibold text-slate-400">
            SCGI <span class="mx-1.5 text-slate-300">/</span> <span class="text-slate-600 font-bold dark:text-slate-300">Inventarios</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-1">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Movimientos de Stock</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Registra entradas y salidas de mercancía para el control del taller</p>
            </div>
            <button type="button" 
                    @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Registrar Movimiento
            </button>
        </div>
    </div>

    <!-- TARJETAS DE MÉTRICAS RÁPIDAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <span class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalMovements ?? 0 }}</span>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2">Total movimientos</span>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $totalEntradas ?? 0 }}</span>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2">Entradas registradas</span>
        </div>
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $totalSalidas ?? 0 }}</span>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2">Salidas registradas</span>
        </div>
    </div>

    <!-- BARRA DE BÚSQUEDA Y FILTROS -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" 
                   x-model="searchQuery"
                   placeholder="Buscar por producto o motivo..." 
                   class="w-full pl-10 pr-4 py-2 bg-slate-50/75 dark:bg-slate-950/75 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <select x-model="selectedTypeFilter" class="px-4 py-2 bg-slate-50/75 dark:bg-slate-950/75 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-300 focus:outline-none">
                <option value="">Todos los tipos</option>
                <option value="entrada">Entradas</option>
                <option value="salida">Salidas</option>
            </select>
        </div>
    </div>

    <!-- TABLA DE MOVIMIENTOS -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-950/75 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Producto</th>
                        <th class="py-4 px-6">Tipo</th>
                        <th class="py-4 px-6">Cantidad</th>
                        <th class="py-4 px-6">Motivo</th>
                        <th class="py-4 px-6">Registrado por</th>
                        <th class="py-4 px-6">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-600 dark:text-slate-300">
                    @forelse($movements as $movement)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors"
                            x-show="(!searchQuery || '{{ strtolower(addslashes($movement->product->name ?? '')) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($movement->reason ?? '')) }}'.includes(searchQuery.toLowerCase())) && (!selectedTypeFilter || '{{ $movement->type }}' === selectedTypeFilter)">
                            
                            <td class="py-4 px-6 text-slate-400 font-mono text-[11px]">#{{ $movement->id }}</td>
                            <td class="py-4 px-6 font-bold text-slate-900 dark:text-white">{{ $movement->product->name ?? 'N/A' }}</td>
                            
                            <!-- Tipo -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase {{ $movement->type === 'entrada' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 'bg-rose-50 text-rose-700 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20' }}">
                                    {{ $movement->type }}
                                </span>
                            </td>

                            <td class="py-4 px-6 font-mono font-bold">{{ $movement->quantity }} pzas</td>
                            <td class="py-4 px-6 text-slate-500 dark:text-slate-400">{{ $movement->reason }}</td>
                            <td class="py-4 px-6">{{ $movement->user->name ?? 'Sistema' }}</td>
                            
                            {{-- FECHA FORMATO d/m/Y (SIN HORA) --}}
                            <td class="py-4 px-6 text-slate-400 text-[11px] whitespace-nowrap">
                                {{ $movement->created_at ? $movement->created_at->format('d/m/Y') : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No se encontraron movimientos de stock registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DE NUEVO MOVIMIENTO -->
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 w-full max-w-lg overflow-hidden transform transition-all"
             @click.outside="isModalOpen = false">
            
            <!-- Header -->
            <div class="px-8 py-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white">Registrar Movimiento de Inventario</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Añade o retira piezas del catálogo de inventario</p>
                </div>
                <button type="button" @click="isModalOpen = false" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Formulario -->
            <form action="{{ route('stock.store') }}" method="POST" class="p-8 space-y-5">
                @csrf

                <!-- PRODUCTO -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Producto</label>
                    <select name="product_id" 
                            x-model="currentMovement.product_id"
                            @change="onProductChange($event)"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50/75 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                        <option value="" disabled selected>Selecciona un artículo...</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                    data-price="{{ $product->price ?? $product->precio ?? 0 }}">
                                {{ $product->name }} (Stock actual: {{ $product->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- TIPO DE MOVIMIENTO -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Tipo de Movimiento</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                               :class="currentMovement.type === 'entrada' ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-500/10' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:border-slate-300'">
                            <input type="radio" name="type" value="entrada" x-model="currentMovement.type" class="sr-only">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">Entrada</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Sumar artículos</span>
                        </label>
                        <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                               :class="currentMovement.type === 'salida' ? 'border-rose-500 bg-rose-50/20 dark:bg-rose-500/10' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 hover:border-slate-300'">
                            <input type="radio" name="type" value="salida" x-model="currentMovement.type" class="sr-only">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">Salida</span>
                            <span class="text-[10px] text-slate-400 mt-0.5">Restar artículos</span>
                        </label>
                    </div>
                </div>

                <!-- CANTIDAD -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Cantidad</label>
                    <input type="number" 
                           name="quantity" 
                           x-model.number="currentMovement.quantity" 
                           min="1"
                           required
                           class="w-full px-4 py-2.5 bg-slate-50/75 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                </div>

                <!-- MOTIVO CON SELECTOR + ENTRADA LIBRE -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Motivo del Movimiento</label>
                    <select x-model="currentMovement.reason_preset"
                            required
                            class="w-full px-4 py-2.5 bg-slate-50/75 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                        <option value="" disabled selected>Selecciona una opción...</option>
                        
                        <!-- Motivos si es Entrada -->
                        <template x-if="currentMovement.type === 'entrada'">
                            <optgroup label="Motivos de Entrada">
                                <option value="Compra de insumos / piezas">Compra de insumos / piezas</option>
                                <option value="Devolución de cliente">Devolución de cliente</option>
                                <option value="Ajuste por inventario físico">Ajuste por inventario físico</option>
                            </optgroup>
                        </template>

                        <!-- Motivos si es Salida -->
                        <template x-if="currentMovement.type === 'salida'">
                            <optgroup label="Motivos de Salida">
                                <option value="Venta directa">Venta directa</option>
                                <option value="Uso en servicio / taller">Uso en servicio / taller</option>
                                <option value="Pieza dañada o defectuosa">Pieza dañada o defectuosa</option>
                                <option value="Ajuste por inventario físico">Ajuste por inventario físico</option>
                            </optgroup>
                        </template>

                        <option value="Otro">Otro motivo (Especificar...)</option>
                    </select>

                    <!-- Campo dinámico libre si eligen "Otro" -->
                    <div x-show="currentMovement.reason_preset === 'Otro'" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="mt-3">
                        <label class="block text-[10px] font-bold text-indigo-500 uppercase tracking-wider mb-1">Escribe tu motivo</label>
                        <input type="text" 
                               x-model="currentMovement.reason_custom"
                               :required="currentMovement.reason_preset === 'Otro'"
                               placeholder="Escribe el motivo personalizado..." 
                               class="w-full px-4 py-2.5 bg-white dark:bg-slate-950 border border-indigo-200 dark:border-indigo-900 rounded-xl text-xs font-semibold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                    </div>

                    <!-- Input oculto enviado al controlador con name="reason" -->
                    <input type="hidden" name="reason" :value="finalReason">
                </div>

                <!-- BLOQUE DINÁMICO DE COBRO PARA VENTA DIRECTA -->
                <div x-show="currentMovement.type === 'salida' && currentMovement.reason_preset === 'Venta directa'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="p-4 bg-indigo-50/50 dark:bg-slate-950 border border-indigo-100 dark:border-slate-800 rounded-2xl space-y-4">
                    
                    <div class="flex items-center justify-between border-b border-indigo-100/80 dark:border-slate-800 pb-2">
                        <span class="text-xs font-bold text-indigo-900 dark:text-indigo-400 uppercase tracking-wider">Detalles de Cobro</span>
                        <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">Total: $<span x-text="totalCobro.toFixed(2)"></span></span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <!-- PRECIO UNITARIO -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Precio Unitario ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   name="precio_unitario"
                                   x-model.number="currentMovement.precio_unitario"
                                   :required="currentMovement.type === 'salida' && currentMovement.reason_preset === 'Venta directa'"
                                   class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        </div>

                        <!-- MONTO RECIBIDO -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Dinero Recibido ($)</label>
                            <input type="number" step="0.01" min="0" 
                                   name="monto_recibido"
                                   x-model.number="currentMovement.monto_recibido"
                                   :required="currentMovement.type === 'salida' && currentMovement.reason_preset === 'Venta directa'"
                                   placeholder="0.00"
                                   class="w-full px-3.5 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs font-bold text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600">
                        </div>
                    </div>

                    <!-- DESGLOSE DE CAMBIO -->
                    <div class="flex items-center justify-between pt-1">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Cambio a entregar:</span>
                        <span class="text-base font-black" 
                              :class="cambioCalculado < 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
                            $<span x-text="cambioCalculado >= 0 ? cambioCalculado.toFixed(2) : '0.00'"></span>
                        </span>
                    </div>

                    <template x-if="cambioCalculado < 0 && currentMovement.monto_recibido > 0">
                        <p class="text-[10px] font-bold text-rose-500">⚠️ El monto recibido es menor al total del cobro.</p>
                    </template>
                </div>

                <!-- Footer del Modal -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" 
                            @click="isModalOpen = false" 
                            class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all cursor-pointer">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.sessionSuccess = @json(session('success'));
    window.sessionError = @json(session('error'));
</script>
<script src="{{ asset('js/components/stock-management.js') }}"></script>
@endpush