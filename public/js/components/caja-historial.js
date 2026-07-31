window.verDetalleCaja = function (id) {
    const modal = document.getElementById('modalDetalle');
    const tbody = document.getElementById('tablaMovimientosBody');
    const modalTitulo = document.getElementById('modalTitulo');

    // Estado de carga inicial en la tabla (colspan="5")
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="p-8 text-center text-neutral-400">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-neutral-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Cargando información del turno...</span>
                    </div>
                </td>
            </tr>`;
    }

    // Mostrar el modal
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    fetch(`/caja/historial/${id}/detalle`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al obtener la información de la caja.');
        }
        return response.json();
    })
    .then(data => {
        const caja = data.caja || {};
        const nombreUsuario = (caja.user && caja.user.name) ? caja.user.name : 'Usuario';
        
        if (modalTitulo) {
            modalTitulo.innerText = `Detalle del Turno #${caja.id || id} (${nombreUsuario})`;
        }

        // Asignar Métricas e Indicadores Financieros
        const montoInicial = parseFloat(caja.monto_apertura || data.monto_apertura) || 0;
        const efectivo = parseFloat(data.total_efectivo) || 0;
        const tarjeta = parseFloat(data.total_tarjeta) || 0;
        const transferencia = parseFloat(data.total_transferencia) || 0;
        const gastos = parseFloat(data.total_gastos) || 0;
        
        // Efectivo físico en la caja (Monto Inicial + Ventas en Efectivo - Gastos)
        const efectivoEnCaja = (montoInicial + efectivo) - gastos;

        const setElementText = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.innerText = value;
        };

        setElementText('detMontoInicial', `$${montoInicial.toFixed(2)}`);
        setElementText('detEfectivo', `$${efectivo.toFixed(2)}`);
        setElementText('detTarjeta', `$${tarjeta.toFixed(2)}`);
        setElementText('detTransferencia', `$${transferencia.toFixed(2)}`);
        setElementText('detGastos', `$${gastos.toFixed(2)}`);
        setElementText('detEsperado', `$${efectivoEnCaja.toFixed(2)}`);

        // Renderizar tabla de movimientos
        if (!tbody) return;
        tbody.innerHTML = '';

        const movimientos = Array.isArray(caja.movimientos) ? caja.movimientos : [];

        if (movimientos.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-6 text-center text-neutral-400 font-medium">
                        No hay movimientos registrados en este turno.
                    </td>
                </tr>`;
            return;
        }

        const fragment = document.createDocumentFragment();

        movimientos.forEach(m => {
            const reason = m.reason || '';
            const esVenta = m.type === 'salida' && reason.startsWith('Venta directa');

            // 1. Badge para el Tipo de Movimiento
            let badgeClass = 'bg-rose-100 text-rose-700 border border-rose-200';
            let tipoTexto = 'Salida / Gasto';

            if (esVenta) {
                badgeClass = 'bg-blue-100 text-blue-700 border border-blue-200';
                tipoTexto = 'Venta';
            } else if (m.type === 'entrada') {
                badgeClass = 'bg-emerald-100 text-emerald-700 border border-emerald-200';
                tipoTexto = 'Entrada / Surtido';
            }

            // 2. Badge de Método de Pago
            const metodoRaw = String(m.payment_method || m.metodo_pago || (esVenta ? 'cash' : 'N/A')).toLowerCase();
            let badgeMetodo = '';

            switch (metodoRaw) {
                case 'card':
                case 'tarjeta':
                    badgeMetodo = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">💳 Tarjeta</span>`;
                    break;
                case 'transfer':
                case 'transferencia':
                    badgeMetodo = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">📲 Transferencia</span>`;
                    break;
                case 'cash':
                case 'efectivo':
                    badgeMetodo = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">💵 Efectivo</span>`;
                    break;
                default:
                    badgeMetodo = `<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-neutral-100 text-neutral-500 border border-neutral-200">-</span>`;
                    break;
            }

            // Referencia o Folio si existe
            const refHtml = (m.reference || m.referencia) 
                ? `<span class="block font-mono text-[10px] text-neutral-500 font-bold mt-1">Ref: <span class="text-neutral-800">${escapeHtml(m.reference || m.referencia)}</span></span>` 
                : '';

            // Nombre del producto con fallbacks
            const productoNombre = (m.product && (m.product.name || m.product.nombre))
                ? (m.product.name || m.product.nombre)
                : (reason || 'Movimiento manual');

            const totalMov = m.total !== undefined && m.total !== null 
                ? parseFloat(m.total) 
                : ((m.quantity || 1) * (m.unit_price || 0));

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-neutral-50/70 border-b border-neutral-100 text-xs transition-colors';
            tr.innerHTML = `
                <td class="p-3">
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold ${badgeClass}">
                        ${tipoTexto}
                    </span>
                </td>
                <td class="p-3">
                    <div class="inline-flex flex-col items-start">
                        ${badgeMetodo}
                        ${refHtml}
                    </div>
                </td>
                <td class="p-3 font-medium text-neutral-800">${escapeHtml(productoNombre)}</td>
                <td class="p-3 text-center text-neutral-600 font-mono font-bold">${m.quantity || 1}</td>
                <td class="p-3 font-mono font-bold text-neutral-900">$${totalMov.toFixed(2)}</td>
            `;
            fragment.appendChild(tr);
        });

        tbody.appendChild(fragment);
    })
    .catch(error => {
        console.error('Error:', error);
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="p-6 text-center text-rose-500 font-bold">
                        Ocurrió un detalle al cargar la información del turno.
                    </td>
                </tr>`;
        }
    });
};

window.cerrarModalDetalle = function () {
    const modal = document.getElementById('modalDetalle');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

function escapeHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        window.cerrarModalDetalle();
    }
});