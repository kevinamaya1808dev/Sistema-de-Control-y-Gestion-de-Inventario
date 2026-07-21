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
        openCreateModal() {
            this.modals.create = true;
        },
        openEditModal(id, name, categoryId, price, stock, sku) {
            this.formEdit.id = id;
            this.formEdit.name = name;
            this.formEdit.category_id = categoryId;
            this.formEdit.price = price;
            this.formEdit.stock = stock;
            this.formEdit.sku = sku;
            this.modals.edit = true;
        },
        openDeleteModal(id) {
            this.formDelete.id = id;
            this.modals.delete = true;
        },
        closeModal(type) {
            this.modals[type] = false;
            
            // Limpieza e interactividad al cerrar los modales de carga de archivos
            if (type === 'create' || type === 'edit') {
                const suffix = type === 'create' ? '' : '-edit';
                const preview = document.getElementById(`image-preview${suffix}`);
                const prompt = document.getElementById(`upload-prompt${suffix}`);
                const fileUpload = document.getElementById(`file-upload${suffix}`);
                
                if (fileUpload) fileUpload.value = ''; // Resetea el input file
                if (preview) {
                    preview.src = '';
                    preview.classList.add('hidden');
                }
                if (prompt) prompt.classList.remove('hidden');
            }
        },
        previewFile(type) {
            const suffix = type === 'create' ? '' : '-edit';
            
            const preview = document.getElementById(`image-preview${suffix}`);
            const prompt = document.getElementById(`upload-prompt${suffix}`);
            const fileUpload = document.getElementById(`file-upload${suffix}`);
            
            if (!preview || !fileUpload) return;

            const file = fileUpload.files[0];
            const reader = new FileReader();

            reader.addEventListener("load", function () {
                preview.src = reader.result;
                preview.classList.remove('hidden');
                if (prompt) prompt.classList.add('hidden'); // Corregido: ocultamos el prompt por completo
            }, false);

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    }));
});