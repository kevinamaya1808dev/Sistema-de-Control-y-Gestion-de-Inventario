@extends('layouts.app')

@section('title', 'SCGI - Dashboard')
@section('header_title', 'Panel General')

@section('content')
<div class="space-y-6">

    <!-- BREADCRUMB & HEADER -->
    <div class="flex flex-col gap-1">
        <nav class="text-xs font-semibold text-slate-400">
            SCGI <span class="mx-1.5 text-slate-300 dark:text-slate-700">/</span> <span class="text-slate-600 dark:text-slate-300 font-bold">Dashboard</span>
        </nav>
        <div class="mt-1">
            <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Bienvenido, {{ Auth::user()->name ?? 'Colaborador' }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 capitalize">
                @php
                    \Carbon\Carbon::setLocale('es');
                @endphp
                {{ \Carbon\Carbon::now()->translatedFormat('l, d \de F \de Y') }}
            </p>
        </div>
    </div>

    <!-- ALERTA DE STOCK BAJO -->
    @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
    <div class="bg-rose-50/70 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 rounded-3xl p-6 space-y-3 shadow-2xs">
        <div class="flex items-center gap-2 text-rose-700 dark:text-rose-400 font-extrabold text-xs">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ $lowStockProducts->count() }} productos requieren reposición urgente</span>
        </div>
        <div class="space-y-1.5 pl-6">
            @foreach($lowStockProducts as $product)
                <div class="text-xs text-rose-900 dark:text-rose-300 flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-white dark:bg-rose-900/60 border border-rose-200 dark:border-rose-800 font-mono text-[10px] font-bold text-rose-700 dark:text-rose-300">{{ $product->sku ?? 'N/D' }}</span>
                    <span class="font-bold">{{ $product->name ?? 'Producto' }}</span>
                    <span class="text-rose-500 dark:text-rose-400">— stock actual:</span>
                    <span class="font-extrabold">{{ $product->stock ?? 0 }} pza</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- TARJETAS DE MÉTRICAS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Valor Total del Inventario -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                    $
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/20 uppercase tracking-wider">Sistema</span>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    ${{ number_format($totalInventoryValue ?? 0, 2) }}
                </span>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Valor total del inventario</p>
                <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-2 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Inventario en tiempo real
                </p>
            </div>
        </div>

        <!-- Productos con Stock Bajo -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-rose-200 dark:border-rose-900/40 shadow-2xs flex flex-col justify-between bg-gradient-to-b from-rose-50/20 dark:from-rose-950/20 to-white dark:to-slate-900">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 flex items-center justify-center text-rose-600 dark:text-rose-400 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div>
                <span class="text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">
                    {{ isset($lowStockProducts) ? $lowStockProducts->count() : 0 }}
                </span>
                <p class="text-xs font-bold text-slate-900 dark:text-slate-200 mt-1">Productos con stock bajo</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">Umbral: stock < 5 unidades</p>
            </div>
        </div>

        <!-- Productos Registrados -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div>
                <span class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $totalProductsCount ?? 0 }}
                </span>
                <p class="text-xs font-bold text-slate-900 dark:text-slate-200 mt-1">Productos registrados</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">{{ $activeSkusCount ?? 0 }} SKUs activos en catálogo</p>
            </div>
        </div>

    </div>

    <!-- TABLA DE ACTIVIDAD RECIENTE -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Actividad reciente</h3>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">
                {{ isset($recentActivities) ? $recentActivities->count() : 0 }} registros
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-950/75 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6">Producto</th>
                        <th class="py-4 px-6">SKU</th>
                        <th class="py-4 px-6">Tipo</th>
                        <th class="py-4 px-6">Cantidad</th>
                        <th class="py-4 px-6 text-right">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-600 dark:text-slate-300">
                    @forelse($recentActivities ?? [] as $activity)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-4 px-6 text-slate-400 dark:text-slate-500">
                                {{ $activity->created_at ? $activity->created_at->translatedFormat('d M Y') : '' }}
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900 dark:text-white">
                                {{ $activity->product->name ?? 'N/D' }}
                            </td>
                            <td class="py-4 px-6 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                {{ $activity->product->sku ?? 'N/D' }}
                            </td>
                            <td class="py-4 px-6">
                                @if(($activity->type ?? '') === 'entrada')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                        ↗ ENTRADA
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                                        ↙ SALIDA
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-bold {{ ($activity->type ?? '') === 'entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ ($activity->type ?? '') === 'entrada' ? '+' : '-' }}{{ $activity->quantity ?? 0 }}
                            </td>
                            <td class="py-4 px-6 text-right font-bold text-slate-700 dark:text-slate-300">
                                {{ $activity->user->name ?? 'Sistema' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                No hay registros de actividad recientes en la base de datos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection 