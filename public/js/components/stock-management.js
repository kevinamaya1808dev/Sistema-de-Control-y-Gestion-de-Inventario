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

            // Limpiar la talla si cambia el ID del producto
            this.$watch('currentMovement.product_id', (newVal) => {
                if (!newVal) {
                    this.availableSizes = [];
                    this.currentMovement.talla = '';
                }
            });
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
                const rawSizes = selectedOption.dataset.sizes;
                if (rawSizes) {
                    try {
                        this.availableSizes = typeof rawSizes === 'string' ? JSON.parse(rawSizes) : rawSizes;
                    } catch (e) {
                        console.error('Error al parsear tallas:', e);
                        this.availableSizes = [];
                    }
                } else {
                    // Si no trae atributo data-sizes, busca directamente en el objeto local de productos
                    const product = this.products.find(p => p.id == selectedOption.value);
                    this.availableSizes = product ? (product.sizes || product.tallas || []) : [];
                }
            } else {
                this.availableSizes = [];
            }
            
            this.currentMovement.talla = '';
        },

        // --- GETTERS COMPUTADOS ---
        get currentSizeStock() {
            if (!this.currentMovement.talla || this.availableSizes.length === 0) return null;
            
            const selected = this.availableSizes.find(item => {
                if (typeof item === 'object' && item !== null) {
                    return (item.talla || item.name || item.size) === this.currentMovement.talla;
                }
                return item === this.currentMovement.talla;
            });

            if (selected && typeof selected === 'object' && selected.stock !== undefined) {
                return selected.stock;
            }
            
            return null;
        },

        get finalReason() {
            if (this.currentMovement.reason_preset === 'Otro') {
                return this.currentMovement.reason_custom;
            }
            return this.currentMovement.reason_preset;
        }
    }));
});