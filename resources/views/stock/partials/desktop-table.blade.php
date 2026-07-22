<div class="hidden md:block overflow-x-auto">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr>
                <th class="table-th w-24">ID</th>
                <th class="table-th w-20">Imagen</th>
                <th class="table-th">Producto</th>
                <th class="table-th w-28">Talla</th>
                <th class="table-th w-32">Tipo</th>
                <th class="table-th w-32">Cantidad</th>
                <th class="table-th">Motivo</th>
                <th class="table-th w-44">Registrado por</th>
                <th class="table-th w-32">Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
                <tr class="table-tr"
                    x-show="(!searchQuery || '{{ strtolower(addslashes($movement->product->name ?? '')) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($movement->reason ?? '')) }}'.includes(searchQuery.toLowerCase())) && (!selectedTypeFilter || '{{ $movement->type }}' === selectedTypeFilter)">
                    
                    <td class="table-td">
                        <span class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800/80 rounded-lg font-mono text-[11px] font-bold text-slate-500 dark:text-slate-400">
                            #{{ $movement->id }}
                        </span>
                    </td>
                    <td class="table-td">
                        @if($movement->image)
                            <img src="{{ asset('storage/' . $movement->image) }}" alt="Evidencia" class="w-10 h-10 object-cover rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        @else
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">S/N</span>
                        @endif
                    </td>
                    <td class="table-td font-bold text-slate-900 dark:text-white">{{ $movement->product->name ?? 'N/A' }}</td>
                    <td class="table-td font-semibold text-slate-600 dark:text-slate-300">
                        <span class="px-2.5 py-1 bg-indigo-500/10 text-indigo-500 rounded-lg text-xs">{{ $movement->talla ?? 'N/A' }}</span>
                    </td>
                    <td class="table-td">
                        <span class="{{ $movement->type === 'entrada' ? 'badge-emerald' : 'badge-rose' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $movement->type === 'entrada' ? 'bg-emerald-500 shadow-sm shadow-emerald-500' : 'bg-rose-500 shadow-sm shadow-rose-500' }}"></span>
                            {{ ucfirst($movement->type) }}
                        </span>
                    </td>
                    <td class="table-td font-mono font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $movement->type === 'entrada' ? '+' : '-' }}{{ $movement->quantity }} pzas
                    </td>
                    <td class="table-td text-slate-500 dark:text-slate-400 font-medium">{{ $movement->reason }}</td>
                    <td class="table-td font-semibold text-slate-700 dark:text-slate-300">{{ $movement->user->name ?? 'Sistema' }}</td>
                    <td class="table-td text-slate-400 dark:text-slate-500 text-[11px] whitespace-nowrap font-mono">
                        {{ $movement->created_at ? $movement->created_at->format('d/m/Y') : '' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="py-16 text-center text-slate-400 dark:text-slate-500 font-medium">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            <p>No se encontraron movimientos de stock registrados.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>