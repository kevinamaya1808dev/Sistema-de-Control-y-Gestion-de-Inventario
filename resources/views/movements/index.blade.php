@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    @if(optional(Auth::user()->role)->name === 'Operador')
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Bienvenido, {{ Auth::user()->name }}</h1>
                    <p class="text-sm text-slate-500">Accede a tu historial y registra movimientos de stock.</p>
                </div>
                <div class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xxs font-semibold text-emerald-700">Operador</div>
            </div>
        </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-2xl font-bold text-slate-900 mb-3">Control de Stock</h2>
                <p class="text-sm text-slate-500 mb-6">Registra entradas y salidas de mercancía en tiempo real</p>

                <div x-data="movementForm()" class="bg-white">
                    <form :action="formAction" method="POST">
                        @csrf
                        <div class="mb-4 rounded-lg border bg-slate-50 p-1 flex items-center gap-1">
                            <button type="button" :class="type === 'entrada' ? 'bg-emerald-600 text-white' : 'text-slate-600'" @click="type='entrada'" class="px-6 py-2 rounded-md text-sm font-semibold">↗ Entrada</button>
                            <button type="button" :class="type === 'salida' ? 'bg-slate-50 text-slate-600' : 'text-slate-600'" @click="type='salida'" class="px-6 py-2 rounded-md text-sm font-semibold">↘ Salida</button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-2">PRODUCTO *</label>
                                <select name="product_id" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900">
                                    <option value="">— Selecciona un producto —</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} — {{ $p->sku }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-2">CANTIDAD *</label>
                                <input name="quantity" type="number" min="1" value="1" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-2">MOTIVO</label>
                                <select name="reason" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900">
                                    <option value="">— Selecciona el motivo —</option>
                                    @foreach($motives as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="type" :value="type">

                            <div>
                                <button type="submit" class="w-full rounded-md bg-emerald-600 text-white px-4 py-3 font-semibold" x-text="type === 'entrada' ? 'Registrar entrada' : 'Registrar salida'"></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Historial de movimientos</h3>

                <div class="space-y-4">
                    @foreach($movements as $mov)
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="h-9 w-9 rounded-full flex items-center justify-center bg-emerald-100 text-emerald-600 font-semibold">
                                    @if($mov->type === 'entrada') + @else - @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $mov->product->name }} <span class="ml-2 text-xxs font-mono text-slate-400">{{ $mov->product->sku }}</span></div>
                                    <div class="text-sm text-slate-500 mt-1">
                                        <span class="font-semibold">@if($mov->type === 'entrada') +@else -@endif{{ $mov->quantity }}</span>
                                        &nbsp;·&nbsp; {{ $mov->user->name ?? 'Usuario' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-slate-400">{{ $mov->date?->format('d M Y') }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function movementForm() {
        return {
            type: 'entrada',
            formAction: '{{ route('movements.store') }}'
        }
    }
</script>

@endsection
