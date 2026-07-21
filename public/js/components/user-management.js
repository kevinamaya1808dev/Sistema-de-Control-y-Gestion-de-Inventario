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

// 2. Definición del Componente Alpine.js (Modular y encapsulado)
document.addEventListener('alpine:init', () => {
    Alpine.data('userManagement', () => ({
        isModalOpen: false,
        isEditMode: false,
        
        // Estado inicial de datos del modal
        currentUser: { 
            id: null, 
            name: '', 
            email: '', 
            role_id: 1, 
            is_active: true 
        },
        currentUserPerms: [],
        
        // Cadenas vacías limpias para evitar errores de filtrado en el x-show de Blade
        searchQuery: '',
        selectedRoleFilter: '',

        // Getter computado para contar reactivamente los permisos marcados
        get selectedPermsCount() {
            return Array.isArray(this.currentUserPerms) ? this.currentUserPerms.length : 0;
        },

        // Inicializador del formulario en modo Creación
        openCreateModal() {
            this.isEditMode = false;
            this.currentUser = { 
                id: null, 
                name: '', 
                email: '', 
                role_id: 1, 
                is_active: true 
            };
            this.currentUserPerms = [];
            this.isModalOpen = true;
        },

        // Inicializador del formulario en modo Edición (Disparado por el escuchador de eventos)
        setUserData(user, userPermsIds) {
            if (!user) return;
            this.isEditMode = true;
            
            this.currentUser = {
                id: user.id ? parseInt(user.id, 10) : null,
                name: user.name || '',
                email: user.email || '',
                role_id: user.role_id ? parseInt(user.role_id, 10) : 1,
                is_active: user.is_active === undefined ? true : !!user.is_active
            };
            
            // Asegura un arreglo limpio de enteros
            this.currentUserPerms = Array.isArray(userPermsIds) 
                ? userPermsIds.map(id => parseInt(id, 10)) 
                : [];
            
            this.isModalOpen = true;
        },

        // Alterna la presencia de un ID de permiso en el arreglo (Optimizado)
        togglePerm(id) {
            const permId = parseInt(id, 10);
            if (!Array.isArray(this.currentUserPerms)) {
                this.currentUserPerms = [];
            }
            const index = this.currentUserPerms.indexOf(permId);
            if (index > -1) {
                this.currentUserPerms.splice(index, 1);
            } else {
                this.currentUserPerms.push(permId);
            }
        },

        // 3. NUEVO: Método de confirmación de eliminación integrado en el componente
        confirmDelete(event) {
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
                background: '#1a1a1a',         // Coherente con tu tema oscuro
                color: '#ffffff',
                customClass: {
                    popup: 'rounded-2xl border border-slate-800 shadow-xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Si confirma, enviamos el formulario de forma nativa
                }
            });
        }
    }));
});