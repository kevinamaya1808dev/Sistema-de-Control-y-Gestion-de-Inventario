@extends('layouts.app')

@section('header_title', 'Historial de Caja')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">Historial de Caja</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Bitácora general de aperturas, cierres y flujos de efectivo de todos los usuarios.</p>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-base font-bold text-slate-800 dark:text-white">Registros del Sistema</h2>
            <p class="text-xs text-slate-400 mt-0.5">Historial maestro en tiempo real.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 font-semibold uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-6 py-3.5">Usuario</th>
                        <th class="px-6 py-3.5">Apertura</th>
                        <th class="px-6 py-3.5">Cierre</th>
                        <th class="px-6 py-3.5">Monto Inicial</th>
                        <th class="px-6 py-3.5">Monto Cierre</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($historial as $movimiento)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition duration-150">
                            <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                                {{ $movimiento->user->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $movimiento->fecha_apertura ? \Carbon\Carbon::parse($movimiento->fecha_apertura)->format('d/m/Y h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $movimiento->fecha_cierre ? \Carbon\Carbon::parse($movimiento->fecha_cierre)->format('d/m/Y h:i A') : '-' }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-emerald-600 dark:text-emerald-400">
                                ${{ number_format($movimiento->monto_apertura, 2) }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700 dark:text-slate-300">
                                {{ $movimiento->monto_cierre ? '$' . number_format($movimiento->monto_cierre, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center whitespace-nowrap">
                                @if($movimiento->estado === 'abierta')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/25">
                                        Abierta
                                    </span>
                                @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-500/10 text-slate-400 border border-slate-500/25">
                                        Cerrada
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">
                                No hay registros de movimientos en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($historial) && method_exists($historial, 'hasPages') && $historial->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $historial->links() }}
            </div>
        @endif
    </div>

</div>
@endsection