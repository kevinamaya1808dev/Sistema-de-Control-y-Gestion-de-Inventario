document.addEventListener('alpine:init', () => {
    Alpine.data('stockManagement', () => ({
        modals: {
            create: false
        },

        // Buscador y Filtros
        searchQuery: '',
        selectedTypeFilter: '',

        // Catálogo de Productos y Tallas
        products: [],
        availableSizes: [],

        // Estado del formulario
        currentMovement: {
            product_id: '',
            type: 'entrada',
            quantity: 1,
            talla: '',
            reason_preset: '',
            reason_custom: ''
        },

        init() {
            // Cargar productos si vienen serializados en data-products del contenedor
            const container = this.$el;
            if (container && container.dataset.products) {
                try {
                    this.products = JSON.parse(container.dataset.products);
                } catch (e) {
                    console.error('Error al parsear productos:', e);
                    this.products = [];
                }
            }
        },

        // --- FILTRADO DE TABLA Y TARJETAS MÓVILES ---
        filterRow(productName = '', reason = '', type = '') {
            const query = (this.searchQuery || '').trim().toLowerCase();
            const typeFilter = this.selectedTypeFilter;

            const matchesSearch = query === '' || 
                                  productName.toLowerCase().includes(query) || 
                                  reason.toLowerCase().includes(query);

            const matchesType = typeFilter === '' || type === typeFilter;

            return matchesSearch && matchesType;
        },

        // --- MANEJO DE MODALES Y CAMPOS ---
        openCreateModal() {
            this.resetForm();
            // Soporte para apertura nativa de Alpine o mediante eventos dispatch
            this.modals.create = true;
            if (window.Alpine) {
                this.$dispatch('open-modal', { name: 'create' });
            }
        },

        closeModal(name) {
            if (this.modals[name] !== undefined) {
                this.modals[name] = false;
            }
            this.$dispatch('close-modal', { name });
        },

        resetForm() {
            this.currentMovement = {
                product_id: '',
                type: 'entrada',
                quantity: 1,
                talla: '',
                reason_preset: '',
                reason_custom: ''
            };
            this.availableSizes = [];
        },

        // --- SELECCIÓN Y CAMBIO DE PRODUCTO / TALLAS ---
        onProductChange(event) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                // Si pasas las tallas dinámicas en el data-sizes del option
                const rawSizes = selectedOption.dataset.sizes;
                if (rawSizes) {
                    try {
                        this.availableSizes = typeof rawSizes === 'string' ? JSON.parse(rawSizes) : rawSizes;
                    } catch (e) {
                        this.availableSizes = [];
                    }
                } else {
                    // Fallback a array vacio para usar las tallas por defecto del select
                    this.availableSizes = [];
                }
            } else {
                this.availableSizes = [];
            }
            this.currentMovement.talla = '';
        },

        // --- GETTERS COMPUTADOS ---
        get finalReason() {
            if (this.currentMovement.reason_preset === 'Otro') {
                return this.currentMovement.reason_custom;
            }
            return this.currentMovement.reason_preset;
        }
    }));
});