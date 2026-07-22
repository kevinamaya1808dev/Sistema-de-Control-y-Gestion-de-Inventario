@extends('layouts.app')

@section('title', 'SCGI - Historial de Caja')
@section('header_title', 'Historial de Turnos y Caja')

@section('content')
<x-app-container>
<div class="space-y-8">

    <!-- CABECERA -->
    <div class="flex flex-col gap-1">
        <nav class="text-xs font-bold text-indigo-500 tracking-wide uppercase">
            SCGI <span class="mx-1 text-slate-400 dark:text-slate-600">/</span> <span class="text-slate-600 dark:text-slate-300">Caja</span>
        </nav>
        <div>
            <h1 class="page-title">Historial de Caja</h1>
            <p class="page-subtitle">Bitácora general de aperturas, cierres y flujos de efectivo de todos los usuarios</p>
        </div>
    </div>

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="table-container">
        
        <!-- Header de la tabla -->
        <div class="p-6 bg-slate-50/50 dark:bg-[#070a11] border-b border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-black text-slate-900 dark:text-white">Registros del Sistema</h2>
                <p class="page-subtitle">Historial maestro en tiempo real</p>
            </div>
            <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-500 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <!-- 1. VISTA DE TARJETAS (MÓVIL < 768px) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800/60">
            @forelse($historial as $movimiento)
                <div class="p-5 space-y-4 hover:bg-indigo-50/30 dark:hover:bg-indigo-500/5 transition-colors">
                    
                    <!-- Usuario y Estado -->
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 text-xs shrink-0 shadow-sm">
                                {{ strtoupper(substr($movimiento->user->name ?? 'U', 0, 2)) }}
                            </div>
                            <span class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                {{ $movimiento->user->name ?? 'N/A' }}
                            </span>
                        </div>

                        <div>
                            @if($movimiento->estado === 'abierta')
                                <span class="badge-emerald">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500"></span>
                                    Abierta
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/20">
                                    Cerrada
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Detalles de Montos -->
                    <div class="grid grid-cols-2 gap-3 bg-slate-100/60 dark:bg-[#070a11] p-3.5 rounded-2xl border border-slate-200/60 dark:border-slate-800">
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Monto Inicial</span>
                            <span class="text-xs font-black text-emerald-600 dark:text-emerald-400">
                                ${{ number_format($movimiento->monto_apertura, 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Monto Cierre</span>
                            <span class="text-xs font-black text-slate-700 dark:text-slate-200">
                                {{ $movimiento->monto_cierre ? '$' . number_format($movimiento->monto_cierre, 2) : '-' }}
                            </span>
                        </div>
                    </div>

                    <!-- Fechas -->
                    <div class="space-y-1.5 text-[11px] text-slate-500 dark:text-slate-400 pt-0.5">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-slate-400">Apertura:</span>
                            <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">
                                {{ $movimiento->fecha_apertura ? \Carbon\Carbon::parse($movimiento->fecha_apertura)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-slate-400">Cierre:</span>
                            <span class="font-mono font-semibold text-slate-700 dark:text-slate-300">
                                {{ $movimiento->fecha_cierre ? \Carbon\Carbon::parse($movimiento->fecha_cierre)->format('d/m/Y H:i') : '-' }}
                            </span>
                        </div>
                    </div>

                </div>
            @empty
                <div class="p-12 text-center text-xs text-slate-400">
                    No hay registros de movimientos en la base de datos.
                </div>
            @endforelse
        </div>

        <!-- 2. VISTA DE TABLA (ESCRITORIO >= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-th">Usuario</th>
                        <th class="table-th">Apertura</th>
                        <th class="table-th">Cierre</th>
                        <th class="table-th">Monto Inicial</th>
                        <th class="table-th">Monto Cierre</th>
                        <th class="table-th text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historial as $movimiento)
                        <tr class="table-tr">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-indigo-500/30 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 text-xs shrink-0">
                                        {{ strtoupper(substr($movimiento->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <span class="font-bold text-slate-900 dark:text-white">
                                        {{ $movimiento->user->name ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="table-td font-mono text-[11px] text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $movimiento->fecha_apertura ? \Carbon\Carbon::parse($movimiento->fecha_apertura)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="table-td font-mono text-[11px] text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $movimiento->fecha_cierre ? \Carbon\Carbon::parse($movimiento->fecha_cierre)->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="table-td font-mono font-bold text-emerald-500 dark:text-emerald-400">
                                ${{ number_format($movimiento->monto_apertura, 2) }}
                            </td>
                            <td class="table-td font-mono font-bold text-slate-800 dark:text-slate-200">
                                {{ $movimiento->monto_cierre ? '$' . number_format($movimiento->monto_cierre, 2) : '-' }}
                            </td>
                            <td class="table-td text-center whitespace-nowrap">
                                @if($movimiento->estado === 'abierta')
                                    <span class="badge-emerald">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-500"></span>
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-500/10 text-slate-500 dark:text-slate-400 border border-slate-500/20">
                                        Cerrada
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400 dark:text-slate-500 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p>No hay registros de movimientos en la base de datos.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        @if(isset($historial) && method_exists($historial, 'hasPages') && $historial->hasPages())
            <div class="p-4 border-t border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-[#070a11]">
                {{ $historial->links() }}
            </div>
        @endif
    </div>

</div>
</x-app-container>
@endsection