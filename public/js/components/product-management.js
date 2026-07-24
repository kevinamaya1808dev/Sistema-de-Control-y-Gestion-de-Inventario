document.addEventListener('alpine:init', () => {
    Alpine.data('productManagement', () => ({
        modals: {
            create: false,
            edit: false,
            delete: false
        },
        formEdit: {
            id: '',
            name: '',
            category_id: '',
            price: '',
            stock: '',
            sku: ''
        },
        formDelete: {
            id: ''
        },

        // --- MANEJO DE TALLAS Y DESGLOSE ---
        showSizes: false,
        globalStock: '',
        tallasList: [
            { talla: '22', active: false, stock: '' },
            { talla: '22.5', active: false, stock: '' },
            { talla: '23', active: false, stock: '' },
            { talla: '23.5', active: false, stock: '' },
            { talla: '24', active: false, stock: '' },
            { talla: '24.5', active: false, stock: '' },
            { talla: '25', active: false, stock: '' },
            { talla: '25.5', active: false, stock: '' },
            { talla: '26', active: false, stock: '' },
            { talla: '26.5', active: false, stock: '' },
            { talla: '27', active: false, stock: '' },
            { talla: '27.5', active: false, stock: '' },
            { talla: '28', active: false, stock: '' },
            { talla: '28.5', active: false, stock: '' },
            { talla: '29', active: false, stock: '' }
        ],

        get totalTallasAssigned() {
            return this.tallasList.reduce((sum, item) => {
                return item.active && !isNaN(parseInt(item.stock)) ? sum + parseInt(item.stock) : sum;
            }, 0);
        },

        getMaxForSize(index) {
            let currentStock = parseInt(this.tallasList[index].stock) || 0;
            let otherStock = this.totalTallasAssigned - currentStock;
            return Math.max(0, (parseInt(this.globalStock) || 0) - otherStock);
        },

        validateSizeInput(index) {
            let item = this.tallasList[index];
            let maxAllowed = this.getMaxForSize(index);
            if (parseInt(item.stock) > maxAllowed) {
                item.stock = maxAllowed;
            }
        },

        openCreateModal() {
            this.globalStock = '';
            this.showSizes = false;
            this.tallasList.forEach(t => { t.active = false; t.stock = ''; });
            this.resetImagePreview('create');
            this.modals.create = true;
            this.$dispatch('open-modal', 'create');
        },

        openEditModal(id, name, categoryId, price, stock, sku, imageUrl) {
            this.formEdit.id = id;
            this.formEdit.name = name;
            this.formEdit.category_id = categoryId;
            this.formEdit.price = price;
            this.formEdit.stock = stock;
            this.formEdit.sku = sku;
            this.showSizes = false;

            const preview = document.getElementById('image-preview-edit');
            const prompt = document.getElementById('upload-prompt-edit');
            const fileUpload = document.getElementById('file-upload-edit');

            if (fileUpload) fileUpload.value = '';

            if (preview) {
                if (imageUrl && imageUrl !== '') {
                    preview.src = imageUrl;
                    preview.classList.remove('hidden');
                    if (prompt) prompt.classList.add('hidden');
                } else {
                    preview.src = '';
                    preview.classList.add('hidden');
                    if (prompt) prompt.classList.remove('hidden');
                }
            }

            this.modals.edit = true;
            this.$dispatch('open-modal', 'edit');
        },

        openDeleteModal(id) {
            this.formDelete.id = id;
            this.modals.delete = true;
            this.$dispatch('open-modal', 'delete');
        },

        closeModal(type) {
            this.modals[type] = false;
            this.showSizes = false;
            this.$dispatch('close-modal', type);

            if (type === 'create' || type === 'edit') {
                this.resetImagePreview(type);
            }
        },

        resetImagePreview(type) {
            const suffix = type === 'create' ? '' : '-edit';
            const preview = document.getElementById(`image-preview${suffix}`);
            const prompt = document.getElementById(`upload-prompt${suffix}`);
            const fileUpload = document.getElementById(`file-upload${suffix}`);

            if (fileUpload) fileUpload.value = '';
            if (preview) {
                preview.src = '';
                preview.classList.add('hidden');
            }
            if (prompt) prompt.classList.remove('hidden');
        },

        previewFile(event, type) {
            const suffix = type === 'create' ? '' : '-edit';
            const preview = document.getElementById(`image-preview${suffix}`);
            const prompt = document.getElementById(`upload-prompt${suffix}`);

            const file = event.target.files[0];
            if (!file || !preview) return;

            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (prompt) prompt.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    }));
});