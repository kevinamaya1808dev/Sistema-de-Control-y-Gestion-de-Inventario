<div class="space-y-3">
    @forelse($movements ?? [] as $movement)
        <div class="p-4 bg-white rounded-2xl border border-neutral-200/80 shadow-xs space-y-3">
            
            {{-- Encabezado: Producto y Estado --}}
            <div class="flex items-start justify-between gap-2">
                <div>
                    <h4 class="font-bold text-neutral-900 text-sm leading-tight">
                        {{ $movement->product->name ?? 'N/A' }}
                    </h4>
                    <p class="text-[11px] text-neutral-500 mt-0.5">
                        {{ $movement->reason }}
                    </p>
                </div>

                {{-- Badge de Entrada/Salida --}}
                <div class="shrink-0">
                    @if($movement->type === 'entrada')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            ENTRADA
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200/60">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            SALIDA
                        </span>
                    @endif
                </div>
            </div>

            {{-- Detalles en Grilla / Filas --}}
            <div class="pt-2 border-t border-neutral-100 grid grid-cols-2 gap-2 text-xs">
                
                {{-- SKU y Talla --}}
                <div>
                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">SKU / Talla</span>
                    <div class="flex items-center gap-1 mt-0.5">
                        <span class="font-mono text-[11px] bg-neutral-100 text-neutral-700 px-1.5 py-0.5 rounded border border-neutral-200/60">
                            {{ $movement->product->sku ?? 'N/A' }}
                        </span>
                        @if(!empty($movement->talla) && $movement->talla !== 'N/A')
                            <span class="font-bold text-neutral-600 text-[11px]">({{ $movement->talla }})</span>
                        @endif
                    </div>
                </div>

                {{-- Cantidad --}}
                <div class="text-right">
                    <span class="text-[10px] font-bold text-neutral-400 uppercase tracking-wider block">Cantidad</span>
                    <span class="font-black text-sm mt-0.5 block {{ $movement->type === 'entrada' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $movement->type === 'entrada' ? '+' : '-' }}{{ $movement->quantity }}
                    </span>
                </div>
            </div>

            {{-- Pie de tarjeta: Fecha y Usuario --}}
            <div class="pt-2 border-t border-neutral-100 flex items-center justify-between text-[11px] text-neutral-400 font-medium">
                <span class="font-mono">
                    {{ $movement->created_at->format('d M Y - H:i') }}
                </span>
                
                <div class="flex items-center gap-1.5 text-neutral-600 font-semibold">
                    <span class="w-5 h-5 rounded-full bg-neutral-100 border border-neutral-200 flex items-center justify-center text-[9px] font-bold">
                        {{ strtoupper(substr($movement->user->name ?? 'A', 0, 1)) }}
                    </span>
                    <span>{{ $movement->user->name ?? 'admin' }}</span>
                </div>
            </div>

        </div>
    @empty
        <div class="p-6 bg-white rounded-2xl border border-neutral-200/80 text-center text-xs text-neutral-400 font-medium">
            No hay movimientos registrados.
        </div>
    @endforelse
</div>