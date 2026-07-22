document.addEventListener('alpine:init', () => {
    Alpine.data('stockManagement', () => ({
        // Objeto de modales para sincronizar con <x-modal name="create">
        modals: {
            create: false
        },
        searchQuery: '',
        selectedTypeFilter: '',

        currentMovement: {
            product_id: '',
            type: 'entrada',
            quantity: 1,
            reason_preset: '',
            reason_custom: '',
            precio_unitario: 0,
            monto_recibido: ''
        },

        init() {
            if (window.sessionSuccess) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: window.sessionSuccess,
                    timer: 3000,
                    showConfirmButton: false
                });
            }

            if (window.sessionError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: window.sessionError
                });
            }
        },

        openCreateModal() {
            this.resetForm();
            this.modals.create = true;
        },

        closeModal(name) {
            if (this.modals[name] !== undefined) {
                this.modals[name] = false;
            }
        },

        resetForm() {
            this.currentMovement = {
                product_id: '',
                type: 'entrada',
                quantity: 1,
                reason_preset: '',
                reason_custom: '',
                precio_unitario: 0,
                monto_recibido: ''
            };
        },

        onProductChange(event) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            this.currentMovement.precio_unitario = price;
        },

        get finalReason() {
            if (this.currentMovement.reason_preset === 'Otro') {
                return this.currentMovement.reason_custom;
            }
            return this.currentMovement.reason_preset;
        },

        get totalCobro() {
            const qty = parseFloat(this.currentMovement.quantity) || 0;
            const price = parseFloat(this.currentMovement.precio_unitario) || 0;
            return qty * price;
        },

        get cambioCalculado() {
            const recibido = parseFloat(this.currentMovement.monto_recibido) || 0;
            return recibido - this.totalCobro;
        }
    }));
});