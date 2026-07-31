<!-- Ticket Container -->
<div class="max-w-xs mx-auto my-2">
    <div id="printableTicket" 
         class="relative bg-white dark:bg-[#0f172a] border border-slate-200 dark:border-slate-800 rounded-3xl p-5 shadow-2xl space-y-4 font-mono text-xs text-slate-800 dark:text-slate-100 transition-all">
        
        <!-- Muescas decorativas (Solo pantalla) -->
        <div class="no-print absolute -left-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-slate-100 dark:bg-[#070a11] rounded-full border border-slate-200 dark:border-slate-800"></div>
        <div class="no-print absolute -right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 bg-slate-100 dark:bg-[#070a11] rounded-full border border-slate-200 dark:border-slate-800"></div>

        <!-- Encabezado -->
        <div class="text-center space-y-1 pb-3 border-b border-dashed border-slate-300 dark:border-slate-700/80">
            
            <!-- 🛑 Badge decorativo solo en pantalla (Corregido con no-print) -->
            <div class="no-print inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800/80 text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">
                <svg class="w-3 h-3 text-emerald-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                Venta Confirmada
            </div>

            <h3 class="font-black text-base tracking-widest text-slate-900 dark:text-white uppercase">SCGI NEGOCIOS</h3>
            <p class="text-[10px] text-slate-400 font-medium">Sistema de Control y Gestión</p>
            <p class="text-[10px] text-slate-400 font-bold pt-0.5" x-text="new Date().toLocaleString('es-MX', { dateStyle: 'short', timeStyle: 'short' })"></p>
        </div>

        <!-- Lista de Productos -->
        <div class="space-y-2.5 max-h-48 overflow-y-auto pr-1 [scrollbar-width:thin] text-slate-700 dark:text-slate-300">
            <template x-for="(item, index) in (lastTicket.items || [])" :key="index">
                <div class="flex justify-between items-start gap-2 text-xs leading-tight">
                    <div class="flex-1 min-w-0 space-y-0.5">
                        <div class="font-bold truncate text-slate-900 dark:text-white" x-text="item.name + (item.size ? ' (' + item.size + ')' : '')"></div>
                        <div class="text-[10px] text-slate-400 font-medium" x-text="(item.qty || 1) + ' x $' + parseFloat(item.price || 0).toFixed(2)"></div>
                    </div>
                    <span class="font-black shrink-0 text-slate-900 dark:text-white" x-text="'$' + ((parseFloat(item.price) || 0) * (parseInt(item.qty) || 1)).toFixed(2)"></span>
                </div>
            </template>
        </div>

        <!-- Totales y Métodos de Pago -->
        <div class="pt-3 border-t border-dashed border-slate-300 dark:border-slate-700/80 space-y-2">
            <div class="flex justify-between text-slate-500 dark:text-slate-400 text-xs font-bold">
                <span>Subtotal:</span>
                <span class="font-mono" x-text="'$' + (parseFloat(lastTicket.total) || 0).toFixed(2)"></span>
            </div>
            
            <div class="flex justify-between items-center font-black text-base pt-2 border-t border-slate-200 dark:border-slate-800">
                <span class="text-slate-900 dark:text-white">TOTAL:</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-mono text-lg" x-text="'$' + (parseFloat(lastTicket.total) || 0).toFixed(2)"></span>
            </div>

            <template x-if="lastTicket.paymentMethod === 'cash' || lastTicket.paymentMethod === 'efectivo'">
                <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800/60 text-[11px]">
                    <div class="flex justify-between text-slate-500 dark:text-slate-400 font-bold">
                        <span>Efectivo Entregado:</span>
                        <span class="font-mono text-slate-800 dark:text-slate-200" x-text="'$' + (parseFloat(lastTicket.amountReceived) || 0).toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50/50 dark:bg-indigo-500/10 p-1.5 rounded-lg">
                        <span>Cambio:</span>
                        <span class="font-mono" x-text="'$' + (parseFloat(lastTicket.change) || 0).toFixed(2)"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pie de Página -->
        <div class="text-center pt-3 border-t border-dashed border-slate-300 dark:border-slate-700/80 text-[10px] text-slate-400 space-y-0.5">
            <p class="font-bold text-slate-600 dark:text-slate-300">¡Gracias por su compra!</p>
            <p class="text-[9px]">Conserve este ticket para cualquier aclaración o devolución.</p>
        </div>

    </div>
</div>