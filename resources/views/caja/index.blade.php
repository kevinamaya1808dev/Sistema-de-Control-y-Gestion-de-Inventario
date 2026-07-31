@extends('layouts.app')

@section('header_title', 'Control de Caja y Turno')

@section('content')
<x-app-container x-data="{ openGastoModal: false }">
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

    {{-- Encabezado Principal --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-200/80 dark:border-slate-800/80">
        <div>
            <h1 class="page-title">Movimientos de Caja</h1>
            <p class="page-subtitle">Gestión de apertura de turno, corte de caja y registro de flujo de efectivo.</p>
        </div>
        <div class="flex items-center gap-3 self-start sm:self-auto">
            @if($cajaActiva)
                <button type="button" @click="openGastoModal = true"
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold rounded-xl transition duration-200 shadow-md shadow-rose-600/20 text-xs uppercase tracking-wider cursor-pointer shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4m8-8v16"/></svg>
                    <span>Registrar Gasto</span>
                </button>
                <span class="badge-emerald">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Caja Abierta (#{{ $cajaActiva->id }})
                </span>
            @else
                <span class="badge-rose">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Caja Cerrada
                </span>
            @endif
        </div>
    </div>

    @if(!$cajaActiva)
        {{-- FORMULARIO DE APERTURA --}}
        <div class="max-w-md mx-auto card-base p-0 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-[#070a11]/80">
                <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Abrir Nuevo Turno</h2>
                <p class="text-[11px] text-slate-400 font-bold mt-0.5">Ingresa el monto inicial para comenzar las operaciones.</p>
            </div>
            <form action="{{ route('caja.abrir') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label for="monto_apertura" class="form-label">Monto Inicial en Efectivo ($)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">$</span>
                        <input type="number" step="0.01" min="0" name="monto_apertura" id="monto_apertura" required placeholder="0.00" class="form-input pl-8 font-black text-base">
                    </div>
                </div>
                <div>
                    <label for="observaciones" class="form-label">Observaciones / Notas</label>
                    <textarea name="observaciones" id="observaciones" rows="3" placeholder="Opcional..." class="form-input"></textarea>
                </div>
                <button type="submit" class="btn-primary w-full uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Abrir Caja e Iniciar Turno
                </button>
            </form>
        </div>
    @else
        {{-- LAYOUT PRINCIPAL DE 2 COLUMNAS --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6 items-start">

            {{-- COLUMNA IZQUIERDA: RESUMEN Y CIERRE --}}
            <div class="lg:col-span-4 space-y-5 sm:space-y-6">
                <div class="card-base p-0 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-[#070a11]/80 flex items-center justify-between">
                        <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Resumen de Turno</h2>
                        <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">#{{ $cajaActiva->id }}</span>
                    </div>
                    <div class="p-6 space-y-3 text-xs">
                        <div class="flex justify-between text-slate-500 dark:text-slate-400 font-semibold">
                            <span>Cajero:</span>
                            <span class="font-bold text-slate-800 dark:text-white truncate max-w-[150px] text-right">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400 font-semibold">
                            <span>Apertura:</span>
                            <span class="font-bold text-slate-800 dark:text-white">{{ $cajaActiva->fecha_apertura?->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex justify-between text-slate-600 dark:text-slate-300 font-bold">
                            <span>Saldo Inicial:</span>
                            <span class="text-slate-900 dark:text-white">${{ number_format($cajaActiva->monto_apertura, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-emerald-600 dark:text-emerald-400 font-bold">
                            <span>+ Ventas (Efectivo):</span>
                            <span>+${{ number_format($totalVentasEfectivo ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-blue-600 dark:text-blue-400 font-bold">
                            <span>Ventas (Tarjeta):</span>
                            <span>${{ number_format($totalVentasTarjeta ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-indigo-500 dark:text-indigo-300 font-bold">
                            <span>Ventas (Transferencia):</span>
                            <span>${{ number_format($totalVentasTransferencia ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-rose-600 dark:text-rose-400 pb-2 border-b border-slate-100 dark:border-slate-800/80 font-bold">
                            <span>- Salidas / Gastos:</span>
                            <span>-${{ number_format($totalGastos ?? 0, 2) }}</span>
                        </div>
                        <div class="pt-2">
                            <span class="kpi-label">Saldo Físico Estimado (solo efectivo)</span>
                            <span class="kpi-value text-indigo-600 dark:text-indigo-400 mt-1 block">
                                ${{ number_format($saldoEfectivoEnCaja ?? 0, 2) }}
                            </span>
                            <p class="text-[10px] text-slate-400 font-semibold mt-1">
                                Este es el monto que debe existir físicamente en el cajón. Tarjeta y transferencia se concilian aparte.
                            </p>
                        </div>
                    </div>

                    {{-- Formulario de Cierre Integrado --}}
                    <form id="formCorteCaja" action="{{ route('caja.cerrar') }}" method="POST"
                          onsubmit="event.preventDefault(); confirmarCorteCaja();"
                          class="p-6 border-t border-slate-100 dark:border-slate-800/80 space-y-4 bg-slate-50/30 dark:bg-[#070a11]/40">
                        @csrf
                        <div>
                            <label for="monto_cierre" class="form-label">Monto Final en Caja ($)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">$</span>
                                <input type="number" step="0.01" min="0" name="monto_cierre" id="monto_cierre" required placeholder="0.00" class="form-input pl-8 font-black text-sm focus:border-rose-500 focus:ring-rose-500/40">
                            </div>
                        </div>
                        <div>
                            <label for="observaciones_cierre" class="form-label">Notas del Corte</label>
                            <textarea name="observaciones" id="observaciones_cierre" rows="2" placeholder="Sin novedades..." class="form-input focus:border-rose-500 focus:ring-rose-500/40"></textarea>
                        </div>
                        <button type="button" onclick="confirmarCorteCaja()"
                                class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold rounded-2xl transition duration-200 shadow-lg shadow-rose-500/20 flex items-center justify-center gap-2 text-xs uppercase tracking-wider cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            Cerrar Caja
                        </button>
                    </form>
                </div>
            </div>

            {{-- COLUMNA DERECHA: TABLAS DE MOVIMIENTOS --}}
            <div class="lg:col-span-8 space-y-5 sm:space-y-6">

                {{-- VENTAS DEL TURNO --}}
                <div class="table-container">
                    <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between bg-slate-100/60 dark:bg-[#070a11]/80">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Ventas del Turno</h2>
                        </div>
                        <span class="badge-emerald">
                            Total: ${{ number_format($totalVentasGeneral ?? 0, 2) }}
                        </span>
                    </div>

                    {{-- VISTA MÓVIL --}}
                    <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($ventasTurno ?? [] as $venta)
                            @php
                                $montoRecibido = $venta->monto_recibido ?? $venta->amount_received ?? $venta->received ?? 0;
                                $cambio = $venta->cambio ?? $venta->change ?? 0;
                                $totalVenta = $venta->total ?? ($venta->quantity * $venta->unit_price);

                                $metodoLabel = match($venta->payment_method ?? 'cash') {
                                    'card' => 'Tarjeta',
                                    'transfer' => 'Transferencia',
                                    default => 'Efectivo',
                                };
                                $metodoColor = match($venta->payment_method ?? 'cash') {
                                    'card' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                    'transfer' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
                                    default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                };
                            @endphp
                            <div class="p-4 space-y-2 hover:bg-indigo-50/40 dark:hover:bg-indigo-500/5 transition-colors">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400 font-mono text-[11px] font-bold">{{ $venta->created_at?->format('d/m/Y') ?? '-' }}</span>
                                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">
                                        +${{ number_format($totalVenta, 2) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-1 font-medium">
                                    <span>Recibido: <strong class="text-slate-800 dark:text-slate-100 font-bold">${{ number_format($montoRecibido, 2) }}</strong></span>
                                    <span>Cambio: <strong class="{{ $cambio > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }} font-bold">${{ number_format($cambio, 2) }}</strong></span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $metodoColor }}">{{ $metodoLabel }}</span>
                                </div>
                                @if($venta->reference)
                                    <div class="text-[11px] text-slate-400 font-mono pt-1">
                                        Ref: <span class="text-slate-600 dark:text-slate-300 font-bold">{{ $venta->reference }}</span>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs font-bold text-slate-400">
                                No hay ventas registradas en este turno.
                            </div>
                        @endforelse
                    </div>

                    {{-- VISTA DESKTOP --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="table-th">Fecha</th>
                                    <th class="table-th">Total</th>
                                    <th class="table-th">Recibido</th>
                                    <th class="table-th">Cambio</th>
                                    <th class="table-th">Método</th>
                                    <th class="table-th">Referencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ventasTurno ?? [] as $venta)
                                    @php
                                        $montoRecibido = $venta->monto_recibido ?? $venta->amount_received ?? $venta->received ?? 0;
                                        $cambio = $venta->cambio ?? $venta->change ?? 0;
                                        $totalVenta = $venta->total ?? ($venta->quantity * $venta->unit_price);

                                        $metodoLabel = match($venta->payment_method ?? 'cash') {
                                            'card' => 'Tarjeta',
                                            'transfer' => 'Transferencia',
                                            default => 'Efectivo',
                                        };
                                        $metodoColor = match($venta->payment_method ?? 'cash') {
                                            'card' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                                            'transfer' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
                                            default => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                                        };
                                    @endphp
                                    <tr class="table-tr">
                                        <td class="table-td text-slate-400 font-mono text-[11px] font-bold">{{ $venta->created_at?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="table-td font-black text-emerald-600 dark:text-emerald-400">${{ number_format($totalVenta, 2) }}</td>
                                        <td class="table-td font-bold text-slate-800 dark:text-slate-100">${{ number_format($montoRecibido, 2) }}</td>
                                        <td class="table-td font-bold {{ $cambio > 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}">
                                            ${{ number_format($cambio, 2) }}
                                        </td>
                                        <td class="table-td">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $metodoColor }}">{{ $metodoLabel }}</span>
                                        </td>
                                        <td class="table-td text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                            {{ $venta->reference ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-10 text-center text-xs font-bold text-slate-400">
                                            No hay ventas registradas en este turno.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- GASTOS Y SALIDAS --}}
                <div class="table-container">
                    <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between bg-slate-100/60 dark:bg-[#070a11]/80">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Gastos y Salidas</h2>
                        </div>
                        <span class="badge-rose">
                            Total: ${{ number_format($totalGastos ?? 0, 2) }}
                        </span>
                    </div>

                    {{-- VISTA MÓVIL --}}
                    <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800/60">
                        @forelse($gastosTurno ?? [] as $gasto)
                            <div class="p-4 flex items-center justify-between gap-3 hover:bg-rose-50/20 dark:hover:bg-rose-500/5 transition-colors">
                                <div>
                                    <p class="font-bold text-xs text-slate-800 dark:text-white">{{ $gasto->concepto ?? $gasto->observaciones ?? $gasto->reason }}</p>
                                    <span class="text-[10px] text-slate-400 font-mono font-bold">{{ $gasto->created_at?->format('d/m/Y') ?? '-' }}</span>
                                </div>
                                <span class="font-black text-rose-600 dark:text-rose-400 text-sm whitespace-nowrap">
                                    -${{ number_format($gasto->monto ?? $gasto->total, 2) }}
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs font-bold text-slate-400">
                                No hay gastos o salidas registrados en este turno.
                            </div>
                        @endforelse
                    </div>

                    {{-- VISTA DESKTOP --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr>
                                    <th class="table-th">Fecha</th>
                                    <th class="table-th">Concepto / Motivo</th>
                                    <th class="table-th">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gastosTurno ?? [] as $gasto)
                                    <tr class="table-tr">
                                        <td class="table-td text-slate-400 font-mono text-[11px] font-bold">{{ $gasto->created_at?->format('d/m/Y') ?? '-' }}</td>
                                        <td class="table-td font-bold text-slate-800 dark:text-white">{{ $gasto->concepto ?? $gasto->observaciones ?? $gasto->reason }}</td>
                                        <td class="table-td font-black text-rose-600 dark:text-rose-400">-${{ number_format($gasto->monto ?? $gasto->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-10 text-center text-xs font-bold text-slate-400">
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

    {{-- MODAL REGISTRAR GASTO / SALIDA DE DINERO --}}
    <div x-show="openGastoModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="openGastoModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 @click="openGastoModal = false"
                 class="fixed inset-0 transition-opacity bg-slate-900/60 backdrop-blur-xs" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="openGastoModal"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block w-full max-w-md my-8 overflow-hidden text-left align-middle transition-all transform card-base p-0 shadow-2xl">

                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-[#070a11]/80 flex items-center justify-between">
                    <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        Registrar Gasto / Salida de Caja
                    </h2>
                    <button type="button" @click="openGastoModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('caja.gasto') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label for="motivo_gasto" class="form-label">Concepto / Motivo del Gasto</label>
                        <input type="text" name="motivo" id="motivo_gasto" required placeholder="Ej: Bolsas de empaque, Limpieza, Flete, etc." class="form-input">
                    </div>
                    <div>
                        <label for="monto_gasto" class="form-label">Monto del Gasto ($)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 font-bold">$</span>
                            <input type="number" step="0.01" min="0.01" name="monto" id="monto_gasto" required placeholder="0.00" class="form-input pl-8 font-black text-base focus:border-rose-500 focus:ring-rose-500/40">
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="openGastoModal = false" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="py-2.5 px-4 bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-bold rounded-xl text-xs uppercase tracking-wider transition duration-200 shadow-md shadow-rose-600/20">
                            Registrar Gasto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-container>
@endsection

@push('scripts')
<script src="{{ asset('js/components/caja-management.js') }}"></script>
@endpush