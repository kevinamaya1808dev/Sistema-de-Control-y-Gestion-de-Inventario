@extends('layouts.app')

@section('header_title', 'Historial de Caja')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">

    <!-- CABECERA -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white tracking-tight">Historial de Caja</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Bitácora general de aperturas, cierres y flujos de efectivo de todos los usuarios.</p>
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xs border border-slate-200 dark:border-slate-800 overflow-hidden">
        
        <div class="p-4 sm:p-6 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white">Registros del Sistema</h2>
            <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">Historial maestro en tiempo real.</p>
        </div>

        <!-- 1. VISTA DE TARJETAS (SOLO CELULARES: < 768px) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($historial as $movimiento)
                <div class="p-4 space-y-3 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                    
                    <!-- Header de Tarjeta: Usuario y Estado -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400 text-xs flex-shrink-0">
                                {{ substr($movimiento->user->name ?? 'U', 0, 2) }}
                            </div>
                            <span class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                {{ $movimiento->user->name ?? 'N/A' }}
                            </span>
                        </div>

                        <div>
                            @if($movimiento->estado === 'abierta')
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25">
                                    Abierta
                                </span>
                            @else
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/25">
                                    Cerrada
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Detalles: Montos -->
                    <div class="grid grid-cols-2 gap-2 bg-slate-50/75 dark:bg-slate-950/50 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/80">
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Monto Inicial</span>
                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">
                                ${{ number_format($movimiento->monto_apertura, 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Monto Cierre</span>
                            <span class="text-xs font-black text-slate-700 dark:text-slate-300">
                                {{ $movimiento->monto_cierre ? '$' . number_format($movimiento->monto_cierre, 2) : '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="space-y-1 text-[11px] text-slate-500 dark:text-slate-400 pt-0.5">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-slate-400">Apertura:</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">
                                {{ $movimiento->fecha_apertura ? \Carbon\Carbon::parse($movimiento->fecha_apertura)->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-slate-400">Cierre:</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">
                                {{ $movimiento->fecha_cierre ? \Carbon\Carbon::parse($movimiento->fecha_cierre)->format('d/m/Y') : '-' }}
                            </span>
                        </div>
                    </div>

                </div>
            @empty
                <div class="p-8 text-center text-xs text-slate-400">
                    No hay registros de movimientos en la base de datos.
                </div>
            @endforelse
        </div>

        <!-- 2. VISTA DE TABLA (TABLETS Y ESCRITORIO: >= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50/75 dark:bg-slate-950/75 text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Apertura</th>
                        <th class="px-6 py-4">Cierre</th>
                        <th class="px-6 py-4">Monto Inicial</th>
                        <th class="px-6 py-4">Monto Cierre</th>
                        <th class="px-6 py-4 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($historial as $movimiento)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                                {{ $movimiento->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $movimiento->fecha_apertura ? \Carbon\Carbon::parse($movimiento->fecha_apertura)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $movimiento->fecha_cierre ? \Carbon\Carbon::parse($movimiento->fecha_cierre)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                                ${{ number_format($movimiento->monto_apertura, 2) }}
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                                {{ $movimiento->monto_cierre ? '$' . number_format($movimiento->monto_cierre, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($movimiento->estado === 'abierta')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25">
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/25">
                                        Cerrada
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                No hay registros de movimientos en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN RESPONSIVE -->
        @if(isset($historial) && method_exists($historial, 'hasPages') && $historial->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/30">
                {{ $historial->links() }}
            </div>
        @endif
    </div>

</div>
@endsection