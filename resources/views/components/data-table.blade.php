<div class="bg-white rounded-2xl border border-neutral-200/80 shadow-xs overflow-hidden">
    
    {{-- Header de la Card --}}
    <div class="p-5 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/50">
        <div>
            <h3 class="text-base font-bold text-neutral-900 tracking-tight">Últimos Movimientos</h3>
            <p class="text-xs text-neutral-500 mt-0.5">Historial de entradas y salidas de almacén</p>
        </div>
        <a href="{{ route('stock.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 px-3 py-1.5 rounded-lg transition-colors border border-orange-200/50">
            Ver todo
        </a>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-neutral-200/80 bg-neutral-50/80 text-[11px] font-extrabold uppercase tracking-wider text-neutral-400">
                    <th scope="col" class="py-3.5 px-6">Fecha</th>
                    <th scope="col" class="py-3.5 px-6">Producto</th>
                    <th scope="col" class="py-3.5 px-6">SKU</th>
                    <th scope="col" class="py-3.5 px-6 text-center">Tipo</th>
                    <th scope="col" class="py-3.5 px-6 text-center">Cantidad</th>
                    <th scope="col" class="py-3.5 px-6 text-right">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 text-xs font-medium text-neutral-600">
                
                {{-- Bucle de Registros (Ejemplo de fila) --}}
                {{-- @foreach($movements as $movement) --}}
                <tr class="hover:bg-neutral-50/60 transition-colors">
                    
                    {{-- Fecha --}}
                    <td class="py-4 px-6 whitespace-nowrap text-neutral-400 font-mono text-[11px]">
                        24 Jul 2026
                    </td>

                    {{-- Producto --}}
                    <td class="py-4 px-6 whitespace-nowrap">
                        <span class="font-bold text-neutral-900 block text-sm">Nike Air Force One</span>
                    </td>

                    {{-- SKU --}}
                    <td class="py-4 px-6 whitespace-nowrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-medium bg-neutral-100 text-neutral-600 border border-neutral-200/60">
                            nk-ng-21
                        </span>
                    </td>

                    {{-- Tipo (Badge Entrada / Salida) --}}
                    <td class="py-4 px-6 whitespace-nowrap text-center">
                        {{-- Ejemplo para SALIDA --}}
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200/60">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            SALIDA
                        </span>

                        {{-- Ejemplo para ENTRADA (Usar cuando sea ingreso) --}}
                        {{-- 
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            ENTRADA
                        </span> 
                        --}}
                    </td>

                    {{-- Cantidad --}}
                    <td class="py-4 px-6 whitespace-nowrap text-center font-bold text-rose-600 text-sm">
                        -1
                    </td>

                    {{-- Usuario --}}
                    <td class="py-4 px-6 whitespace-nowrap text-right">
                        <div class="inline-flex items-center gap-1.5 text-neutral-700 font-semibold">
                            <span class="w-6 h-6 rounded-full bg-neutral-100 border border-neutral-200 text-neutral-600 flex items-center justify-center text-[10px] font-bold">
                                A
                            </span>
                            admin
                        </div>
                    </td>
                </tr>
                {{-- @endforeach --}}

            </tbody>
        </table>
    </div>
</div>