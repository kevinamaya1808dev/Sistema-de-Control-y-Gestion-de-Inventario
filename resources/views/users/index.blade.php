@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@php
    $totalPermsCount = count($permissions);
@endphp

@section('content')
<x-app-container>
<div x-data="userManagement()" class="space-y-6">

    {{-- CABECERA DE LA PÁGINA --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-slate-800/80">
        <div>
            <h1 class="page-title">Gestión de Usuarios</h1>
            <p class="page-subtitle">Administra los accesos, roles y permisos del personal del sistema</p>
        </div>
        <button @click="openCreateModal()" class="btn-primary w-full sm:w-auto uppercase tracking-wider cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            <span>Nuevo Usuario</span>
        </button>
    </div>

    {{-- TARJETAS DE MÉTRICAS (KPIs) --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="kpi-card">
            <span class="kpi-label">Total Usuarios</span>
            <p class="kpi-value mt-1">{{ $totalUsers }}</p>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Administradores</span>
            <p class="kpi-value text-indigo-600 dark:text-indigo-400 mt-1">{{ $totalAdmins }}</p>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Operadores</span>
            <p class="kpi-value text-emerald-600 dark:text-emerald-400 mt-1">{{ $totalOperators }}</p>
        </div>
        <div class="kpi-card">
            <span class="kpi-label">Usuarios Activos</span>
            <p class="kpi-value text-sky-600 dark:text-sky-400 mt-1">{{ $activeUsers }}</p>
        </div>
        <div class="kpi-card col-span-2 lg:col-span-1">
            <span class="kpi-label">Cajas Abiertas</span>
            <p class="kpi-value text-amber-600 dark:text-amber-400 mt-1">{{ $cajasAbiertasHoy }}</p>
        </div>
    </div>

    {{-- CONTENEDOR DE TABLA ADAPTATIVO --}}
    <div class="table-container">
        
        {{-- TABLA ESCRITORIO ( >= 768px ) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="table-th">Usuario</th>
                        <th class="table-th">Rol</th>
                        <th class="table-th">Permisos</th>
                        <th class="table-th">Estado</th>
                        <th class="table-th">Caja Activa</th>
                        <th class="table-th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        @php $userPermCount = $user->permissions->count(); @endphp
                        <tr class="table-tr">
                            <td class="table-td">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 font-black flex items-center justify-center text-sm border border-indigo-500/20 shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-semibold">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="table-td">
                                <span class="px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-xs {{ optional($user->role)->name === 'Administrador' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' }}">
                                    {{ optional($user->role)->name ?? 'Sin Rol' }}
                                </span>
                            </td>
                            <td class="table-td">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center gap-1">
                                        @for ($i = 1; $i <= $totalPermsCount; $i++)
                                            <span class="w-1.5 h-1.5 rounded-full {{ $i <= $userPermCount ? 'bg-indigo-500 shadow-xs shadow-indigo-500' : 'bg-slate-200 dark:bg-slate-800' }}"></span>
                                        @endfor
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-400">
                                        {{ $userPermCount }}/{{ $totalPermsCount }}
                                    </span>
                                </div>
                            </td>
                            <td class="table-td">
                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="table-td">
                                @if($user->cajaMovimientos->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        Abierta
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 font-bold">—</span>
                                @endif
                            </td>
                            <td class="table-td text-right space-x-1 whitespace-nowrap">
                                <button @click="setUserData(@js($user->only(['id', 'name', 'email', 'role_id', 'is_active'])), @js($user->permissions->pluck('id')))" 
                                        class="p-2 text-indigo-500 hover:text-indigo-600 hover:bg-indigo-500/10 border border-transparent hover:border-indigo-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center" title="Editar Usuario">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                
                                @if($user->id !== 1)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" @submit.prevent="confirmDelete($event, {{ $user->id }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-500 hover:text-rose-600 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center" title="Eliminar Usuario">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- TARJETAS MÓVILES ( < 768px ) --}}
        <div class="block md:hidden divide-y divide-slate-100 dark:divide-slate-800/60">
            @foreach($users as $user)
                @php $userPermCount = $user->permissions->count(); @endphp
                <div class="p-4 space-y-3 hover:bg-indigo-50/40 dark:hover:bg-indigo-500/5 transition-colors">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-9 h-9 rounded-2xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-black flex items-center justify-center text-sm border border-indigo-500/20 shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <div class="font-bold text-slate-900 dark:text-white text-xs truncate">{{ $user->name }}</div>
                                <div class="text-[11px] text-slate-400 truncate font-semibold">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            <button @click="setUserData(@js($user->only(['id', 'name', 'email', 'role_id', 'is_active'])), @js($user->permissions->pluck('id')))" 
                                    class="p-2 text-indigo-500 bg-indigo-500/10 border border-indigo-500/20 rounded-xl active:scale-95 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            @if($user->id !== 1)
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" @submit.prevent="confirmDelete($event, {{ $user->id }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-500 bg-rose-500/10 border border-rose-500/20 rounded-xl active:scale-95 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ optional($user->role)->name === 'Administrador' ? 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20' }}">
                                {{ optional($user->role)->name ?? 'Sin Rol' }}
                            </span>

                            @if($user->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-slate-500/10 text-slate-400 border border-slate-500/20">
                                    Inactivo
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-1">
                                @for ($i = 1; $i <= $totalPermsCount; $i++)
                                    <span class="w-1.5 h-1.5 rounded-full {{ $i <= $userPermCount ? 'bg-indigo-500 shadow-xs shadow-indigo-500' : 'bg-slate-200 dark:bg-slate-800' }}"></span>
                                @endfor
                            </div>
                            <span class="text-[10px] font-bold text-slate-400">
                                {{ $userPermCount }}/{{ $totalPermsCount }}
                            </span>
                        </div>
                    </div>

                    @if($user->cajaMovimientos->isNotEmpty())
                        <div class="pt-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                Caja Abierta
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

    </div>

    {{-- MODAL CON CLASES UNIVERSALES DE FORMULARIO --}}
    <x-modal name="user" title="Gestión de Usuario" maxWidth="max-w-2xl">
        <form :action="isEditMode ? '/usuarios/' + currentUser.id : '{{ route('users.store') }}'" method="POST" class="relative z-10 flex flex-col flex-1 min-h-0 bg-transparent">
            @csrf
            <input type="hidden" name="_method" value="PUT" :disabled="!isEditMode">

            @if ($errors->any())
                <div class="mx-5 sm:mx-7 mt-4 p-3 bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs rounded-2xl shrink-0">
                    <p class="font-bold uppercase tracking-wider text-[11px] mb-1">Se encontraron errores:</p>
                    <ul class="list-disc pl-4 space-y-0.5 text-[11px]">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-5 sm:p-7 space-y-5 overflow-y-auto flex-1 min-h-0 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

                {{-- DATOS BÁSICOS --}}
                <div>
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400/80 mb-3">Datos Básicos</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="space-y-1.5">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="name" x-model="currentUser.name" required class="form-input" placeholder="Ej. Juan Pérez">
                        </div>

                        <div class="space-y-1.5">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" x-model="currentUser.email" required class="form-input" placeholder="admin@scgi.mx">
                        </div>
                    </div>
                </div>

                {{-- CONTRASEÑA --}}
            <div class="space-y-1.5">
                <label class="form-label">
                    Contraseña <span x-show="isEditMode" class="text-slate-400 dark:text-slate-500 font-normal lowercase">(opcional al editar)</span>
                </label>
                <input type="password" name="password" :required="!isEditMode" minlength="8"
                    class="form-input @error('password') border-rose-500 focus:border-rose-500 @enderror" 
                    placeholder="••••••••" autocomplete="new-password">
                
                @error('password')
                    <p class="text-[11px] font-bold text-rose-500 flex items-center gap-1 mt-1">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $message }}
                    </p>
                @else
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-1">Debe tener al menos 8 caracteres.</p>
                @enderror
            </div>

                <template x-if="currentUser.id === 1">
                    <div>
                        <input type="hidden" name="role_id" value="1">
                        <input type="hidden" name="is_active" value="1">
                    </div>
                </template>

                <template x-if="currentUser.id !== 1">
                    <div class="space-y-5">
                        {{-- ROL DEL USUARIO --}}
                        <div>
                            <h4 class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400/80 mb-3">Rol del Usuario</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($roles as $role)
                                    <label @click="currentUser.role_id = {{ $role->id }}" 
                                           class="relative flex flex-col p-4 rounded-2xl border-2 cursor-pointer transition-all"
                                           :class="currentUser.role_id == {{ $role->id }} ? 'border-indigo-500 dark:border-indigo-500 bg-indigo-50/60 dark:bg-indigo-500/10 shadow-md dark:shadow-[0_0_15px_rgba(99,102,241,0.2)]' : 'border-slate-200/80 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/40 hover:border-slate-300 dark:hover:border-slate-700'">
                                        <div class="flex items-center justify-between mb-2.5">
                                            <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $role->name === 'Administrador' ? 'bg-indigo-500/15 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400' : 'bg-emerald-500/15 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                            </div>
                                            <input type="radio" name="role_id" value="{{ $role->id }}" x-model.number="currentUser.role_id" :checked="currentUser.role_id == {{ $role->id }}" class="sr-only">
                                        </div>
                                        <span class="text-xs font-black text-slate-900 dark:text-white">{{ $role->name }}</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-0.5">{{ $role->name === 'Administrador' ? 'Acceso completo a las funciones' : 'Operaciones limitadas' }}</span>
                                        <span class="mt-2.5 text-[10px] font-black text-indigo-600 dark:text-indigo-400 flex items-center gap-1 uppercase tracking-wider" x-show="currentUser.role_id == {{ $role->id }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            SELECCIONADO
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- ESTADO DE LA CUENTA --}}
                        <div class="flex items-center justify-between p-4 bg-slate-100/80 dark:bg-slate-900/80 border border-slate-200/80 dark:border-slate-800/80 rounded-2xl">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block uppercase tracking-wider">Estado de la cuenta</span>
                                <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold">Determina si el usuario puede ingresar al sistema</span>
                            </div>
                            <input type="hidden" name="is_active" :value="currentUser.is_active ? 1 : 0">
                            <button type="button" @click="currentUser.is_active = !currentUser.is_active" 
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                    :class="currentUser.is_active ? 'bg-indigo-600 dark:bg-indigo-500' : 'bg-slate-300 dark:bg-slate-700'">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition duration-200 ease-in-out"
                                    :class="currentUser.is_active ? 'translate-x-5' : 'translate-x-0'"></span>
                            </button>
                        </div>

                        {{-- PERMISOS PERSONALIZADOS --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400/80 mb-0">Permisos Personalizados</h4>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-600 dark:text-indigo-400 border border-emerald-500/20 dark:border-indigo-500/20">
                                    <span x-text="currentUserPerms.length"></span> / {{ $totalPermsCount }} SELECCIONADOS
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach($permissions as $permission)
                                    <label class="flex items-center justify-between p-3 bg-slate-100/60 dark:bg-slate-900/60 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800/80 cursor-pointer transition-all">
                                        <div class="flex items-center gap-2.5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                   x-model.number="currentUserPerms" 
                                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-indigo-600 dark:text-indigo-500 focus:ring-0">
                                            <div>
                                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block line-clamp-1">{{ $permission->name }}</span>
                                            </div>
                                        </div>
                                        <span class="w-2 h-2 rounded-full transition-colors shrink-0" 
                                            :class="currentUserPerms.includes({{ $permission->id }}) ? 'bg-indigo-600 dark:bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.8)]' : 'bg-slate-300 dark:bg-slate-700'"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            {{-- PIE DE BOTONES --}}
            <div class="flex items-center justify-end gap-3 p-5 sm:px-7 border-t border-slate-100 dark:border-white/5 bg-transparent shrink-0">
            <button type="button" @click="closeModal('user')" class="btn-secondary">
                Cancelar
            </button>
            <button type="submit" class="btn-primary px-8 py-2.5">
                <span x-text="isEditMode ? 'Guardar Cambios' : 'Guardar Usuario'"></span>
            </button>
            </div>
        </form>
    </x-modal>

</div>
</x-app-container>
@endsection
@push('scripts')
    <script src="{{ asset('js/components/user-management.js') }}"></script>
@endpush