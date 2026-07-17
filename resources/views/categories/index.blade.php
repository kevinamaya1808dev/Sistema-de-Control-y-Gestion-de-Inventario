@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto" x-data="categoriesPage()">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">SCGI / Categorías</p>
            <h1 class="text-3xl font-bold text-slate-900 mt-3">Categorías</h1>
            <p class="mt-2 text-sm text-slate-500">Organiza el catálogo de productos por categoría.</p>
        </div>

        <button @click="openCreate()"
                class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition-all hover:bg-indigo-700">
            <span class="text-xl">+</span>
            Crear categoría
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-700 shadow-sm">
            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Categorías registradas</h2>
                <p class="text-sm text-slate-500 mt-1">Total de categorías: <span class="font-semibold text-slate-900">{{ $categories->count() }}</span></p>
            </div>
            <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">
                {{ auth()->user()->role->name ?? 'Admin' }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.22em]">
                    <tr>
                        <th class="px-6 py-4">ID</th>
                        <th class="px-6 py-4">Nombre</th>
                        <th class="px-6 py-4">Descripción</th>
                        <th class="px-6 py-4 text-center">Productos</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 font-semibold text-slate-500">c{{ $category->id }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $category->name }}</td>
                            <td class="px-6 py-4 text-slate-500">{{ $category->description ?? 'Sin descripción' }}</td>
                            <td class="px-6 py-4 text-center text-slate-600">{{ $category->products_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openEdit({ id: {{ $category->id }}, name: '{{ addslashes($category->name) }}', description: '{{ addslashes($category->description ?? '') }}' })"
                                        class="mr-2 inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-all">
                                    Editar
                                </button>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-2xl bg-rose-500 px-3 py-2 text-xs font-semibold text-white hover:bg-rose-600 transition-all" onclick="return confirm('¿Eliminar esta categoría?')">
                                        Borrar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center text-slate-400">
                                No hay categorías registradas aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 py-6">
        <div @click.away="closeModal()" class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-slate-900" x-text="modalTitle"></h3>
                    <p class="mt-1 text-sm text-slate-500" x-text="modalSubtitle"></p>
                </div>
                <button @click="closeModal()" class="rounded-full bg-slate-100 p-2 text-slate-500 hover:bg-slate-200 transition-colors">✕</button>
            </div>

            <form :action="formAction" method="POST" class="mt-6 space-y-4">
                @csrf
                <template x-if="isEditMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="grid gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Nombre *</label>
                        <input x-model="form.name" name="name" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" placeholder="Ej. Electrónica">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Descripción</label>
                        <textarea x-model="form.description" name="description" rows="3" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" placeholder="Breve descripción de la categoría..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="closeModal()" class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Cancelar</button>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700" x-text="submitLabel"></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function categoriesPage() {
        return {
            modalOpen: false,
            isEditMode: false,
            modalTitle: 'Crear categoría',
            modalSubtitle: 'Agrega una nueva categoría para organizar el inventario.',
            submitLabel: 'Crear categoría',
            formAction: '{{ route('categories.store') }}',
            form: {
                name: '',
                description: '',
            },
            openCreate() {
                this.modalOpen = true;
                this.isEditMode = false;
                this.modalTitle = 'Crear categoría';
                this.modalSubtitle = 'Agrega una nueva categoría para organizar el inventario.';
                this.submitLabel = 'Crear categoría';
                this.formAction = '{{ route('categories.store') }}';
                this.form.name = '';
                this.form.description = '';
            },
            openEdit(category) {
                this.modalOpen = true;
                this.isEditMode = true;
                this.modalTitle = 'Editar categoría';
                this.modalSubtitle = 'Actualiza el nombre o la descripción de esta categoría.';
                this.submitLabel = 'Guardar cambios';
                this.formAction = '/categorias/' + category.id;
                this.form.name = category.name;
                this.form.description = category.description;
            },
            closeModal() {
                this.modalOpen = false;
            }
        }
    }
</script>
@endsection