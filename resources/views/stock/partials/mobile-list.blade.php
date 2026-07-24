<div class="space-y-3 w-full max-w-full">
    @forelse($movements as $movement)
        @php
            $rawReason = $movement->reason ?? '';
            $talla = $movement->talla;
            $cleanReason = $rawReason;

            if (empty($talla) && preg_match('/\(Talla:\s*([^)]+)\)/i', $rawReason, $matches)) {
                $talla = trim($matches[1]);
                $cleanReason = trim(preg_replace('/\(Talla:\s*[^)]+\)/i', '', $rawReason));
            }
        @endphp
        <div x-show="filterRow('{{ addslashes($movement->product->name ?? '') }}', '{{ addslashes($cleanReason) }}', '{{ $movement->type }}')"
             class="p-3.5 bg-white dark:bg-[#0b0f19] rounded-2xl border border-slate-200/80 dark:border-slate-800/80 space-y-3 shadow-sm w-full max-w-full box-border">
            
            <div class="flex items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800/60 pb-2.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    @if(optional($movement->product)->image || optional($movement->product)->imagen)
                        <img src="{{ asset('storage/' . ($movement->product->image ?? $movement->product->imagen)) }}" 
                             class="w-9 h-9 object-cover rounded-xl border border-slate-700/50 shrink-0">
                    @else
                        <div class="w-9 h-9 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center text-[9px] text-slate-400 shrink-0 font-bold">
                            TENIS
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h4 class="font-black text-xs text-slate-800 dark:text-slate-100 truncate">
                            {{ $movement->product->name ?? 'Producto no encontrado' }}
                        </h4>
                        <span class="text-[10px] text-slate-400 block font-mono">#{{ $movement->id }}</span>
                    </div>
                </div>

                <span class="shrink-0 px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider {{ strtolower($movement->type) === 'entrada' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                    {{ $movement->type }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/40">
                    <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider">Talla (MX)</span>
                    <span class="font-black text-indigo-400 text-xs">{{ $talla ?? '-' }}</span>
                </div>

                <div class="bg-slate-50 dark:bg-slate-900/60 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/40">
                    <span class="text-[9px] font-bold text-slate-400 block uppercase tracking-wider">Cantidad</span>
                    <span class="font-black text-slate-700 dark:text-slate-200 text-xs">{{ $movement->quantity }} prs</span>
                </div>
            </div>

            <div class="space-y-1 text-xs pt-0.5">
                <div class="flex items-center justify-between text-[11px] gap-2">
                    <span class="text-slate-400 shrink-0">Motivo:</span>
                    <span class="text-slate-700 dark:text-slate-300 font-semibold truncate text-right">{{ $cleanReason ?: 'Sin motivo' }}</span>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1.5 border-t border-slate-100 dark:border-slate-800/40">
                    <span>Por: <strong class="text-slate-600 dark:text-slate-300">{{ $movement->user->name ?? 'Sistema' }}</strong></span>
                    <span class="font-mono">{{ optional($movement->created_at)->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="p-6 text-center text-xs text-slate-400 bg-white dark:bg-[#0b0f19] rounded-2xl border border-slate-200 dark:border-slate-800">
            No hay movimientos registrados.
        </div>
    @endforelse
</div>