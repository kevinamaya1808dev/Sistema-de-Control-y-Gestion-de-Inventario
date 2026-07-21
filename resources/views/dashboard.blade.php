@extends('layouts.app')

@section('title', 'SCGI - Dashboard')
@section('header_title', 'Panel General')

@section('content')
<div class="space-y-6">

    <!-- BREADCRUMB & HEADER + SELECTOR DE MES -->
    <div class="flex flex-col gap-1">
        <nav class="text-xs font-semibold text-slate-400">
            SCGI <span class="mx-1.5 text-slate-300 dark:text-slate-700">/</span> <span class="text-slate-600 dark:text-slate-300 font-bold">Dashboard</span>
        </nav>
        <div class="mt-1 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Bienvenido, {{ Auth::user()->name ?? 'Colaborador' }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 capitalize">
                    {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>

            <!-- SELECTOR DE MES (ENVÍO AUTOMÁTICO AL CAMBIAR) -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 bg-white dark:bg-slate-900 p-2 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs w-full sm:w-auto">
                <label for="month" class="text-xs font-bold text-slate-500 dark:text-slate-400 pl-2">Mes:</label>
                <input type="month" name="month" id="month" value="{{ $selectedMonth }}" 
                    onchange="this.form.submit()"
                    class="text-xs font-bold bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-1.5 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 w-full sm:w-auto">
            </form>
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
                <div class="text-xs text-rose-900 dark:text-rose-300 flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                    <span class="px-2 py-0.5 rounded bg-white dark:bg-rose-900/60 border border-rose-200 dark:border-rose-800 font-mono text-[10px] font-bold text-rose-700 dark:text-rose-300 w-fit">{{ $product->sku ?? 'N/D' }}</span>
                    <span class="font-bold">{{ $product->name ?? 'Producto' }}</span>
                    <span class="text-rose-500 dark:text-rose-400">— stock actual:</span>
                    <span class="font-extrabold">{{ $product->stock ?? 0 }} pza</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- TARJETAS DE MÉTRICAS (GRID ADAPTATIVO 1 A 5 COLUMNAS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Valor Total del Inventario -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">$</div>
                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-500/20 uppercase tracking-wider">Sistema</span>
            </div>
            <div>
                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">${{ number_format($totalInventoryValue ?? 0, 2) }}</span>
                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Valor total del inventario</p>
            </div>
        </div>

        <!-- Dinero Total en Ventas del Mes -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-emerald-200 dark:border-emerald-900/40 shadow-2xs flex flex-col justify-between bg-gradient-to-b from-emerald-50/20 dark:from-emerald-950/20 to-white dark:to-slate-900">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-500/20 uppercase tracking-wider">Ventas Mes</span>
            </div>
            <div>
                <span class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">${{ number_format($totalSalesMoney ?? 0, 2) }}</span>
                <p class="text-xs font-bold text-slate-900 dark:text-slate-200 mt-1">Dinero total en ventas</p>
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
                <span class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 tracking-tight">{{ isset($lowStockProducts) ? $lowStockProducts->count() : 0 }}</span>
                <p class="text-xs font-bold text-slate-900 dark:text-slate-200 mt-1">Productos con stock bajo</p>
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
                <span class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $totalProductsCount ?? 0 }}</span>
                <p class="text-xs font-bold text-slate-900 dark:text-slate-200 mt-1">Productos registrados</p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-2">{{ $activeSkusCount ?? 0 }} SKUs activos</p>
            </div>
        </div>

        <!-- PRODUCTO MÁS VENDIDO DEL MES -->
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-amber-200 dark:border-amber-900/40 shadow-2xs flex flex-col justify-between bg-gradient-to-b from-amber-50/20 dark:from-amber-950/20 to-white dark:to-slate-900 sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.690h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.690l1.519-4.674z"/></svg>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[9px] font-black bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-500/20 uppercase tracking-wider">Top Mes</span>
            </div>
            <div>
                <span class="text-sm font-black text-slate-900 dark:text-white tracking-tight truncate block">
                    {{ $mostSoldProduct->product->name ?? 'Sin ventas en el mes' }}
                </span>
                <p class="text-xs font-bold text-amber-600 dark:text-amber-400 mt-0.5">
                    {{ $mostSoldProduct->total_quantity ?? 0 }} unidades vendidas
                </p>
            </div>
        </div>

    </div>

    <!-- SECCIÓN GRÁFICA: PRODUCTOS MÁS VENDIDOS -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Productos Más Vendidos ({{ \Carbon\Carbon::parse($selectedMonth)->locale('es')->isoFormat('MMMM YYYY') }})</h3>
                <p class="text-[11px] text-slate-400">Gráfica de salida de inventario correspondiente al periodo seleccionado</p>
            </div>
        </div>
        <div class="relative h-72 w-full">
            <canvas id="topProductsChart" 
                data-labels="{{ json_encode($chartLabels) }}" 
                data-values="{{ json_encode($chartData) }}">
            </canvas>
        </div>
    </div>

    <!-- TABLA DE ACTIVIDAD RECIENTE OPTIMIZADA Y RESPONSIVA -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Actividad reciente</h3>
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500">
                {{ isset($recentActivities) ? $recentActivities->count() : 0 }} registros
            </span>
        </div>
        
        <!-- VISTA MÓVIL (TARJETAS COMPACTAS) - SE OCULTA EN PANTALLAS MEDIANAS Y GRANDES (sm:hidden) -->
        <div class="block sm:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($recentActivities ?? [] as $activity)
                <div class="p-4 space-y-2 hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-900 dark:text-white text-xs">
                            {{ $activity->product->name ?? 'N/D' }}
                        </span>
                        @if(($activity->type ?? '') === 'entrada')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">ENTRADA</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">SALIDA</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 font-mono">
                        <span>SKU: {{ $activity->product->sku ?? 'N/D' }}</span>
                        <span class="font-bold text-xs {{ ($activity->type ?? '') === 'entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                            {{ ($activity->type ?? '') === 'entrada' ? '+' : '-' }}{{ $activity->quantity ?? 0 }} pza(s)
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800/60">
                        <span>{{ $activity->created_at ? $activity->created_at->isoFormat('DD MMM YYYY, HH:mm') : '' }}</span>
                        <span class="font-medium text-slate-600 dark:text-slate-300">Por: {{ $activity->user->name ?? 'Sistema' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 dark:text-slate-500 text-xs">
                    No hay registros de actividad recientes en la base de datos.
                </div>
            @endforelse
        </div>

        <!-- VISTA DE TABLA TRADICIONAL - OCULTA EN MÓVILES PEQUEÑOS (hidden sm:block) -->
        <div class="hidden sm:block overflow-x-auto w-full">
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
                            <td class="py-4 px-6 text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                {{ $activity->created_at ? $activity->created_at->isoFormat('DD MMM YYYY') : '' }}
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-900 dark:text-white">
                                {{ $activity->product->name ?? 'N/D' }}
                            </td>
                            <td class="py-4 px-6 font-mono text-[11px] text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                {{ $activity->product->sku ?? 'N/D' }}
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                @if(($activity->type ?? '') === 'entrada')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">ENTRADA</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">SALIDA</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-bold whitespace-nowrap {{ ($activity->type ?? '') === 'entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                                {{ ($activity->type ?? '') === 'entrada' ? '+' : '-' }}{{ $activity->quantity ?? 0 }}
                            </td>
                            <td class="py-4 px-6 text-right font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/components/dashboard-chart.js') }}"></script>
@endpush