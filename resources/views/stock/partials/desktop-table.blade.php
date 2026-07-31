<table class="w-full text-left border-collapse">
    <thead>
        <tr class="border-b border-neutral-200/80 bg-neutral-50/80 text-[11px] font-extrabold uppercase tracking-wider text-neutral-400">
            <th class="py-3.5 px-6">Fecha</th>
            <th class="py-3.5 px-6">Producto</th>
            <th class="py-3.5 px-6">SKU / Talla</th>
            <th class="py-3.5 px-6 text-center">Tipo</th>
            <th class="py-3.5 px-6 text-center">Cantidad</th>
            <th class="py-3.5 px-6 text-right">Usuario</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-neutral-100 text-xs font-medium text-neutral-600">
        @forelse($movements ?? [] as $movement)
            <tr class="hover:bg-neutral-50/60 transition-colors">
                {{-- Fecha --}}
                <td class="py-4 px-6 whitespace-nowrap text-neutral-400 font-mono text-[11px]">
                    {{ $movement->created_at->format('d M Y') }}
                </td>

                {{-- Producto --}}
                <td class="py-4 px-6 whitespace-nowrap">
                    <span class="font-bold text-neutral-900 block text-sm">{{ $movement->product->name ?? 'N/A' }}</span>
                    <span class="text-[11px] text-neutral-400 block">{{ $movement->reason }}</span>
                </td>

                {{-- SKU / Talla --}}
                <td class="py-4 px-6 whitespace-nowrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-medium bg-neutral-100 text-neutral-600 border border-neutral-200/60">
                        {{ $movement->product->sku ?? 'N/A' }}
                    </span>
                    @if(!empty($movement->talla) && $movement->talla !== 'N/A')
                        <span class="ml-1 text-[11px] font-bold text-neutral-500">({{ $movement->talla }})</span>
                    @endif
                </td>

                {{-- Tipo --}}
                <td class="py-4 px-6 whitespace-nowrap text-center">
                    @if($movement->type === 'entrada')
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            ENTRADA
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200/60">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            SALIDA
                        </span>
                    @endif
                </td>

                {{-- Cantidad --}}
                <td class="py-4 px-6 whitespace-nowrap text-center font-bold text-sm {{ $movement->type === 'entrada' ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $movement->type === 'entrada' ? '+' : '-' }}{{ $movement->quantity }}
                </td>

                {{-- Usuario --}}
                <td class="py-4 px-6 whitespace-nowrap text-right">
                    <div class="inline-flex items-center gap-1.5 text-neutral-700 font-semibold">
                        <span class="w-6 h-6 rounded-full bg-neutral-100 border border-neutral-200 text-neutral-600 flex items-center justify-center text-[10px] font-bold">
                            {{ strtoupper(substr($movement->user->name ?? 'A', 0, 1)) }}
                        </span>
                        {{ $movement->user->name ?? 'admin' }}
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-neutral-400 font-medium">
                    No hay movimientos registrados.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>