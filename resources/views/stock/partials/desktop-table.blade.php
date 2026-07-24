<div class="w-full overflow-x-auto min-w-full inline-block align-middle [scrollbar-width:thin]">
    <table class="w-full text-left border-collapse whitespace-nowrap min-w-[650px]">
        <thead>
            <tr class="border-b border-slate-200/80 dark:border-slate-800/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 bg-slate-50/50 dark:bg-slate-900/40">
                <th class="p-3.5 sm:p-4">Producto</th>
                <th class="p-3.5 sm:p-4 text-center">Talla</th>
                <th class="p-3.5 sm:p-4 text-center">Tipo</th>
                <th class="p-3.5 sm:p-4 text-center">Cantidad</th>
                <th class="p-3.5 sm:p-4">Motivo / Notas</th>
                <th class="p-3.5 sm:p-4 text-right">Fecha</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-xs">
            @forelse($movements as $movement)
                @php
                    $rawReason = $movement->reason ?? '';
                    $talla = $movement->talla;
                    $cleanReason = $rawReason;

                    // Si no trajo talla en la columna pero viene pegada en el motivo "(Talla: XX)"
                    if (empty($talla) && preg_match('/\(Talla:\s*([^)]+)\)/i', $rawReason, $matches)) {
                        $talla = trim($matches[1]);
                        $cleanReason = trim(preg_replace('/\(Talla:\s*[^)]+\)/i', '', $rawReason));
                    }
                @endphp
                <tr x-show="filterRow('{{ addslashes($movement->product->name ?? '') }}', '{{ addslashes($cleanReason) }}', '{{ $movement->type }}')"
                    class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                    
                    {{-- PRODUCTO --}}
                    <td class="p-3.5 sm:p-4 font-bold text-slate-800 dark:text-slate-200">
                        <div class="flex items-center gap-2.5">
                            @if(optional($movement->product)->image || optional($movement->product)->imagen)
                                <img src="{{ asset('storage/' . ($movement->product->image ?? $movement->product->imagen)) }}" 
                                     class="w-7 h-7 object-cover rounded-lg border border-slate-700/50 shrink-0">
                            @endif
                            <span class="truncate max-w-[200px]">{{ $movement->product->name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    
                    {{-- TALLA --}}
                    <td class="p-3.5 sm:p-4 text-center font-bold text-indigo-400">
                        {{ $talla ?? '-' }}
                    </td>
                    
                    {{-- TIPO --}}
                    <td class="p-3.5 sm:p-4 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ strtolower($movement->type) === 'entrada' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ strtolower($movement->type) === 'entrada' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                            {{ $movement->type }}
                        </span>
                    </td>
                    
                    {{-- CANTIDAD --}}
                    <td class="p-3.5 sm:p-4 text-center font-black text-slate-800 dark:text-slate-100">
                        {{ $movement->quantity }} <span class="text-[10px] font-normal text-slate-400">prs</span>
                    </td>
                    
                    {{-- MOTIVO / NOTAS --}}
                    <td class="p-3.5 sm:p-4 text-slate-500 dark:text-slate-400 max-w-[220px] truncate">
                        {{ $cleanReason ?: '-' }}
                    </td>
                    
                    {{-- FECHA --}}
                    <td class="p-3.5 sm:p-4 text-right text-slate-400 font-mono text-[11px]">
                        {{ optional($movement->created_at)->format('d/m/Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-400">
                        No hay movimientos registrados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>