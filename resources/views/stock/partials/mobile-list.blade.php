<div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800/60">
    @forelse($movements as $movement)
        <div class="p-4 space-y-3 hover:bg-indigo-50/40 dark:hover:bg-indigo-500/5 transition-colors"
             x-show="(!searchQuery || '{{ strtolower(addslashes($movement->product->name ?? '')) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($movement->reason ?? '')) }}'.includes(searchQuery.toLowerCase())) && (!selectedTypeFilter || '{{ $movement->type }}' === selectedTypeFilter)">
            
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800/80 rounded-lg font-mono text-[11px] font-bold text-slate-500 dark:text-slate-400 shrink-0">
                        #{{ $movement->id }}
                    </span>
                    <span class="{{ $movement->type === 'entrada' ? 'badge-emerald' : 'badge-rose' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $movement->type === 'entrada' ? 'bg-emerald-500 shadow-sm shadow-emerald-500' : 'bg-rose-500 shadow-sm shadow-rose-500' }}"></span>
                        {{ ucfirst($movement->type) }}
                    </span>
                </div>

                <span class="font-mono font-bold text-xs text-indigo-600 dark:text-indigo-400 shrink-0">
                    {{ $movement->type === 'entrada' ? '+' : '-' }}{{ $movement->quantity }} pzas
                </span>
            </div>

            <div class="flex items-center gap-3">
                @if($movement->image)
                    <img src="{{ asset('storage/' . $movement->image) }}" alt="Evidencia" class="w-12 h-12 object-cover rounded-xl border border-slate-200 dark:border-slate-800 shrink-0 shadow-sm">
                @endif
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $movement->product->name ?? 'N/A' }}</div>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-500 rounded-md text-[10px] font-semibold">Talla: {{ $movement->talla ?? 'N/A' }}</span>
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">{{ $movement->reason }}</div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1 text-[11px] border-t border-slate-100 dark:border-slate-800/40">
                <span class="font-semibold text-slate-600 dark:text-slate-300">
                    Reg: <span class="text-slate-400 dark:text-slate-500 font-normal">{{ $movement->user->name ?? 'Sistema' }}</span>
                </span>
                <span class="text-slate-400 dark:text-slate-500 font-mono">
                    {{ $movement->created_at ? $movement->created_at->format('d/m/Y') : '' }}
                </span>
            </div>
        </div>
    @empty
        <div class="py-16 text-center text-slate-400 dark:text-slate-500 font-medium px-4">
            <div class="flex flex-col items-center justify-center gap-2">
                <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                <p>No se encontraron movimientos de stock registrados.</p>
            </div>
        </div>
    @endforelse
</div>