@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="userManagement()">

    <!-- Encabezado con Botón -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span class="text-xs font-semibold text-slate-400">SCGI / <span class="text-slate-600">Usuarios</span></span>
            <h2 class="text-2xl font-bold text-slate-900 mt-2">Gestión de Usuarios</h2>
            <p class="text-sm text-slate-500">Administra roles y permisos de los colaboradores del sistema</p>
        </div>
        <button @click="openCreateModal()" 
                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2 cursor-pointer">
            <span class="text-lg leading-none">+</span> Nuevo usuario
        </button>
    </div>

    <!-- Tarjetas KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-xs">
            <h3 class="text-3xl font-extrabold text-indigo-600">{{ $totalUsers }}</h3>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Total usuarios</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-xs">
            <h3 class="text-3xl font-extrabold text-blue-600">{{ $totalAdmins }}</h3>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Administradores</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-xs">
            <h3 class="text-3xl font-extrabold text-emerald-600">{{ $totalOperators }}</h3>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Operadores</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-xs">
            <h3 class="text-3xl font-extrabold text-teal-600">{{ $activeUsers }}</h3>
            <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mt-1">Activos</p>
        </div>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="relative w-full sm:max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">🔍</span>
                <input type="text" placeholder="Buscar por nombre o correo..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all">
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                <select class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-sm text-slate-600 focus:outline-hidden focus:border-indigo-500">
                    <option>Todos los roles</option>
                </select>
                <span class="text-xs text-slate-400 font-medium">{{ $users->count() }} usuarios</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 text-slate-400 text-xxs font-bold uppercase border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4">Usuario</th>
                        <th class="px-6 py-4">Correo</th>
                        <th class="px-6 py-4">Rol</th>
                        <th class="px-6 py-4">Permisos activados</th>
                        <th class="px-6 py-4">Estado</th>
                        <th class="px-6 py-4">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs uppercase">
                                {{ substr($user->name, 0, 2) }}
                            </span>
                            <span class="font-bold text-slate-800">{{ $user->name }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ optional($user->role)->name === 'Administrador' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100' }}">
                                {{ optional($user->role)->name ?? 'Sin Rol' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs text-slate-600 font-bold">

                                
                                    {{ $user->id === 1 ? 'Todos (Master)' : $user->permissions->count() . ' activos' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button @click="openEditModal({{ json_encode($user) }}, {{ json_encode($user->permissions->pluck('id')) }})" 
                                    class="px-4 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-100 transition-colors cursor-pointer">
                                Editar permisos
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DE USUARIOS Y PERMISOS -->
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-xs" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:leave="transition ease-in duration-200"
         style="display: none;">
         
        <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-200 shadow-2xl flex flex-col max-h-[90vh]" @click.away="isModalOpen = false">
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <template x-if="isEditMode">
                        <span class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 font-bold flex items-center justify-center text-sm uppercase" x-text="currentUser.initials"></span>
                    </template>
                    <template x-if="!isEditMode">
                        <span class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-sm">👤</span>
                    </template>
                    <div>
                        <h3 class="font-bold text-slate-900" x-text="isEditMode ? 'Editar Usuario y Permisos' : 'Registrar Nuevo Usuario'"></h3>
                        <p class="text-xs text-slate-400" x-text="isEditMode ? currentUser.email : 'Asigna rol, credenciales y permisos detallados'"></p>
                    </div>
                </div>
                <button @click="isModalOpen = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50 transition-colors cursor-pointer">✕</button>
            </div>

            <form :action="isEditMode ? '/usuarios/' + currentUser.id : '/usuarios'" method="POST" class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="_method" :value="isEditMode ? 'PUT' : 'POST'">

                <div class="p-6 space-y-6 overflow-y-auto flex-1">
                    
                    <!-- Datos Básicos -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Datos Personales</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-500">Nombre Completo</label>
                                <input type="text" name="name" x-model="currentUser.name" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-500">Correo Electrónico</label>
                                <input type="email" name="email" x-model="currentUser.email" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Credenciales -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400" x-text="isEditMode ? 'Actualizar Contraseña (Opcional)' : 'Contraseña de Acceso'"></h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-500">Contraseña</label>
                                <input type="password" name="password" :required="!isEditMode" placeholder="admin123" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-500">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" :required="!isEditMode" placeholder="admin123" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Selección de Rol -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rol del Usuario</h4>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($roles as $role)
                            <label class="relative flex flex-col p-4 bg-white border rounded-2xl cursor-pointer transition-all select-none"
                                   :class="currentUser.role_id == {{ $role->id }} ? 'border-emerald-500 bg-emerald-50/10' : 'border-slate-200 hover:bg-slate-50/50'">
                                <input type="radio" name="role_id" value="{{ $role->id }}" x-model="currentUser.role_id" required class="sr-only">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :class="currentUser.role_id == {{ $role->id }} ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                                    <span class="text-sm font-bold text-slate-800">{{ $role->name }}</span>
                                </div>
                                <span class="text-xxs text-slate-400 mt-1">
                                    {{ $role->name === 'Administrador' ? 'Acceso total al sistema' : 'Acceso limitado a operaciones' }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Estado de la Cuenta -->
                    <div class="p-4 bg-slate-50/50 border border-slate-200/60 rounded-xl flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Cuenta activa</h4>
                            <p class="text-xxs text-slate-400 mt-0.5">Permite que este usuario inicie sesión</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="is_active" value="1" :checked="currentUser.is_active" :disabled="currentUser.id === 1" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-focus:ring-2 peer-focus:ring-indigo-300 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>

                    <!-- Permisos -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Permisos Personalizados</h4>
                            <span class="text-xs font-semibold" :class="currentUser.id === 1 ? 'text-indigo-600 font-bold' : 'text-slate-400'" x-text="currentUser.id === 1 ? 'Acceso Total (ID 1)' : selectedPermsCount + ' activos'"></span>
                        </div>

                        <!-- Advertencia si es el ID 1 -->
                        <div x-show="currentUser.id === 1" class="p-3 bg-indigo-50 border border-indigo-100 rounded-xl text-xs text-indigo-700 font-medium">
                            Este usuario cuenta con el identificador maestro (ID 1). Sus permisos son globales por defecto y no pueden ser revocados.
                        </div>

                        @foreach($permissionsByModule as $module => $modulePermissions)
                        <div class="bg-slate-50/40 border border-slate-100 rounded-xl overflow-hidden">
                            <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 text-xxs font-bold uppercase tracking-wider text-slate-400">
                                {{ $module }}
                            </div>
                            <div class="divide-y divide-slate-100">
                                @foreach($modulePermissions as $permission)
                                <label class="flex items-center justify-between p-3 transition-colors"
                                       :class="currentUser.id === 1 ? 'opacity-75 cursor-not-allowed bg-slate-50/80' : 'cursor-pointer hover:bg-white'">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                               :checked="currentUser.id === 1 || currentUserPerms.includes({{ $permission->id }})"
                                               :disabled="currentUser.id === 1"
                                               @change="togglePerm({{ $permission->id }})"
                                               class="w-4.5 h-4.5 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500 disabled:opacity-50">
                                        <span class="text-xs font-semibold text-slate-700">{{ $permission->name }}</span>
                                    </div>
                                    <span class="text-xxs font-bold" 
                                          :class="(currentUser.id === 1 || currentUserPerms.includes({{ $permission->id }})) ? 'text-indigo-600' : 'text-slate-300'" 
                                          x-text="(currentUser.id === 1 || currentUserPerms.includes({{ $permission->id }})) ? 'ON' : 'OFF'">
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

                <div class="p-6 border-t border-slate-100 flex gap-3 bg-white">
                    <button type="button" @click="isModalOpen = false" class="flex-1 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-bold transition-colors text-sm cursor-pointer text-center">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl font-bold shadow-md transition-colors text-sm cursor-pointer text-center" x-text="isEditMode ? 'Guardar cambios' : 'Registrar usuario'">
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function userManagement() {
        return {
            isModalOpen: false,
            isEditMode: false,
            currentUser: {},
            currentUserPerms: [],
            get selectedPermsCount() {
                return this.currentUserPerms.length;
            },
            openCreateModal() {
                this.isEditMode = false;
                this.currentUser = { id: '', name: '', email: '', role_id: '2', is_active: true };
                this.currentUserPerms = [];
                this.isModalOpen = true;
            },
            openEditModal(user, userPermsIds) {
                this.isEditMode = true;
                this.currentUser = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role_id: user.role_id,
                    is_active: !!user.is_active,
                    initials: user.name.split(' ').map(n => n[0]).join('').substring(0, 2)
                };
                this.currentUserPerms = [...userPermsIds];
                this.isModalOpen = true;
            },
            togglePerm(id) {
                if (this.currentUser.id === 1) return; // Bloqueo extra por seguridad en frontend si es ID 1
                if (this.currentUserPerms.includes(id)) {
                    this.currentUserPerms = this.currentUserPerms.filter(pId => pId !== id);
                } else {
                    this.currentUserPerms.push(id);
                }
            }
        }
    }
</script>
@endsection