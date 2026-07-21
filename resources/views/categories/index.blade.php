@extends('layouts.app')

@section('title', 'SCGI - Gestión de Categorías')
@section('header_title', 'Gestión de Categorías')

@section('content')
<!-- Cambiado de max-w-4xl a max-w-5xl para darle más cuerpo y tamaño global -->
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="categoryManagement()">
    
    <!-- ALERTAS NOTIFICACIONES PREMIUM -->
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 p-4 rounded-2xl shadow-xl shadow-emerald-500/5 mb-6 flex items-center space-x-3 transition-all">
            <div class="p-2 rounded-xl bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span class="text-xs font-bold tracking-wide">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-400 p-4 rounded-2xl shadow-xl shadow-rose-500/5 mb-6 flex items-center space-x-3 transition-all">
            <div class="p-2 rounded-xl bg-rose-500/20 text-rose-600 dark:text-rose-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <span class="text-xs font-bold tracking-wide">{{ session('error') }}</span>
        </div>
    @endif

    <!-- ENCABEZADO PRINCIPAL -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 pb-6 border-b border-slate-100 dark:border-slate-800/80">
        <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Gestión de Categorías
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 dark:text-slate-500 mt-1 font-semibold tracking-wide">
                Administra las clasificaciones globales para el control de inventario
            </p>
        </div>
        
        <button @click="openCreateModal()" 
                class="group relative px-6 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30 transition-all flex items-center gap-2.5 cursor-pointer transform hover:-translate-y-0.5 active:translate-y-0">
            <svg class="w-4 h-4 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            <span class="uppercase tracking-wider">Nueva Categoría</span>
        </button>
    </div>

    <!-- CONTENEDOR DE LA TABLA (MÁS AMPLIO Y PROPORCIONAL) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200/70 dark:border-slate-800/80 overflow-hidden">
        
        <!-- Encabezado Interno -->
        <div class="px-7 py-5 bg-slate-50/50 dark:bg-slate-950/40 border-b border-slate-100 dark:border-slate-800/80">
            <h2 class="text-xs sm:text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider">Categorías Activas</h2>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-bold mt-0.5">Listado maestro en tiempo real</p>
        </div>
        
        <!-- Tabla Estilizada con Fuentes y Padding más Grandes -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 dark:bg-slate-950 text-slate-400 dark:text-slate-500 text-[11px] font-black uppercase tracking-widest border-b border-slate-100 dark:border-slate-800">
                        <th class="py-4.5 px-7 w-28">ID de Registro</th>
                        <th class="py-4.5 px-6 w-64">Nombre / Etiqueta</th>
                        <th class="py-4.5 px-6">Descripción Operativa</th>
                        <th class="py-4.5 px-7 text-right w-36">Acciones del Sistema</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs sm:text-sm font-bold text-slate-600 dark:text-slate-300">
                    @forelse($categories as $category)
                        <tr class="hover:bg-indigo-50/20 dark:hover:bg-slate-800/40 transition-colors duration-200">
                            <!-- ID Tabular -->
                            <td class="py-5 px-7 font-bold text-slate-400 dark:text-slate-500 tabular-nums">
                                #{{ $category->id }}
                            </td>
                            
                            <!-- Badge de Nombre (Un poco más grande y legible) -->
                            <td class="py-5 px-6">
                                <span class="font-black text-slate-900 dark:text-white px-3 py-1.5 bg-slate-100 dark:bg-slate-800 border border-slate-200/50 dark:border-slate-700 rounded-xl text-[12px] tracking-wide inline-block">
                                    {{ $category->name }}
                                </span>
                            </td>
                            
                            <!-- Descripción Cortada -->
                            <td class="py-5 px-6 text-slate-500 dark:text-slate-400 max-w-md font-medium truncate text-xs sm:text-sm">
                                {{ $category->description ?? 'Sin descripción configurada en el sistema.' }}
                            </td>
                            
                            <!-- Botones de Acción Modulares -->
                            <td class="py-5 px-7 text-right space-x-2 whitespace-nowrap">
                                
                                <!-- Editar -->
                                <button @click="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description) }}')" 
                                        class="p-2.5 text-amber-500 hover:text-amber-600 hover:bg-amber-500/10 border border-transparent hover:border-amber-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center shadow-xs" title="Modificar Registro">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>

                                <!-- Eliminar -->
                                <button @click="openDeleteModal({{ $category->id }})" 
                                        class="p-2.5 text-rose-500 hover:text-rose-600 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 rounded-xl transition-all cursor-pointer inline-flex items-center shadow-xs" title="Remover del Servidor">
                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-16v1a3 3 0 003 3h10M9 3h6M4 7h16"/></svg>
                                </button>
                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-14 text-center text-slate-400 dark:text-slate-600 font-bold tracking-wide">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="p-4 bg-slate-100 dark:bg-slate-950 rounded-2xl text-slate-300 dark:text-slate-700">
                                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5M14 10h1"/></svg>
                                    </div>
                                    <span class="text-xs uppercase tracking-wider">No se encontraron clasificaciones registradas.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: CREAR REGISTRO -->
    <div x-show="modals.create" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200/40 dark:border-slate-800" @click.outside="modals.create = false">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/20">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span> Registrar Nueva Categoría
                </h3>
                <button type="button" @click="modals.create = false" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-750 text-slate-400 dark:text-slate-500 hover:text-slate-700 flex items-center justify-center cursor-pointer transition-colors font-bold">✕</button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Nombre Único</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:border-slate-800 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-xs font-bold focus:outline-hidden focus:ring-4 focus:ring-indigo-500/10 transition-all" placeholder="Ej. Hardware" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Descripción del Catálogo</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:border-slate-800 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-xs font-bold focus:outline-hidden focus:ring-4 focus:ring-indigo-500/10 transition-all" placeholder="Especifica qué tipo de artículos entran aquí..."></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" @click="modals.create = false" class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 cursor-pointer transition-all">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR REGISTRO -->
    <div x-show="modals.edit" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/60 backdrop-blur-md flex items-center justify-center p-4" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200/40 dark:border-slate-800" @click.outside="modals.edit = false">
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/50 dark:bg-slate-950/20">
                <h3 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Modificar Registro Electrónico
                </h3>
                <button type="button" @click="modals.edit = false" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center cursor-pointer font-bold">✕</button>
            </div>
            <form :action="'{{ route('categories.index') }}/' + formEdit.id" method="POST">
                @csrf
                @method('PUT')
                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Nombre Actualizado</label>
                        <input type="text" x-model="formEdit.name" name="name" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:border-slate-800 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-xs font-bold focus:outline-hidden focus:ring-4 focus:ring-indigo-500/10 transition-all" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Nueva Descripción Funcional</label>
                        <textarea x-model="formEdit.description" name="description" rows="3" class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 focus:border-indigo-500 dark:border-slate-800 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-xs font-bold focus:outline-hidden focus:ring-4 focus:ring-indigo-500/10 transition-all"></textarea>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button type="button" @click="modals.edit = false" class="px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl cursor-pointer">Cancelar</button>
                    <button type="submit" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl cursor-pointer transition-colors">Actualizar Datos</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: ELIMINAR REGISTRO -->
    <div x-show="modals.delete" 
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/70 backdrop-blur-md flex items-center justify-center p-4" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;">
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6 border border-slate-200/40 dark:border-slate-800" @click.outside="modals.delete = false">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-600 dark:text-rose-400 flex items-center justify-center mx-auto mb-4 border border-rose-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-sm font-black text-slate-900 dark:text-white mb-1.5 uppercase tracking-wide">¿Confirmar Destrucción?</h3>
            <p class="text-[11px] text-slate-400 dark:text-slate-500 mb-6 font-bold px-2 leading-relaxed">
                Esta acción eliminará el registro del servidor. Asegúrate de que no contenga productos amarrados en base de datos.
            </p>
            <form :action="'{{ route('categories.index') }}/' + formDelete.id" method="POST" class="flex gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="modals.delete = false" class="w-1/2 py-3 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-900 font-black text-xs cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Abortar</button>
                <button type="submit" class="w-1/2 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black text-xs rounded-xl cursor-pointer transition-all shadow-md shadow-rose-600/10">Eliminar</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.laravelErrors = @json($errors->any());
</script>
<script src="{{ asset('js/components/category-management.js') }}"></script>
@endpush