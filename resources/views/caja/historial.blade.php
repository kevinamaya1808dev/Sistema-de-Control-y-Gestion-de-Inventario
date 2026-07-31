@extends('layouts.app')

@section('title', 'SCGI - Historial de Caja')
@section('header_title', 'Historial de Turnos y Caja')

@section('content')
<x-app-container>
    <div class="w-full max-w-full space-y-6">

        {{-- CABECERA --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-neutral-200/80">
            <div>
                <nav class="text-[11px] font-extrabold text-neutral-400 uppercase tracking-wider mb-1">
                    SCGI <span class="mx-1 text-neutral-300">/</span> <span class="text-neutral-600">Caja</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-neutral-900 tracking-tight">Historial de Caja</h1>
                <p class="text-xs text-neutral-500 mt-0.5">Bitácora general de aperturas, cierres y flujos de efectivo</p>
            </div>
        </div>

        {{-- CONTENEDOR TABLA Y LISTA MÓVIL --}}
        <div class="w-full space-y-4">

            {{-- 1. VISTA MÓVIL (< 768px - TARJETAS) --}}
            <div class="block md:hidden space-y-3">
                @forelse($historial as $movimiento)
                    <div class="p-4 bg-white rounded-2xl border border-neutral-200/80 shadow-xs hover:border-neutral-300 transition-all space-y-3">
                        
                        {{-- Usuario y Estado --}}
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-neutral-100 border border-neutral-200/80 flex items-center justify-center font-bold text-neutral-700 text-xs shrink-0">
                                    {{ strtoupper(substr($movimiento->user->name ?? 'U', 0, 2)) }}
                                </div>
                                <span class="font-bold text-xs text-neutral-900 truncate">
                                    {{ $movimiento->user->name ?? 'Usuario Desconocido' }}
                                </span>
                            </div>

                            <div class="shrink-0">
                                @if(($movimiento->estado ?? '') === 'abierta')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-500 border border-neutral-200/80">
                                        Cerrada
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Montos --}}
                        <div class="grid grid-cols-2 gap-2 bg-neutral-50/80 p-3 rounded-xl border border-neutral-200/60 text-xs">
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400">Monto Inicial</span>
                                <span class="text-xs font-black text-emerald-600 mt-0.5 block">
                                    ${{ number_format($movimiento->monto_apertura ?? 0, 2) }}
                                </span>
                            </div>
                            <div>
                                <span class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400">Monto Cierre</span>
                                <span class="text-xs font-black text-neutral-900 mt-0.5 block">
                                    {{ !is_null($movimiento->monto_cierre) ? '$' . number_format($movimiento->monto_cierre, 2) : '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- Fechas (Sin horas) --}}
                        <div class="space-y-1.5 text-[11px] text-neutral-500 pt-1">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-neutral-400">Apertura:</span>
                                <span class="font-mono font-semibold text-neutral-700">
                                    {{ $movimiento->fecha_apertura ? \Carbon\Carbon::parse($movimiento->fecha_apertura)->format('d/m/Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-neutral-400">Cierre:</span>
                                <span class="font-mono font-semibold text-neutral-700">
                                    {{ $movimiento->fecha_cierre ? \Carbon\Carbon::parse($movimiento->fecha_cierre)->format('d/m/Y') : '-' }}
                                </span>
                            </div>
                        </div>

                        {{-- Botón Ver Detalle Móvil --}}
                        <div class="pt-2 border-t border-neutral-100">
                            <button type="button" onclick="verDetalleCaja({{ $movimiento->id }})" class="w-full bg-orange-50/80 hover:bg-orange-100 text-orange-600 border border-orange-200/80 font-bold text-xs py-2 px-3 rounded-xl transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Ver Detalle Completo
                            </button>
                        </div>

                    </div>
                @empty
                    <div class="p-8 bg-white rounded-2xl border border-neutral-200/80 text-center text-xs text-neutral-400 font-medium shadow-xs">
                        No hay registros de movimientos en la base de datos.
                    </div>
                @endforelse
            </div>

            {{-- 2. VISTA ESCRITORIO (>= 768px) --}}
            <div class="hidden md:block w-full overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-xs">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-neutral-200/80 bg-neutral-50/80 text-[11px] font-extrabold uppercase tracking-wider text-neutral-400">
                            <th class="py-3.5 px-6">Usuario</th>
                            <th class="py-3.5 px-6">Apertura</th>
                            <th class="py-3.5 px-6">Cierre</th>
                            <th class="py-3.5 px-6">Monto Inicial</th>
                            <th class="py-3.5 px-6">Monto Cierre</th>
                            <th class="py-3.5 px-6 text-center">Estado</th>
                            <th class="py-3.5 px-6 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-100 text-xs font-medium text-neutral-600">
                        @forelse($historial as $movimiento)
                            <tr class="hover:bg-neutral-50/80 transition-colors">
                                {{-- Usuario --}}
                                <td class="py-3.5 px-6 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-full bg-neutral-100 border border-neutral-200/80 flex items-center justify-center font-bold text-neutral-700 text-[10px] shrink-0">
                                            {{ strtoupper(substr($movimiento->user->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <span class="font-bold text-neutral-900">
                                            {{ $movimiento->user->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Fecha Apertura (Sin hora) --}}
                                <td class="py-3.5 px-6 whitespace-nowrap font-mono text-[11px] text-neutral-500">
                                    {{ $movimiento->fecha_apertura ? \Carbon\Carbon::parse($movimiento->fecha_apertura)->format('d/m/Y') : '-' }}
                                </td>

                                {{-- Fecha Cierre (Sin hora) --}}
                                <td class="py-3.5 px-6 whitespace-nowrap font-mono text-[11px] text-neutral-500">
                                    {{ $movimiento->fecha_cierre ? \Carbon\Carbon::parse($movimiento->fecha_cierre)->format('d/m/Y') : '-' }}
                                </td>

                                {{-- Monto Inicial --}}
                                <td class="py-3.5 px-6 whitespace-nowrap font-mono font-bold text-emerald-600 text-xs">
                                    ${{ number_format($movimiento->monto_apertura ?? 0, 2) }}
                                </td>

                                {{-- Monto Cierre --}}
                                <td class="py-3.5 px-6 whitespace-nowrap font-mono font-bold text-neutral-900 text-xs">
                                    {{ !is_null($movimiento->monto_cierre) ? '$' . number_format($movimiento->monto_cierre, 2) : '-' }}
                                </td>

                                {{-- Estado --}}
                                <td class="py-3.5 px-6 whitespace-nowrap text-center">
                                    @if(($movimiento->estado ?? '') === 'abierta')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Abierta
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-neutral-100 text-neutral-500 border border-neutral-200/80">
                                            Cerrada
                                        </span>
                                    @endif
                                </td>

                                {{-- Acción --}}
                                <td class="py-3.5 px-6 whitespace-nowrap text-center">
                                    <button type="button" onclick="verDetalleCaja({{ $movimiento->id }})" class="bg-orange-500 hover:bg-orange-600 active:scale-95 text-white font-bold px-3 py-1.5 rounded-xl text-xs transition-all inline-flex items-center gap-1.5 shadow-xs cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Ver Detalle
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-neutral-400 font-medium">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p>No hay registros de movimientos en la base de datos.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            @if(isset($historial) && method_exists($historial, 'hasPages') && $historial->hasPages())
                <div class="p-4 border border-neutral-200/80 rounded-2xl bg-white shadow-xs">
                    {{ $historial->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- MODAL DE DETALLE DEL TURNO --}}
    <div id="modalDetalle" class="fixed inset-0 bg-neutral-950/40 backdrop-blur-xs hidden items-center justify-center z-50 p-4 transition-all" onclick="if(event.target === this) cerrarModalDetalle()">
        <div class="bg-white rounded-3xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col shadow-2xl border border-neutral-200/80 animate-in fade-in zoom-in-95 duration-150">
            
            {{-- Header Modal --}}
            <div class="p-5 border-b border-neutral-200/80 flex justify-between items-center bg-neutral-50/50 shrink-0">
                <div>
                    <h3 class="text-base font-bold text-neutral-900" id="modalTitulo">Detalle del Turno</h3>
                    <p class="text-xs text-neutral-500">Desglose de flujos financieros y transacciones del turno</p>
                </div>
                <button type="button" onclick="cerrarModalDetalle()" class="w-8 h-8 rounded-full bg-neutral-100 hover:bg-neutral-200 text-neutral-500 hover:text-neutral-700 flex items-center justify-center font-bold text-base transition-colors cursor-pointer">&times;</button>
            </div>
            
            {{-- Cuerpo del Modal (Scroll Oculto) --}}
            <div class="p-6 overflow-y-auto space-y-6 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                <!-- Tarjetas Resumen Financiero -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 text-center">
                    <div class="bg-neutral-50 p-3 rounded-2xl border border-neutral-200/70">
                        <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Monto Inicial</span>
                        <span class="text-xs sm:text-sm font-black text-neutral-800 mt-0.5 block" id="detMontoInicial">$0.00</span>
                    </div>
                    <div class="bg-emerald-50/60 p-3 rounded-2xl border border-emerald-100">
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider block">Efectivo</span>
                        <span class="text-xs sm:text-sm font-black text-emerald-700 mt-0.5 block" id="detEfectivo">$0.00</span>
                    </div>
                    <div class="bg-indigo-50/60 p-3 rounded-2xl border border-indigo-100">
                        <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider block">Tarjeta</span>
                        <span class="text-xs sm:text-sm font-black text-indigo-700 mt-0.5 block" id="detTarjeta">$0.00</span>
                    </div>
                    <div class="bg-purple-50/60 p-3 rounded-2xl border border-purple-100">
                        <span class="text-[10px] font-bold text-purple-600 uppercase tracking-wider block">Transferencia</span>
                        <span class="text-xs sm:text-sm font-black text-purple-700 mt-0.5 block" id="detTransferencia">$0.00</span>
                    </div>
                    <div class="bg-rose-50/60 p-3 rounded-2xl border border-rose-100">
                        <span class="text-[10px] font-bold text-rose-600 uppercase tracking-wider block">Gastos</span>
                        <span class="text-xs sm:text-sm font-black text-rose-700 mt-0.5 block" id="detGastos">$0.00</span>
                    </div>
                    <div class="bg-teal-50/60 p-3 rounded-2xl border border-teal-100">
                        <span class="text-[10px] font-bold text-teal-600 uppercase tracking-wider block">Efectivo Caja</span>
                        <span class="text-xs sm:text-sm font-black text-teal-700 mt-0.5 block" id="detEsperado">$0.00</span>
                    </div>
                </div>

                <!-- Tabla de Movimientos Realizados -->
                <div>
                    <h4 class="text-xs font-extrabold uppercase tracking-wider text-neutral-400 mb-3">Movimientos Registrados en el Turno</h4>
                    <div class="border border-neutral-200/80 rounded-2xl overflow-hidden max-h-80 overflow-y-auto shadow-xs [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-neutral-50 text-neutral-400 uppercase tracking-wider text-[11px] font-extrabold sticky top-0 border-b border-neutral-200/80 z-10">
                                <tr>
                                    <th class="py-3 px-4">Tipo</th>
                                    <th class="py-3 px-4">Método de Pago</th>
                                    <th class="py-3 px-4">Producto / Motivo</th>
                                    <th class="py-3 px-4 text-center">Cant.</th>
                                    <th class="py-3 px-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody id="tablaMovimientosBody" class="divide-y divide-neutral-100 text-xs font-medium text-neutral-700 bg-white">
                                <!-- Se renderiza con JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Footer Modal --}}
            <div class="p-4 border-t border-neutral-200/80 bg-neutral-50/50 flex justify-end shrink-0">
                <button type="button" onclick="cerrarModalDetalle()" class="bg-neutral-200/80 hover:bg-neutral-300/80 text-neutral-700 font-bold px-5 py-2 rounded-xl text-xs transition-colors cursor-pointer">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</x-app-container>
@endsection

@push('scripts')
<script src="{{ asset('js/components/caja-historial.js') }}"></script>
@endpush