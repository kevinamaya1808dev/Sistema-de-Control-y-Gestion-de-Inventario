document.addEventListener('DOMContentLoaded', function () {
    window.confirmarCorteCaja = function() {
        const montoInput = document.getElementById('monto_cierre');
        
        if (!montoInput || !montoInput.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Campo Requerido',
                text: 'Por favor ingresa el monto final para realizar el corte de caja.',
                confirmButtonColor: '#6366f1'
            });
            return;
        }

        const esDark = document.documentElement.classList.contains('dark');

        Swal.fire({
            title: '¿Cerrar turno de caja?',
            text: 'Se realizará el corte definitivo y se registrará el dinero ingresado.',
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