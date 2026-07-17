@extends('layouts.app')

@section('title', 'Panel Operador - SCGI')
@section('header-title', 'Panel de Control')

@section('content')
<div class="w-full space-y-6 pb-12">
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex items-start justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Bienvenido, {{ $user->name }}</h2>
            <p class="text-sm text-slate-500">Accede a tu historial de salidas (ventas / pérdidas).</p>
        </div>
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">
            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
            Rol: Operador
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs lg:col-span-2">
            <h3 class="text-lg font-bold text-slate-900 mb-4">Listado de Ventas / Pérdidas</h3>

            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm text-slate-600">
                    <thead class="text-xxs uppercase text-slate-400 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3">SKU</th>
                            <th class="px-4 py-3">Cantidad</th>
                            <th class="px-4 py-3">Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($recentOut as $mov)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-4 text-slate-500">{{ $mov->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-4 font-semibold text-slate-900">{{ $mov->product->name }}</td>
                                <td class="px-4 py-4"><span class="font-mono text-xxs text-slate-400">{{ $mov->product->sku }}</span></td>
                                <td class="px-4 py-4 text-rose-600 font-semibold">-{{ $mov->quantity }}</td>
                                <td class="px-4 py-4 text-slate-500">{{ $mov->reason ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">No hay salidas registradas por tu usuario.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs">
            <h4 class="text-sm font-semibold text-slate-900">Atajos</h4>
            <p class="text-xxs text-slate-400 mt-2">Accede rápido al Control de Stock para registrar nuevas salidas.</p>
            <a href="{{ route('movements.index') }}" class="mt-4 inline-block w-full text-center bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl">Ir a Control de Stock</a>
        </div>
    </div>
</div>
@endsection
