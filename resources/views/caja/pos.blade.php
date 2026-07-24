@extends('layouts.app')

@section('title', 'SCGI - Punto de Venta')
@section('header_title', 'Punto de Venta (POS)')

@section('content')
<x-app-container>
    {{-- Contenedor Principal con AlpineJS --}}
    <div class="w-full h-full flex flex-col lg:flex-row gap-4 pb-20 lg:pb-0 relative" 
         x-data="posSystem(@js($products), '{{ url('/productos/imagen') }}')">
        
        {{-- COLUMNA IZQUIERDA: Búsqueda y Catálogo de Productos --}}
        <div class="flex-1 flex flex-col gap-3 min-h-0 relative z-10">
            
            {{-- Buscador --}}
            <div class="bg-white dark:bg-[#070a11] border border-slate-200/80 dark:border-slate-800/80 p-3 rounded-2xl shadow-sm shrink-0">
                <div class="relative flex-1">
                    <svg class="w-5 h-5 absolute left-3.5 top-3 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           x-model="searchQuery"
                           @keydown.enter="addFirstMatch()"
                           placeholder="Buscar producto o código..." 
                           class="form-input pl-11 pr-10 text-sm py-2.5 bg-slate-50/50 dark:bg-[#0b0f19] border-slate-200/80 dark:border-slate-800 text-slate-900 dark:text-white w-full rounded-xl"
                           autofocus>
                    <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Grid de Productos --}}
            <div class="flex-1 overflow-y-auto pr-1 [scrollbar-width:thin]">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 p-1">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="selectProduct(product)" 
                             class="bg-white dark:bg-[#070a11] border border-slate-200/80 dark:border-slate-800/80 hover:border-indigo-500/50 rounded-2xl p-2.5 flex flex-col justify-between cursor-pointer transition-all shadow-sm group relative">
                            
                            <div>
                                {{-- Imagen adaptable --}}
                                <div class="w-full h-28 sm:h-32 mb-2 bg-slate-50 dark:bg-[#0b0f19] rounded-xl overflow-hidden flex items-center justify-center border border-slate-200/60 dark:border-slate-800 relative">
                                    <template x-if="product.image_path || product.image">
                                        <img :src="getImageUrl(product.image_path || product.image)" :alt="product.name" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!product.image_path && !product.image">
                                        <div class="flex flex-col items-center justify-center text-slate-400 dark:text-slate-600">
                                            <svg class="w-6 h-6 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-[8px] font-bold uppercase tracking-wider">Sin foto</span>
                                        </div>
                                    </template>

                                    <template x-if="product.size || product.talla">
                                        <span class="absolute top-1.5 right-1.5 bg-slate-900/80 text-white text-[9px] font-black px-1.5 py-0.5 rounded-md border border-white/10 uppercase" x-text="product.size || product.talla"></span>
                                    </template>
                                </div>

                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block truncate" x-text="product.category ? product.category.name : 'General'"></span>
                                <h4 class="font-extrabold text-slate-900 dark:text-white text-xs mt-0.5 group-hover:text-indigo-400 line-clamp-2" x-text="product.name"></h4>
                            </div>
                            
                            <div class="mt-2.5 flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                <span class="text-xs sm:text-sm font-black text-indigo-600 dark:text-indigo-400" x-text="'$' + parseFloat(product.price).toFixed(2)"></span>
                                <span :class="product.stock > 5 ? 'badge-emerald' : 'badge-rose'" class="text-[9px] px-1.5 py-0.5" x-text="product.stock + 'P'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- COLUMNA DERECHA: Carrito / Orden Actual --}}
        <div class="w-full lg:w-96 bg-white dark:bg-[#070a11] border border-slate-200/80 dark:border-slate-800/80 rounded-3xl p-4 flex flex-col justify-between shadow-sm shrink-0 relative z-10">
            <div class="flex flex-col h-full min-h-[250px] lg:min-h-0">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800/80 shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <h3 class="font-black text-slate-900 dark:text-white text-xs sm:text-sm uppercase tracking-wider">Orden Actual</h3>
                    </div>
                    <button @click="cart = []" x-show="cart.length > 0" class="text-[10px] font-bold text-rose-500 uppercase tracking-wider hover:underline">Vaciar</button>
                </div>

                {{-- Items del carrito --}}
                <div class="flex-1 overflow-y-auto my-2.5 space-y-2 pr-1 [scrollbar-width:thin]">
                    <template x-for="(item, index) in cart" :key="item.key">
                        <div class="flex items-center gap-2.5 bg-slate-50/60 dark:bg-[#0b0f19] p-2 rounded-2xl border border-slate-200/60 dark:border-slate-800">
                            <div class="w-9 h-9 bg-slate-200/50 dark:bg-slate-800 rounded-xl overflow-hidden shrink-0 flex items-center justify-center">
                                <template x-if="item.image_path">
                                    <img :src="getImageUrl(item.image_path)" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!item.image_path">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </template>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h5 class="text-xs font-extrabold text-slate-900 dark:text-white truncate" x-text="item.name"></h5>
                                <span class="text-xs text-indigo-600 dark:text-indigo-400 font-black" x-text="'$' + (item.price * item.qty).toFixed(2)"></span>
                            </div>

                            <div class="flex items-center gap-1 bg-white dark:bg-[#070a11] border border-slate-200/80 dark:border-slate-800 rounded-xl p-0.5">
                                <button @click="updateQty(index, -1)" class="w-5 h-5 rounded-lg bg-slate-100 dark:bg-slate-800 font-black text-xs text-slate-700 dark:text-slate-200 flex items-center justify-center">-</button>
                                <span class="text-xs font-black w-5 text-center text-slate-900 dark:text-white" x-text="item.qty"></span>
                                <button @click="updateQty(index, 1)" class="w-5 h-5 rounded-lg bg-slate-100 dark:bg-slate-800 font-black text-xs text-slate-700 dark:text-slate-200 flex items-center justify-center">+</button>
                            </div>
                        </div>
                    </template>
                    
                    <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-400 text-xs py-8 gap-1">
                        <svg class="w-6 h-6 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span>Sin artículos en la orden</span>
                    </div>
                </div>
            </div>

            {{-- Totales y Botón de Cobro --}}
            <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 space-y-2.5 shrink-0">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-slate-400 uppercase">Total</span>
                    <span class="text-xl font-black text-emerald-500 font-mono" x-text="'$' + total.toFixed(2)"></span>
                </div>
                <button :disabled="cart.length === 0" 
                        @click="openModal('checkoutModal')"
                        class="btn-primary w-full py-3 text-xs uppercase tracking-wider disabled:opacity-40 shadow-sm">
                    Cobrar
                </button>
            </div>
        </div>
    {{-- MODAL DE TALLAS --}}
        <x-modal name="selectSize" title="Seleccionar Talla" maxWidth="max-w-sm">
            <div class="flex flex-col max-h-[80vh]">
                <div class="p-5 space-y-4 overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] flex-1">
                    <div class="text-center space-y-1">
                        <h3 class="text-sm font-black text-slate-900 dark:text-white" x-text="selectedProductForSize?.name"></h3>
                        <p class="text-xs text-slate-400">Selecciona la talla disponible:</p>
                    </div>

                    <div class="flex flex-wrap gap-2 justify-center p-1">
                        <template x-for="(sz, idx) in (selectedProductForSize?.sizes || [])" :key="typeof sz === 'object' ? (sz.id || idx) : idx">
                            {{-- Validamos que si es un objeto de la BD con stock, este sea mayor a 0 para mostrarse --}}
                            <button x-show="typeof sz !== 'object' || Number(sz.stock ?? sz.quantity ?? 1) > 0"
                                    @click="selectedSize = sz"
                                    :class="(selectedSize && (selectedSize === sz || selectedSize.id === sz.id)) 
                                        ? 'bg-indigo-600 text-white border-indigo-500 shadow-md ring-2 ring-indigo-500/40' 
                                        : 'bg-slate-50 dark:bg-[#0b0f19] text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-800'"
                                    class="px-3.5 py-2.5 rounded-xl border font-black text-xs flex flex-col items-center justify-center transition-all min-w-[70px] gap-0.5">
                                <span x-text="typeof sz === 'object' ? (sz.name || sz.size || sz.talla) : sz" class="uppercase"></span>
                                <span class="text-[10px] font-medium opacity-75" x-text="(typeof sz === 'object' && (sz.stock !== undefined || sz.quantity !== undefined)) ? (sz.stock ?? sz.quantity) + ' disp.' : ''"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 dark:bg-[#070a11] border-t border-slate-200/80 dark:border-slate-800 flex gap-2 shrink-0">
                    <button @click="closeModal('selectSize'); selectedProductForSize = null; selectedSize = null;" class="btn-secondary flex-1 text-xs py-2.5">Cancelar</button>
                    <button @click="confirmSizeSelection(); closeModal('selectSize');" :disabled="!selectedSize" class="btn-primary flex-1 text-xs py-2.5 disabled:opacity-40">Agregar</button>
                </div>
            </div>
        </x-modal>

        {{-- MODAL DE COBRO ESTRUCTURADO --}}
        <x-modal name="checkoutModal" title="Completar Cobro" maxWidth="max-w-md">
            <div class="p-5 space-y-4">
                <div class="bg-slate-50 dark:bg-[#0b0f19] border border-slate-200/80 dark:border-slate-800 rounded-2xl p-4 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total a pagar</span>
                        <span class="text-xl font-black text-emerald-500 font-mono" x-text="'$' + total.toFixed(2)"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Artículos</span>
                        <span class="text-sm font-black text-slate-700 dark:text-slate-300" x-text="cart.reduce((acc, item) => acc + item.qty, 0)"></span>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase">Método de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button @click="paymentMethod = 'cash'" 
                                :class="paymentMethod === 'cash' ? 'bg-indigo-600 text-white border-indigo-500 shadow-sm' : 'bg-slate-50 dark:bg-[#0b0f19] text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-800'"
                                class="py-2.5 px-3 rounded-xl border text-xs font-black transition-all text-center">
                            Efectivo
                        </button>
                        <button @click="paymentMethod = 'card'" 
                                :class="paymentMethod === 'card' ? 'bg-indigo-600 text-white border-indigo-500 shadow-sm' : 'bg-slate-50 dark:bg-[#0b0f19] text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-800'"
                                class="py-2.5 px-3 rounded-xl border text-xs font-black transition-all text-center">
                            Tarjeta
                        </button>
                        <button @click="paymentMethod = 'transfer'" 
                                :class="paymentMethod === 'transfer' ? 'bg-indigo-600 text-white border-indigo-500 shadow-sm' : 'bg-slate-50 dark:bg-[#0b0f19] text-slate-700 dark:text-slate-300 border-slate-200/80 dark:border-slate-800'"
                                class="py-2.5 px-3 rounded-xl border text-xs font-black transition-all text-center">
                            Transferencia
                        </button>
                    </div>
                </div>

                <div x-show="paymentMethod === 'cash'" class="space-y-3">
                    <div class="space-y-1.5">
                        <label class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase">Efectivo Recibido</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-slate-400 font-bold">$</span>
                            <input type="number" step="0.01" 
                                   x-model.number="amountReceived" 
                                   @input="calculateChange()"
                                   placeholder="0.00" 
                                   class="form-input pl-8 text-sm font-mono py-2.5 bg-slate-50/50 dark:bg-[#0b0f19] border-slate-200/80 dark:border-slate-800 text-slate-900 dark:text-white w-full rounded-xl">
                        </div>
                    </div>

                    <div class="flex items-center justify-between bg-slate-100/60 dark:bg-[#0b0f19]/60 border border-slate-200/60 dark:border-slate-800/80 p-3 rounded-xl">
                        <span class="text-xs font-black text-slate-500 uppercase">Cambio:</span>
                        <span class="text-base font-black font-mono" :class="change < 0 ? 'text-rose-500' : 'text-indigo-500'" x-text="'$' + (change >= 0 ? change.toFixed(2) : '0.00')"></span>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <button @click="closeModal('checkoutModal')" class="btn-secondary flex-1 text-xs py-2.5">Cancelar</button>
                    <button @click="processSale()" 
                            :disabled="paymentMethod === 'cash' && amountReceived < total"
                            class="btn-primary flex-1 text-xs py-2.5 shadow-sm disabled:opacity-40">
                        Confirmar Venta
                    </button>
                </div>
            </div>
        </x-modal>

        {{-- MODAL DEL TICKET FINAL --}}
        <x-modal name="ticketModal" title="Ticket de Venta" maxWidth="max-w-sm">
            <div class="flex flex-col max-h-[80vh]">
                {{-- Cuerpo con scroll oculto pero funcional --}}
                <div class="p-4 overflow-y-auto space-y-4 [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none] flex-1">
                    <div id="printableTicket" class="bg-white dark:bg-[#0b0f19] text-slate-900 dark:text-white p-2 mx-auto w-full max-w-[260px]">
                        @include('components.pos-ticket-print')
                    </div>
                </div>

                {{-- Pie fijo con los botones --}}
                <div class="p-3 bg-slate-50 dark:bg-[#070a11] border-t border-slate-200/80 dark:border-slate-800 flex gap-2 shrink-0">
                    <button @click="closeModal('ticketModal')" class="btn-secondary flex-1 text-xs py-2.5">Cerrar</button>
                    <button @click="printTicket()" class="btn-primary flex-1 text-xs py-2.5 shadow-sm flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimir
                    </button>
                </div>
            </div>
        </x-modal>

    </div>
</x-app-container>
@endsection

@push('scripts')
    <script src="{{ asset('js/components/pos.js') }}"></script>
@endpush