@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
<div x-data="userManagement()" class="space-y-6">

    <!-- ALERTAS DE NOTIFICACIÓN -->
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 rounded-2xl text-xs font-semibold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- CABECERA -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Gestión de Usuarios</h1>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">Administra los accesos, roles y permisos del personal del sistema</p>
        </div>
        <button @click="openCreateModal()" 
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-2xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95 w-full sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Nuevo Usuario
        </button>
    </div>

    <!-- TARJETAS DE MÉTRICAS -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Usuarios</span>
            <p class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Administradores</span>
            <p class="text-xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $totalAdmins }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Operadores</span>
            <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalOperators }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Usuarios Activos</span>
            <p class="text-xl font-black text-sky-600 dark:text-sky-400 mt-1">{{ $activeUsers }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200 dark:border-slate-800 col-span-2 lg:col-span-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Cajas Abiertas</span>
            <p class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $cajasAbiertasHoy }}</p>
        </div>
    </div>

    <!-- TABLA / LISTADO RESPONSIVO DE USUARIOS -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        
        <!-- VISTA DE TABLA (ESCRITORIO Y TABLETS) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider">
                        <th class="p-4">Usuario</th>
                        <th class="p-4">Rol</th>
                        <th class="p-4">Permisos</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4">Caja Activa</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-600 dark:text-slate-300 font-medium">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-black flex items-center justify-center text-sm border border-slate-200 dark:border-slate-700 shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ optional($user->role)->name === 'Administrador' ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' }}">
                                    {{ optional($user->role)->name ?? 'Sin Rol' }}
                                </span>
                            </td>
                            <!-- PUNTITOS Y CONTADOR DE PERMISOS -->
                            <td class="p-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        @php 
                                            $totalPermsCount = count($permissions);
                                            $userPermCount = $user->permissions->count();
                                        @endphp
                                        @for ($i = 1; $i <= $totalPermsCount; $i++)
                                            <span class="w-1.5 h-1.5 rounded-full {{ $i <= $userPermCount ? 'bg-indigo-500 shadow-sm shadow-indigo-500' : 'bg-slate-200 dark:bg-slate-700' }}"></span>
                                        @endfor
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
                                        {{ $userPermCount }}/{{ $totalPermsCount }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 border border-slate-200 dark:border-slate-700">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                @if($user->cajaMovimientos->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        Abierta
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <button @click="setUserData({{ json_encode($user) }}, {{ json_encode($user->permissions->pluck('id')) }})" 
                                        class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                
                                @if($user->id !== 1)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" @submit.prevent="confirmDelete($event, {{ $user->id }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-400 hover:text-rose-600 dark:hover:text-rose-500 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- VISTA DE TARJETAS (MÓVILES) -->
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800">
            @foreach($users as $user)
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 font-black flex items-center justify-center text-sm border border-slate-200 dark:border-slate-700 shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <div class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $user->name }}</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate">{{ $user->email }}</div>
                            </div>
                        </div>

                        <!-- ACCIONES MÓVIL -->
                        <div class="flex items-center gap-1 shrink-0">
                            <button @click="setUserData({{ json_encode($user) }}, {{ json_encode($user->permissions->pluck('id')) }})" 
                                    class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @if($user->id !== 1)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" @submit.prevent="confirmDelete($event, {{ $user->id }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- ETIQUETAS E INFORMACIÓN ADICIONAL (INCLUYENDO PUNTITOS EN MÓVIL) -->
                    <div class="flex items-center justify-between pt-1 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ optional($user->role)->name === 'Administrador' ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800' : 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' }}">
                                {{ optional($user->role)->name ?? 'Sin Rol' }}
                            </span>

                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 border border-slate-200 dark:border-slate-700">
                                    Inactivo
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1">
                                @php 
                                    $totalPermsCount = count($permissions);
                                    $userPermCount = $user->permissions->count();
                                @endphp
                                @for ($i = 1; $i <= $totalPermsCount; $i++)
                                    <span class="w-1.5 h-1.5 rounded-full {{ $i <= $userPermCount ? 'bg-indigo-500 shadow-sm shadow-indigo-500' : 'bg-slate-200 dark:bg-slate-700' }}"></span>
                                @endfor
                            </div>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">
                                {{ $userPermCount }}/{{ $totalPermsCount }}
                            </span>
                        </div>
                    </div>

                    @if($user->cajaMovimientos->isNotEmpty())
                        <div class="pt-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                Caja Abierta
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </div>

    <!-- MODAL (CREACIÓN Y EDICIÓN DE USUARIO) -->
    <div x-show="isModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <!-- Fondo Oscuro / Backdrop Blur -->
        <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/70 backdrop-blur-md" @click="isModalOpen = false"></div>

        <!-- Contenedor del Modal -->
        <div class="relative min-h-screen flex items-center justify-center p-3 sm:p-4">
            <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden p-5 sm:p-8 space-y-6 transform transition-all"
                 @click.outside="isModalOpen = false">
                
                <!-- Encabezado del Modal -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400 text-sm shrink-0">
                            <span x-text="currentUser.name ? currentUser.name.substring(0, 2).toUpperCase() : 'US'"></span>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white" x-text="isEditMode ? (currentUser.id === 1 ? 'Editar Super Administrador' : 'Editar Usuario') : 'Crear Nuevo Usuario'"></h3>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5" x-text="isEditMode ? 'Modifica la información y accesos del usuario' : 'Llena los campos para dar de alta a un usuario'"></p>
                        </div>
                    </div>
                    <button type="button" @click="isModalOpen = false" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/50 text-slate-400 hover:text-slate-600 dark:hover:text-white flex items-center justify-center transition-all shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- ERRORES DE VALIDACIÓN SI EXISTEN -->
                @if ($errors->any())
                    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-600 dark:text-rose-400 text-xs font-semibold">
                        <p class="font-bold mb-1">Se encontraron los siguientes errores:</p>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORMULARIO -->
                <form :action="isEditMode ? '/usuarios/' + currentUser.id : '{{ route('users.store') }}'" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" :disabled="!isEditMode">

                    <!-- DATOS BÁSICOS -->
                    <div>
                        <h4 class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Datos Básicos</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Nombre Completo</label>
                                <input type="text" name="name" x-model="currentUser.name" required 
                                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 rounded-xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-indigo-500/30 focus:border-emerald-500 dark:focus:border-indigo-500 transition-all" 
                                       placeholder="Ej. Juan Pérez">
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Correo Electrónico</label>
                                <input type="email" name="email" x-model="currentUser.email" required 
                                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 rounded-xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-indigo-500/30 focus:border-emerald-500 dark:focus:border-indigo-500 transition-all" 
                                       placeholder="admin@scgi.mx">
                            </div>
                        </div>
                    </div>

                    <!-- CONTRASEÑA -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
                            Contraseña <span x-show="isEditMode" class="text-slate-400 dark:text-slate-500 font-normal lowercase">(dejar en blanco para conservar la actual)</span>
                        </label>
                        <input type="password" name="password" :required="!isEditMode" 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 rounded-xl text-xs font-semibold text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:focus:ring-indigo-500/30 focus:border-emerald-500 dark:focus:border-indigo-500 transition-all" 
                               placeholder="••••••••">
                    </div>

                    <!-- CAMPOS OCULTOS SI ES EL SUPER ADMIN (ID: 1) -->
                    <template x-if="currentUser.id === 1">
                        <div>
                            <input type="hidden" name="role_id" value="1">
                            <input type="hidden" name="is_active" value="1">
                        </div>
                    </template>

                    <!-- CONFIGURACIONES SOLO PARA USUARIOS REGULARES -->
                    <template x-if="currentUser.id !== 1">
                        <div class="space-y-6">
                            <!-- SELECCIÓN DE ROL -->
                            <div>
                                <h4 class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Rol del Usuario</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                    @foreach($roles as $role)
                                        <label @click="currentUser.role_id = {{ $role->id }}" 
                                               class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                               :class="currentUser.role_id == {{ $role->id }} ? 'border-emerald-500 bg-emerald-50/30 dark:border-emerald-500/80 dark:bg-emerald-500/10' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/40 hover:border-slate-300 dark:hover:border-slate-700'">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $role->name === 'Administrador' ? 'bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400' : 'bg-emerald-50 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                </div>
                                                <input type="radio" name="role_id" value="{{ $role->id }}" x-model.number="currentUser.role_id" :checked="currentUser.role_id == {{ $role->id }}" class="sr-only">
                                            </div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white">{{ $role->name }}</span>
                                            <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">{{ $role->name === 'Administrador' ? 'Acceso completo a las funciones' : 'Operaciones limitadas' }}</span>
                                            <span class="mt-2 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1" x-show="currentUser.role_id == {{ $role->id }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Seleccionado
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- ESTADO ACTIVO/INACTIVO -->
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 rounded-2xl">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block">Estado de la cuenta</span>
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500">Determina si el usuario puede ingresar al sistema</span>
                                </div>
                                <input type="hidden" name="is_active" :value="currentUser.is_active ? 1 : 0">
                                <button type="button" @click="currentUser.is_active = !currentUser.is_active" 
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="currentUser.is_active ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700'">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                          :class="currentUser.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                                </button>
                            </div>

                            <!-- PERMISOS PERSONALIZADOS -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Permisos Personalizados</h4>
                                    <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-200 dark:border-indigo-500/20">
                                        <span x-text="selectedPermsCount"></span> / {{ count($permissions) }} seleccionados
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-52 overflow-y-auto pr-1">
                                    @foreach($permissions as $permission)
                                        <label class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/40 hover:bg-slate-100 dark:hover:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-800/80 hover:border-slate-300 dark:hover:border-slate-700/80 cursor-pointer transition-all">
                                            <div class="flex items-center gap-3">
                                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                       x-model.number="currentUserPerms" 
                                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-emerald-500 focus:ring-emerald-500/30 focus:ring-offset-white dark:focus:ring-offset-slate-900">
                                                <div>
                                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">{{ $permission->name }}</span>
                                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $permission->slug }}</span>
                                                </div>
                                            </div>
                                            <span class="w-2 h-2 rounded-full transition-colors" 
                                                  :class="currentUserPerms.includes({{ $permission->id }}) ? 'bg-emerald-500 shadow-sm shadow-emerald-500' : 'bg-slate-300 dark:bg-slate-700'"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="isModalOpen = false" class="px-5 py-2.5 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/60 rounded-xl transition-all">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all active:scale-95">
                            <span x-text="isEditMode ? 'Guardar Cambios' : 'Crear Usuario'"></span>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</div>

@push('scripts')
    <script src="{{ asset('js/components/user-management.js') }}"></script>
@endpush
@endsection