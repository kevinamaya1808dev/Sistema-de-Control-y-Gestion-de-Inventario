document.addEventListener('alpine:init', () => {
    Alpine.data('categoryManagement', () => ({
        modals: { 
            create: false, 
            edit: false, 
            delete: false 
        },
        formEdit: { 
            id: '', 
            name: '', 
            description: '' 
        },
        formDelete: { 
            id: '' 
        },
        openCreateModal() { 
            this.modals.create = true; 
        },
        openEditModal(id, name, description) {
            this.formEdit.id = id;
            this.formEdit.name = name;
            this.formEdit.description = description;
            this.modals.edit = true;
        },
        openDeleteModal(id) {
            this.formDelete.id = id;
            this.modals.delete = true;
        }
    }));
});