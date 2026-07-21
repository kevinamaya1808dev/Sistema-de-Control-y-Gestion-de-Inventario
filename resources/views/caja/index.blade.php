@extends('layouts.app')

@section('header_title', 'Control de Caja y Turno')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

    {{-- Encabezado Principal --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white tracking-tight">Movimientos de Caja</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Gestión de apertura de turno, corte de caja y registro de flujo de efectivo.</p>
        </div>
        <div class="self-start sm:self-auto">
            @if($cajaActiva)
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Caja Abierta (#{{ $cajaActiva->id }})
                </span>
            @else
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Caja Cerrada
                </span>
            @endif
        </div>
    </div>

    @if(!$cajaActiva)
        {{-- FORMULARIO DE APERTURA --}}
        <div class="max-w-md mx-auto bg-white dark:bg-slate-900 rounded-2xl shadow-2xs border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50">
                <h2 class="text-base font-bold text-slate-800 dark:text-white">Abrir Nuevo Turno</h2>
                <p class="text-xs text-slate-400 mt-0.5">Ingresa el monto inicial para comenzar las operaciones.</p>
            </div>

            <form action="{{ route('caja.abrir') }}" method="POST" class="p-5 sm:p-6 space-y-4">
                @csrf
                <div>
                    <label for="monto_apertura" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Monto Inicial en Efectivo ($)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold">$</span>
                        <input type="number" step="0.01" min="0" name="monto_apertura" id="monto_apertura" required placeholder="0.00"
                            class="pl-8 w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 focus:border-indigo-500 focus:ring-indigo-500 text-slate-800 dark:text-white font-bold text-base py-2.5">
                    </div>
                </div>

                <div>
                    <label for="observaciones" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-2">Observaciones / Notas</label>
                    <textarea name="observaciones" id="observaciones" rows="3" placeholder="Opcional..."
                        class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 focus:border-indigo-500 focus:ring-indigo-500 text-xs text-slate-700 dark:text-slate-300 p-3"></textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold rounded-xl transition duration-150 shadow-xs flex items-center justify-center gap-2 text-xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Abrir Caja e Iniciar Turno
                </button>
            </form>
        </div>
    @else
        {{-- LAYOUT PRINCIPAL DE 2 COLUMNAS --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">
            
            {{-- COLUMNA IZQUIERDA: RESUMEN Y CIERRE --}}
            <div class="lg:col-span-4 space-y-5 sm:space-y-6">
                
                {{-- Tarjeta de Resumen Financiero --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xs border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/50 flex items-center justify-between">
                        <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Resumen de Turno</h2>
                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">#{{ $cajaActiva->id }}</span>
                    </div>

                    <div class="p-4 sm:p-5 space-y-3 text-xs">
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Cajero:</span>
                            <span class="font-bold text-slate-800 dark:text-white truncate max-w-[150px] text-right">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Apertura:</span>
                            <span class="font-medium text-slate-800 dark:text-white">{{ optional($cajaActiva->fecha_apertura)->format('d/m/Y h:i A') }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex justify-between text-slate-600 dark:text-slate-300">
                            <span>Saldo Inicial:</span>
                            <span class="font-bold text-slate-800 dark:text-white">${{ number_format($cajaActiva->monto_apertura, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400">
                            <span>+ Ventas (Efectivo):</span>
                            <span class="font-semibold">+${{ number_format($totalVentasEfectivo ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-rose-600 dark:text-rose-400 pb-2 border-b border-slate-100 dark:border-slate-800">
                            <span>- Salidas / Gastos:</span>
                            <span class="font-semibold">-${{ number_format($totalGastos ?? 0, 2) }}</span>
                        </div>

                        <div class="pt-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Saldo Actual Estimado</span>
                            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">
                                ${{ number_format($cajaActiva->monto_apertura + ($totalVentasEfectivo ?? 0) - ($totalGastos ?? 0), 2) }}
                            </span>
                        </div>
                    </div>

                    {{-- Formulario de Cierre Integrado --}}
                    <form id="formCorteCaja" action="{{ route('caja.cerrar') }}" method="POST" class="p-4 sm:p-5 border-t border-slate-100 dark:border-slate-800 space-y-4 bg-slate-50/30 dark:bg-slate-950/20">
                        @csrf
                        <div>
                            <label for="monto_cierre" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Monto Final en Caja ($)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold">$</span>
                                <input type="number" step="0.01" min="0" name="monto_cierre" id="monto_cierre" required placeholder="0.00"
                                    class="pl-8 w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 focus:border-rose-500 focus:ring-rose-500 text-slate-800 dark:text-white font-bold text-sm py-2">
                            </div>
                        </div>

                        <div>
                            <label for="observaciones_cierre" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1.5">Notas del Corte</label>
                            <textarea name="observaciones" id="observaciones_cierre" rows="2" placeholder="Sin novedades..."
                                class="w-full rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 focus:border-rose-500 focus:ring-rose-500 text-xs text-slate-700 dark:text-slate-300 p-2.5"></textarea>
                        </div>

                        <button type="button" onclick="confirmarCorteCaja()"
                            class="w-full py-2.5 px-4 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white font-semibold rounded-xl transition duration-150 shadow-xs flex items-center justify-center gap-2 text-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            Cerrar Caja
                        </button>
                    </form>
                </div>

            </div>

            {{-- COLUMNA DERECHA: TABLAS DE MOVIMIENTOS (VENTAS Y GASTOS) --}}
            <div class="lg:col-span-8 space-y-5 sm:space-y-6">
                
                {{-- VENTAS DEL TURNO --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xs border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/50">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Ventas del Turno</h2>
                        </div>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-200/50 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                            Total: ${{ number_format($totalVentasEfectivo ?? 0, 2) }}
                        </span>
                    </div>

                    <!-- Vista Celulares: Tarjetas ( < 768px ) -->
                    <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($ventasTurno ?? [] as $venta)
                            <div class="p-3.5 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-mono text-[11px]">{{ $venta->created_at ? $venta->created_at->format('d/m/Y h:i A') : '-' }}</span>
                                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                        +${{ number_format($venta->total ?? ($venta->quantity * $venta->unit_price), 2) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1">
                                    <span>Recibido: <strong class="text-slate-700 dark:text-slate-200">${{ number_format($venta->monto_recibido ?? 0, 2) }}</strong></span>
                                    <span>Cambio: <strong class="{{ ($venta->cambio ?? 0) > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">${{ number_format($venta->cambio ?? 0, 2) }}</strong></span>
                                    <span class="inline-flex px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">Efectivo</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400">
                                No hay ventas registradas en este turno.
                            </div>
                        @endforelse
                    </div>

                    <!-- Vista Escritorio: Tabla ( >= 768px ) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-5 py-3">Fecha</th>
                                    <th class="px-5 py-3">Total</th>
                                    <th class="px-5 py-3">Recibido</th>
                                    <th class="px-5 py-3">Cambio</th>
                                    <th class="px-5 py-3">Método</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($ventasTurno ?? [] as $venta)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="px-5 py-3.5 whitespace-nowrap text-slate-400 font-mono text-[11px]">{{ $venta->created_at ? $venta->created_at->format('d/m/Y h:i A') : '' }}</td>
                                        <td class="px-5 py-3.5 font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($venta->total ?? ($venta->quantity * $venta->unit_price), 2) }}</td>
                                        <td class="px-5 py-3.5 font-semibold text-slate-700 dark:text-slate-200">${{ number_format($venta->monto_recibido ?? 0, 2) }}</td>
                                        <td class="px-5 py-3.5 font-semibold {{ ($venta->cambio ?? 0) > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                            ${{ number_format($venta->cambio ?? 0, 2) }}
                                        </td>
                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-600 border border-emerald-500/20">
                                                Efectivo
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-6 text-center text-slate-400">
                                            No hay ventas registradas en este turno.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- GASTOS Y SALIDAS --}}
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xs border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-950/50">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Gastos y Salidas</h2>
                        </div>
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-200/50 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                            Total: ${{ number_format($totalGastos ?? 0, 2) }}
                        </span>
                    </div>

                    <!-- Vista Celulares: Tarjetas ( < 768px ) -->
                    <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($gastosTurno ?? [] as $gasto)
                            <div class="p-3.5 flex items-center justify-between gap-3">
                                <div>
                                    <p class="font-bold text-xs text-slate-800 dark:text-white">{{ $gasto->concepto ?? $gasto->observaciones }}</p>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $gasto->created_at ? $gasto->created_at->format('d/m/Y h:i A') : '' }}</span>
                                </div>
                                <span class="font-black text-rose-600 dark:text-rose-400 text-sm whitespace-nowrap">
                                    -${{ number_format($gasto->monto, 2) }}
                                </span>
                            </div>
                        @empty
                            <div class="p-6 text-center text-xs text-slate-400">
                                No hay gastos o salidas registrados en este turno.
                            </div>
                        @endforelse
                    </div>

                    <!-- Vista Escritorio: Tabla ( >= 768px ) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                            <thead class="bg-slate-50 dark:bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100 dark:border-slate-800">
                                <tr>
                                    <th class="px-5 py-3">Fecha</th>
                                    <th class="px-5 py-3">Concepto / Motivo</th>
                                    <th class="px-5 py-3">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($gastosTurno ?? [] as $gasto)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                                        <td class="px-5 py-3.5 whitespace-nowrap text-slate-400 font-mono text-[11px]">{{ $gasto->created_at ? $gasto->created_at->format('d/m/Y h:i A') : '' }}</td>
                                        <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-white">{{ $gasto->concepto ?? $gasto->observaciones }}</td>
                                        <td class="px-5 py-3.5 font-bold text-rose-600 dark:text-rose-400">-${{ number_format($gasto->monto, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-6 text-center text-slate-400">
                                            No hay gastos o salidas registrados en este turno.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/components/caja-management.js') }}"></script>
@endpush