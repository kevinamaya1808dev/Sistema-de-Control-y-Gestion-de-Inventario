@extends('layouts.app')

@section('title', 'Panel de Administración - SCGI')
@section('header-title', 'Panel de Control')

@section('content')
<div class="w-full space-y-6 pb-12">
    
    <!-- Banner de Bienvenida -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900">¡Bienvenido de nuevo, {{ auth()->user()->name }}!</h2>
            <p class="text-sm text-slate-500">Panel global de supervisión y control gerencial.</p>
        </div>
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700">
            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
            Rol: Administrador
        </span>
    </div>

    <!-- Tarjetas de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Productos</p>
                <h3 class="text-3xl font-bold text-slate-900">{{ $totalProducts }}</h3>
                <p class="text-xs text-slate-500 font-medium">Catálogo activo</p>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Valor de Almacén</p>
                <h3 class="text-3xl font-bold text-slate-900">${{ number_format($inventoryValue, 2) }}</h3>
                <p class="text-xs text-emerald-600 font-semibold">Inversión total</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Bajo Stock</p>
                <h3 class="text-3xl font-bold text-rose-600">{{ $lowStockCount }}</h3>
                <p class="text-xs text-rose-500 font-medium">Requieren reposición</p>
            </div>
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs flex items-center justify-between">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Seguridad</p>
                <h3 class="text-3xl font-bold text-blue-600">Activo</h3>
                <p class="text-xs text-blue-500 font-medium">Sistema blindado</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>

    </div>

    <!-- Secciones Inferiores -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden lg:col-span-2">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Auditoría del Sistema</h3>
                    <p class="text-xs text-slate-500">Monitoreo de acciones de operadores</p>
                </div>
            </div>
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm text-slate-600">
                        <thead class="text-xxs uppercase text-slate-400 bg-slate-50">
                            <tr>
                                <th class="px-4 py-3">Fecha</th>
                                <th class="px-4 py-3">Producto</th>
                                <th class="px-4 py-3">SKU</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Cantidad</th>
                                <th class="px-4 py-3">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($recentMovements as $mov)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-4 text-slate-500">{{ $mov->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-4 font-semibold text-slate-900">{{ $mov->product->name }}</td>
                                    <td class="px-4 py-4"><span class="font-mono text-xxs text-slate-400">{{ $mov->product->sku }}</span></td>
                                    <td class="px-4 py-4">
                                        @if($mov->type === 'entrada')
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xxs bg-emerald-50 text-emerald-600">↗ ENTRADA</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xxs bg-amber-50 text-amber-700">↘ SALIDA</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 font-semibold {{ $mov->type === 'entrada' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $mov->type === 'entrada' ? '+' : '-' }}{{ $mov->quantity }}</td>
                                    <td class="px-4 py-4 text-slate-500">{{ $mov->user->name ?? 'Usuario' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Control Gerencial</h3>
                <p class="text-xs text-slate-500">Herramientas de administración</p>
            </div>
            
            <div class="space-y-2">
                <a href="{{ route('users.index') }}" class="flex items-center gap-3.5 w-full p-3.5 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all border border-slate-100 hover:border-slate-200 group">
                    <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-lg group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </span>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-slate-900">Gestionar Usuarios</p>
                        <p class="text-xxs text-slate-400">Roles y permisos detallados</p>
                    </div>
                </a>
                
                <a href="{{ route('products.index') }}" class="flex items-center gap-3.5 w-full p-3.5 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all border border-slate-100 hover:border-slate-200 group">
                    <span class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </span>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-slate-900">Catálogo de Productos</p>
                        <p class="text-xxs text-slate-400">Administrar stock y precios</p>
                    </div>
                </a>

                <a href="{{ route('categories.index') }}" class="flex items-center gap-3.5 w-full p-3.5 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all border border-slate-100 hover:border-slate-200 group">
                    <span class="p-2.5 bg-amber-50 text-amber-600 rounded-lg group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    </span>
                    <div class="text-left">
                        <p class="text-sm font-semibold text-slate-900">Categorías</p>
                        <p class="text-xxs text-slate-400">Departamentos de abarrotes</p>
                    </div>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection