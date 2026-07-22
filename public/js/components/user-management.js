document.addEventListener('DOMContentLoaded', () => {
    // 1. Configuración global de alertas flotantes SweetAlert2 (Toasts)
    window.notify = (icon, title) => {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            background: '#1a1a1a',
            color: '#fff'
        });
        Toast.fire({ icon, title });
    };

    // Dispara automáticamente Toasts si Laravel envía variables de sesión flash
    if (window.sessionSuccess) {
        window.notify('success', window.sessionSuccess);
    }
    if (window.sessionError) {
        window.notify('error', window.sessionError);
    }
});

// 2. Componente Alpine.js para la Gestión de Usuarios
document.addEventListener('alpine:init', () => {
    Alpine.data('userManagement', () => ({
        // Sincronizado con <x-modal name="user">
        modals: {
            user: false
        },
        isEditMode: false,
        
        // Estado inicial de datos del modal (rol 2 asignado por defecto a nuevos)
        currentUser: { 
            id: null, 
            name: '', 
            email: '', 
            role_id: 2, 
            is_active: true 
        },
        currentUserPerms: [],
        
        // Cadenas limpias para filtros
        searchQuery: '',
        selectedRoleFilter: '',

        // Contador reactivo de permisos seleccionados
        get selectedPermsCount() {
            return Array.isArray(this.currentUserPerms) ? this.currentUserPerms.length : 0;
        },

        // Modal para CREAR usuario
        openCreateModal() {
            this.isEditMode = false;
            this.currentUser = { 
                id: null, 
                name: '', 
                email: '', 
                role_id: 2, 
                is_active: true 
            };
            this.currentUserPerms = [];
            this.modals.user = true;
        },

        // Modal para EDITAR usuario
        setUserData(user, userPermsIds) {
            if (!user) return;
            this.isEditMode = true;
            
            this.currentUser = {
                id: user.id ? parseInt(user.id, 10) : null,
                name: user.name || '',
                email: user.email || '',
                role_id: user.role_id ? parseInt(user.role_id, 10) : 2,
                is_active: user.is_active === undefined ? true : Boolean(Number(user.is_active))
            };
            
            // Forzamos arreglos de números enteros para los checkboxes
            this.currentUserPerms = Array.isArray(userPermsIds) 
                ? userPermsIds.map(id => parseInt(id, 10)) 
                : [];
            
            this.modals.user = true;
        },

        // Método genérico para cerrar modales compatible con <x-modal>
        closeModal(name) {
            if (this.modals[name] !== undefined) {
                this.modals[name] = false;
            }
        },

        // Confirmación para eliminar usuario con SweetAlert2
        confirmDelete(event, userId) {
            // Protección reactiva defensiva: Previene intentar eliminar al Super Admin ID 1
            if (userId === 1) {
                window.notify('error', 'El Super Administrador principal no puede ser eliminado.');
                return;
            }

            const form = event.target;
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Realmente deseas eliminar este usuario? Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5', // Indigo 600
                cancelButtonColor: '#f43f5e',  // Rose 500
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                background: '#1a1a1a',
                color: '#ffffff',
                customClass: {
                    popup: 'rounded-2xl border border-slate-800 shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    }));
});