document.addEventListener('alpine:init', () => {
    Alpine.data('categoryManagement', () => ({
        modals: {
            create: false,
            edit: false,
            delete: false
        },
        formEdit: { id: '', name: '', description: '' },
        formDelete: { id: '' },

        openCreateModal() {
            this.modals.create = true;
            // Si usas el evento del x-modal del sistema:
            this.$dispatch('open-modal', 'create');
        },
        openEditModal(id, name, description) {
            this.formEdit = { id, name, description };
            this.modals.edit = true;
            this.$dispatch('open-modal', 'edit');
        },
        openDeleteModal(id) {
            this.formDelete.id = id;
            this.modals.delete = true;
            this.$dispatch('open-modal', 'delete');
        },
        closeModal(name) {
            if (this.modals[name] !== undefined) {
                this.modals[name] = false;
            }
            this.$dispatch('close-modal', name);
        }
    }));
});