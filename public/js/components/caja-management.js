document.addEventListener('DOMContentLoaded', function () {
    window.confirmarCorteCaja = function() {
        const montoInput = document.getElementById('monto_cierre');

        if (!montoInput || !montoInput.value || parseFloat(montoInput.value) < 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo Requerido',
                text: 'Por favor ingresa el monto final para realizar el corte de caja.',
                confirmButtonColor: '#6366f1'
            });
            if (montoInput) montoInput.focus();
            return;
        }

        const esDark = document.documentElement.classList.contains('dark');
        const efectivoFisico = parseFloat(montoInput.value);
        const montoFormateado = new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(efectivoFisico);

        Swal.fire({
            title: '¿Cerrar turno de caja?',
            html: `
                <div class="text-left text-xs space-y-2 mt-2 p-3 rounded-xl ${esDark ? 'bg-slate-800' : 'bg-slate-100'}">
                    <p class="font-bold ${esDark ? 'text-slate-200' : 'text-slate-700'}">
                        Efectivo declarado en cajón: <span class="text-rose-500 font-black">${montoFormateado}</span>
                    </p>
                    <p class="text-[11px] ${esDark ? 'text-slate-400' : 'text-slate-500'}">
                        * Tarjeta y transferencia no se cuentan aquí: se registraron aparte y se concilian con la terminal o el banco.
                    </p>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, cerrar caja',
            cancelButtonText: 'Cancelar',
            background: esDark ? '#0f172a' : '#ffffff',
            color: esDark ? '#f8fafc' : '#0f172a',
            customClass: {
                popup: 'rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formCorteCaja').submit();
            }
        });
    };
});