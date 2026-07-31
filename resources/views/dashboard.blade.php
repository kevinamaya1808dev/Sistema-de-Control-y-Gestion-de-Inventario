@extends('layouts.app')

@section('title', 'SCGI - Dashboard')
@section('header_title', 'Panel General')

@section('content')
<div class="relative w-full max-w-full space-y-4 sm:space-y-6">

    <!-- BREADCRUMB & HEADER + SELECTOR DE MES -->
    <div class="relative flex flex-col gap-2">
        <nav class="text-xs font-semibold text-neutral-400">
            SCGI <span class="mx-1.5 text-neutral-300 dark:text-neutral-700">/</span> <span class="font-bold text-orange-600 dark:text-orange-500">Dashboard</span>
        </nav>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-lg font-black tracking-tight text-neutral-900 dark:text-white sm:text-2xl">Bienvenido, {{ Auth::user()->name ?? 'Colaborador' }}</h1>
                <p class="mt-0.5 text-xs font-medium capitalize text-neutral-500 dark:text-neutral-400">
                    {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                </p>
            </div>

            <!-- SELECTOR DE MES -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex w-full items-center justify-between gap-2 rounded-2xl border border-neutral-200/80 bg-white p-2 shadow-xs dark:border-neutral-800 dark:bg-neutral-900 sm:w-auto sm:justify-start">
                <label for="month" class="pl-2 text-xs font-bold text-neutral-600 dark:text-neutral-300">Mes:</label>
                <input type="month" name="month" id="month" value="{{ $selectedMonth }}" 
                    onchange="this.form.submit()"
                    class="w-full cursor-pointer rounded-xl border border-neutral-200 bg-neutral-50 px-3 py-1.5 text-xs font-bold text-neutral-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 focus:outline-none dark:border-neutral-700 dark:bg-neutral-800 dark:text-neutral-200 sm:w-auto">
            </form>
        </div>
    </div>

    <!-- ALERTA DE STOCK BAJO -->
    @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
    <div class="relative space-y-3 rounded-2xl border border-orange-500/30 bg-orange-500/5 p-4 shadow-xs dark:border-orange-500/20 dark:bg-orange-950/20 sm:rounded-3xl sm:p-6">
        <div class="flex items-center gap-2 text-xs font-extrabold text-orange-700 dark:text-orange-400">
            <svg class="h-4 w-4 shrink-0 text-orange-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ $lowStockProducts->count() }} productos requieren reposición urgente</span>
        </div>
        <div class="space-y-2 sm:pl-6">
            @foreach($lowStockProducts as $product)
                <div class="flex flex-col gap-1 text-xs text-neutral-800 dark:text-neutral-200 sm:flex-row sm:items-center sm:gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-fit rounded-lg border border-orange-500/20 bg-orange-500/10 px-2 py-0.5 font-mono text-[10px] font-bold text-orange-700 dark:border-orange-500/30 dark:bg-orange-950/50 dark:text-orange-300">{{ $product->sku ?? 'N/D' }}</span>
                        <span class="font-bold">{{ $product->name ?? 'Producto' }}</span>
                    </div>
                    <div class="text-[11px] text-neutral-500 dark:text-neutral-400 sm:text-xs">
                        Stock actual: <span class="font-extrabold text-rose-600 dark:text-rose-400">{{ $product->stock ?? 0 }} pza</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- TARJETAS DE MÉTRICAS -->
    <div class="relative grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-5">
        
        <!-- Valor Total del Inventario -->
        <div class="group flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-xs transition-all duration-200 hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700 sm:rounded-3xl sm:p-5">
            <div class="mb-3 flex items-center justify-between">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-100 font-extrabold text-orange-600 dark:bg-orange-950/40 dark:text-orange-400 sm:h-10 sm:w-10 sm:rounded-2xl sm:text-lg">$</div>
                <span class="rounded-xl bg-neutral-100 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">Sistema</span>
            </div>
            <div>
                <span class="text-lg font-black tracking-tight text-neutral-900 dark:text-white sm:text-2xl">${{ number_format($totalInventoryValue ?? 0, 2) }}</span>
                <p class="mt-1 text-xs font-bold text-neutral-500 dark:text-neutral-400">Valor total del inventario</p>
            </div>
        </div>

        <!-- Dinero Total en Ventas del Mes -->
        <div class="group flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-xs transition-all duration-200 hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700 sm:rounded-3xl sm:p-5">
            <div class="mb-3 flex items-center justify-between">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 font-bold text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400 sm:h-10 sm:w-10 sm:rounded-2xl">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="rounded-xl bg-emerald-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">Ventas Mes</span>
            </div>
            <div>
                <span class="text-lg font-black tracking-tight text-emerald-600 dark:text-emerald-400 sm:text-2xl">${{ number_format($totalSalesMoney ?? 0, 2) }}</span>
                <p class="mt-1 text-xs font-bold text-neutral-500 dark:text-neutral-400">Dinero total en ventas</p>
            </div>
        </div>

        <!-- Productos con Stock Bajo -->
        <div class="group flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-xs transition-all duration-200 hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700 sm:rounded-3xl sm:p-5">
            <div class="mb-3 flex items-center justify-between">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-100 font-bold text-orange-600 dark:bg-orange-950/40 dark:text-orange-400 sm:h-10 sm:w-10 sm:rounded-2xl">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <div>
                <span class="text-lg font-black tracking-tight text-orange-600 dark:text-orange-400 sm:text-2xl">{{ isset($lowStockProducts) ? $lowStockProducts->count() : 0 }}</span>
                <p class="mt-1 text-xs font-bold text-neutral-500 dark:text-neutral-400">Productos con stock bajo</p>
            </div>
        </div>

        <!-- Productos Registrados -->
        <div class="group flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-xs transition-all duration-200 hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700 sm:rounded-3xl sm:p-5">
            <div class="mb-3 flex items-center justify-between">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-neutral-100 font-bold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 sm:h-10 sm:w-10 sm:rounded-2xl">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div>
                <span class="text-lg font-black tracking-tight text-neutral-900 dark:text-white sm:text-2xl">{{ $totalProductsCount ?? 0 }}</span>
                <p class="mt-1 text-xs font-bold text-neutral-500 dark:text-neutral-400">Productos registrados</p>
                <p class="mt-1 font-mono text-[10px] font-semibold text-orange-600 dark:text-orange-400 sm:text-[11px]">{{ $activeSkusCount ?? 0 }} SKUs activos</p>
            </div>
        </div>

        <!-- PRODUCTO MÁS VENDIDO DEL MES -->
        <div class="group flex flex-col justify-between rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-xs transition-all duration-200 hover:border-neutral-300 dark:border-neutral-800 dark:bg-neutral-900 dark:hover:border-neutral-700 sm:col-span-2 sm:rounded-3xl sm:p-5 lg:col-span-1">
            <div class="mb-2 flex items-center justify-between">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-orange-100 text-orange-600 dark:bg-orange-950/40 dark:text-orange-400 sm:h-10 sm:w-10 sm:rounded-2xl">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.690h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.690l1.519-4.674z"/></svg>
                </div>
                <span class="rounded-xl bg-orange-50 px-2.5 py-1 text-[9px] font-black uppercase tracking-wider text-orange-700 dark:bg-orange-950/40 dark:text-orange-400">Top Mes</span>
            </div>
            <div>
                <span class="block truncate text-xs font-black tracking-tight text-neutral-900 dark:text-white sm:text-sm">
                    {{ $mostSoldProduct->product->name ?? 'Sin ventas en el mes' }}
                </span>
                <p class="mt-0.5 text-xs font-bold text-orange-600 dark:text-orange-400">
                    {{ $mostSoldProduct->total_quantity ?? 0 }} unidades vendidas
                </p>
            </div>
        </div>

    </div>

    <!-- SECCIÓN GRÁFICA -->
    <div class="relative space-y-4 rounded-2xl border border-neutral-200/80 bg-white p-4 shadow-xs dark:border-neutral-800 dark:bg-neutral-900 sm:rounded-3xl sm:p-6">
        <div>
            <h3 class="text-xs font-black uppercase tracking-wider text-neutral-900 dark:text-white">Productos Más Vendidos ({{ \Carbon\Carbon::parse($selectedMonth)->locale('es')->isoFormat('MMMM YYYY') }})</h3>
            <p class="text-[10px] font-medium text-neutral-400 sm:text-[11px]">Gráfica de salida de inventario correspondiente al periodo seleccionado</p>
        </div>
        <div class="relative h-64 w-full sm:h-80">
            <canvas id="topProductsChart" 
                data-labels="{{ json_encode($chartLabels) }}" 
                data-values="{{ json_encode($chartData) }}">
            </canvas>
        </div>
    </div>

    <!-- TABLA / ACTIVIDAD RECIENTE -->
    <div class="relative overflow-hidden rounded-2xl border border-neutral-200/80 bg-white shadow-xs dark:border-neutral-800 dark:bg-neutral-900 sm:rounded-3xl">
        <div class="flex items-center justify-between border-b border-neutral-100 p-4 dark:border-neutral-800 sm:p-6">
            <h3 class="text-xs font-black uppercase tracking-wider text-neutral-900 dark:text-white">Actividad reciente</h3>
            <span class="text-xs font-bold text-orange-600 dark:text-orange-400">
                {{ isset($recentActivities) ? $recentActivities->count() : 0 }} registros
            </span>
        </div>
        
        <!-- VISTA MÓVIL (TARJETAS APILADAS) -->
        <div class="block divide-y divide-neutral-100 dark:divide-neutral-800 sm:hidden">
            @forelse($recentActivities ?? [] as $activity)
                <div class="space-y-2 p-3.5 transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/50">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-neutral-900 dark:text-white">
                            {{ $activity->product->name ?? 'N/D' }}
                        </span>
                        @if(($activity->type ?? '') === 'entrada')
                            <span class="inline-flex items-center rounded-md border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[9px] font-bold text-emerald-600 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-400">ENTRADA</span>
                        @else
                            <span class="inline-flex items-center rounded-md border border-orange-200 bg-orange-50 px-2 py-0.5 text-[9px] font-bold text-orange-600 dark:border-orange-900/40 dark:bg-orange-950/40 dark:text-orange-400">SALIDA</span>
                        @endif
                    </div>
                    <div class="flex items-center justify-between font-mono text-[11px] text-neutral-500 dark:text-neutral-400">
                        <span>SKU: {{ $activity->product->sku ?? 'N/D' }}</span>
                        <span class="text-xs font-bold {{ ($activity->type ?? '') === 'entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }}">
                            {{ ($activity->type ?? '') === 'entrada' ? '+' : '-' }}{{ $activity->quantity ?? 0 }} pza(s)
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-t border-neutral-100 pt-1 text-[10px] text-neutral-400 dark:border-neutral-800">
                        <span>{{ $activity->created_at ? $activity->created_at->isoFormat('DD MMM YYYY, HH:mm') : '' }}</span>
                        <span class="font-medium text-neutral-600 dark:text-neutral-300">Por: {{ $activity->user->name ?? 'Sistema' }}</span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs font-medium text-neutral-400 dark:text-neutral-500">
                    No hay registros de actividad recientes.
                </div>
            @endforelse
        </div>

        <!-- VISTA DE TABLA (PANTALLAS SM EN ADELANTE) -->
        <div class="hidden w-full overflow-x-auto sm:block">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-neutral-200/80 bg-neutral-50 text-[11px] font-bold uppercase tracking-wider text-neutral-500 dark:border-neutral-800 dark:bg-neutral-900/80 dark:text-neutral-400">
                        <th class="py-4 px-6">Fecha</th>
                        <th class="py-4 px-6">Producto</th>
                        <th class="py-4 px-6">SKU</th>
                        <th class="py-4 px-6">Tipo</th>
                        <th class="py-4 px-6">Cantidad</th>
                        <th class="py-4 px-6 text-right">Usuario</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100 text-xs font-medium text-neutral-600 dark:divide-neutral-800 dark:text-neutral-300">
                    @forelse($recentActivities ?? [] as $activity)
                        <tr class="transition-colors hover:bg-neutral-50 dark:hover:bg-neutral-800/40">
                            <td class="whitespace-nowrap py-4 px-6 font-mono text-[11px] text-neutral-400">
                                {{ $activity->created_at ? $activity->created_at->isoFormat('DD MMM YYYY') : '' }}
                            </td>
                            <td class="py-4 px-6 font-bold text-neutral-900 dark:text-white">
                                {{ $activity->product->name ?? 'N/D' }}
                            </td>
                            <td class="whitespace-nowrap py-4 px-6 font-mono text-[11px] text-neutral-500 dark:text-neutral-400">
                                {{ $activity->product->sku ?? 'N/D' }}
                            </td>
                            <td class="whitespace-nowrap py-4 px-6">
                                @if(($activity->type ?? '') === 'entrada')
                                    <span class="inline-flex items-center gap-1 rounded-xl border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-600 dark:border-emerald-900/40 dark:bg-emerald-950/40 dark:text-emerald-400">ENTRADA</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-xl border border-orange-200 bg-orange-50 px-2.5 py-1 text-[10px] font-bold text-orange-600 dark:border-orange-900/40 dark:bg-orange-950/40 dark:text-orange-400">SALIDA</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap py-4 px-6 font-bold {{ ($activity->type ?? '') === 'entrada' ? 'text-emerald-600 dark:text-emerald-400' : 'text-orange-600 dark:text-orange-400' }}">
                                {{ ($activity->type ?? '') === 'entrada' ? '+' : '-' }}{{ $activity->quantity ?? 0 }}
                            </td>
                            <td class="whitespace-nowrap py-4 px-6 text-right font-bold text-neutral-700 dark:text-neutral-300">
                                {{ $activity->user->name ?? 'Sistema' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-neutral-400 dark:text-neutral-500">
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