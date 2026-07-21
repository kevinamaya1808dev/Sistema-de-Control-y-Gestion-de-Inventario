@extends('layouts.app')

@section('title', 'SCGI - Gestión de Usuarios')
@section('header_title', 'Gestión de Usuarios y Permisos')

@section('content')
<!-- Contenedor principal escuchando el evento personalizado edit-user -->
<div class="space-y-6" 
     x-data="userManagement()" 
     @edit-user.window="setUserData($event.detail.user, $event.detail.perms)">

    <!-- HEADER / MIGAS DE PAN Y TÍTULO -->
    <div class="flex flex-col gap-1">
        <nav class="text-xs font-semibold text-slate-400">
            SCGI <span class="mx-1.5 text-slate-300">/</span> <span class="text-slate-600 dark:text-slate-300 font-bold">Usuarios</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-1">
            <div>
                <h1 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Gestión de Usuarios</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Administra roles y permisos de los colaboradores del sistema</p>
            </div>
            <button type="button" 
                    @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nuevo Usuario
            </button>
        </div>
    </div>

    <!-- TARJETAS DE MÉTRICAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total usuarios -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <span class="text-2xl font-black text-indigo-600 dark:text-indigo-400">{{ $totalUsers ?? 0 }}</span>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2">Total usuarios</span>
        </div>
        <!-- Administradores -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <span class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $totalAdmins ?? 0 }}</span>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2">Administradores</span>
        </div>
        <!-- Operadores -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $totalOperators ?? 0 }}</span>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2">Operadores</span>
        </div>
        <!-- Activos -->
        <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col justify-between">
            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $activeUsers ?? 0 }}</span>
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-2">Activos</span>
        </div>
    </div>

    <!-- BARRA DE BÚSQUEDA Y FILTROS -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:max-w-md">
            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </span>
            <input type="text" 
                   x-model="searchQuery"
                   placeholder="Buscar por nombre o correo..." 
                   class="w-full pl-10 pr-4 py-2 bg-slate-50/75 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
            <select x-model="selectedRoleFilter" class="px-4 py-2 bg-slate-50/75 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 focus:outline-none">
                <option value="">Todos los roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <span class="text-xs font-bold text-slate-400 whitespace-nowrap">{{ $totalUsers ?? 0 }} usuarios</span>
        </div>
    </div>

    <!-- TABLA DE USUARIOS -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Usuario</th>
                        <th class="py-4 px-6">Correo</th>
                        <th class="py-4 px-6">Rol</th>
                        <th class="py-4 px-6">Permisos</th>
                        <th class="py-4 px-6">Estado</th>
                        <th class="py-4 px-6">Último acceso</th>
                        <th class="py-4 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-600 dark:text-slate-300">
                    @forelse($users as $user)
                        @php
                            $userRoleName = optional($user->role)->name ?? '';
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors"
                            x-show="(!searchQuery || '{{ strtolower($user->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(searchQuery.toLowerCase())) && (!selectedRoleFilter || '{{ $userRoleName }}' === selectedRoleFilter)">
                            
                            <!-- Usuario (Avatar + Nombre) -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-100/60 dark:bg-emerald-950/40 border border-emerald-200/50 dark:border-emerald-800/50 flex items-center justify-center font-bold text-emerald-700 dark:text-emerald-400 text-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-slate-100 flex items-center gap-1.5">
                                            {{ $user->name }}
                                            @if($user->id === 1)
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-400">SUPER ADMIN</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Correo -->
                            <td class="py-4 px-6 text-slate-500 dark:text-slate-400 font-mono text-[11px]">
                                {{ $user->email }}
                            </td>

                            <!-- Rol -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-bold {{ $userRoleName === 'Administrador' ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-900/50' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    {{ $userRoleName ?: 'Operador' }}
                                </span>
                            </td>

                            <!-- Permisos -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        @php
                                            $permCount = $user->id === 1 ? 7 : $user->permissions->count();
                                            $totalPermsTotal = 7;
                                        @endphp
                                        @for($i = 1; $i <= $totalPermsTotal; $i++)
                                            <span class="w-2 h-2 rounded-full {{ $i <= $permCount ? 'bg-indigo-600 dark:bg-indigo-400' : 'bg-slate-200 dark:bg-slate-700' }}"></span>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ $permCount }}/{{ $totalPermsTotal }}</span>
                                </div>
                            </td>

                            <!-- Estado -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $user->is_active ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-900/50' : 'bg-rose-50 dark:bg-rose-950/50 text-rose-700 dark:text-rose-300 border border-rose-100 dark:border-rose-900/50' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                    {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>

                            <!-- Último acceso -->
                            <td class="py-4 px-6 text-slate-400 text-[11px]">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Hace un momento
                                </span>
                            </td>

                            <!-- Acciones -->
                            <td class="py-4 px-6 text-right space-x-2">
                                <button type="button"
                                        @click="$dispatch('edit-user', { user: {{ json_encode($user) }}, perms: {{ json_encode($user->permissions->pluck('id')->toArray()) }} })"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-50/60 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/50 rounded-xl text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Editar permisos
                                </button>

                                @if($user->id !== 1)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline-block" @submit.prevent="confirmDelete($event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-50 dark:bg-rose-950/40 border border-rose-100 dark:border-rose-900/50 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 transition-colors cursor-pointer" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                No se encontraron usuarios registrados en el sistema.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL ESTILIZADO (CREAR / EDITAR) -->
    <div x-show="isModalOpen" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 w-full max-w-2xl overflow-hidden transform transition-all"
             @click.outside="isModalOpen = false">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 flex items-center justify-center font-bold text-emerald-700 dark:text-emerald-400 text-sm">
                        <span x-text="currentUser.name ? currentUser.name.substring(0, 2).toUpperCase() : 'US'"></span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white" x-text="currentUser.name || 'Nuevo Usuario'"></h3>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-mono mt-0.5" x-text="currentUser.email || 'correo@scgi.mx'"></p>
                    </div>
                </div>
                <button type="button" @click="isModalOpen = false" class="w-8 h-8 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="isEditMode ? '{{ route('users.update', ':id') }}'.replace(':id', currentUser.id) : '{{ route('users.store') }}'" 
                  method="POST" 
                  class="p-8 space-y-6 max-h-[75vh] overflow-y-auto">
                @csrf
                <template x-if="isEditMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- DATOS BÁSICOS -->
                <div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider mb-3">Datos Básicos</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Nombre</label>
                            <input type="text" 
                                   name="name" 
                                   x-model="currentUser.name" 
                                   required
                                   class="w-full px-4 py-2.5 bg-slate-50/75 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Correo</label>
                            <input type="email" 
                                   name="email" 
                                   x-model="currentUser.email" 
                                   required
                                   class="w-full px-4 py-2.5 bg-slate-50/75 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                        </div>
                    </div>
                </div>

                <!-- ROL DEL USUARIO -->
                <div x-show="currentUser.id !== 1">
                    <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider mb-3">Rol del Usuario</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($roles as $role)
                            <label class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                   :class="currentUser.role_id == {{ $role->id }} ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-950/20' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-slate-300 dark:hover:border-slate-700'">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $role->name === 'Administrador' ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <input type="radio" name="role_id" value="{{ $role->id }}" x-model.number="currentUser.role_id" class="sr-only">
                                </div>
                                <span class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $role->name }}</span>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5">{{ $role->name === 'Administrador' ? 'Acceso total al sistema' : 'Acceso limitado a operaciones' }}</span>
                                <span class="mt-2 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1" x-show="currentUser.role_id == {{ $role->id }}">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Seleccionado
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- CONTRASEÑA -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Contraseña <span x-show="isEditMode" class="text-slate-400 dark:text-slate-500 font-normal lowercase">(dejar en blanco para no cambiar)</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           :required="!isEditMode"
                           placeholder="••••••••"
                           class="w-full px-4 py-2.5 bg-slate-50/75 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all">
                </div>

                <!-- ESTADO DE LA CUENTA (TOGGLE) -->
                <div class="p-4 bg-slate-50/75 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-between" x-show="currentUser.id !== 1">
                    <div>
                        <span class="text-xs font-bold text-slate-900 dark:text-slate-100 block">Cuenta activa</span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500">El usuario puede iniciar sesión</span>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" x-model="currentUser.is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <!-- PERMISOS PERSONALIZADOS -->
                <div class="pt-2 border-t border-slate-100 dark:border-slate-800" x-show="currentUser.id !== 1">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider">Permisos personalizados</h4>
                        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500">
                            <span x-text="selectedPermsCount"></span> / 7 activos
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($permissions as $permission)
                            <label class="flex items-center justify-between p-3.5 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 cursor-pointer transition-all shadow-2xs">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->id }}"
                                           @click="togglePerm({{ $permission->id }})"
                                           :checked="currentUserPerms.includes({{ $permission->id }})"
                                           class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $permission->name }}</span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider" x-text="currentUserPerms.includes({{ $permission->id }}) ? 'ON' : 'OFF'"></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" 
                            @click="isModalOpen = false" 
                            class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition-colors cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all cursor-pointer">
                        Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.sessionSuccess = @json(session('success'));
    window.sessionError = @json(session('error'));
</script>

<script src="{{ asset('js/components/user-management.js') }}"></script>
@endpush