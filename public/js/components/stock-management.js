document.addEventListener('alpine:init', () => {
    Alpine.data('stockManagement', () => ({
        // Objeto de modales para sincronizar con <x-modal name="create">
        modals: {
            create: false
        },
        searchQuery: '',
        selectedTypeFilter: '',

        selectedProductImage: '', // <--- Variable para almacenar la ruta de la foto del tenis

        currentMovement: {
            product_id: '',
            type: 'entrada',
            quantity: 1,
            reason_preset: '',
            reason_custom: '',
            precio_unitario: 0,
            monto_recibido: '',
            talla: '' // <--- Campo para la talla del calzado
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
                monto_recibido: '',
                talla: ''
            };
            this.selectedProductImage = ''; // <--- Limpiar la imagen al cerrar/reiniciar
        },

        onProductChange(event) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            if (selectedOption && selectedOption.value) {
                // Capturar el precio del producto
                const price = parseFloat(selectedOption.dataset.price) || 0;
                this.currentMovement.precio_unitario = price;

                // Capturar la imagen asignada al producto en el catálogo
                this.selectedProductImage = selectedOption.dataset.image || '';
            } else {
                this.currentMovement.precio_unitario = 0;
                this.selectedProductImage = '';
            }
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