@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 rounded-lg shadow-sm mb-6 flex items-center space-x-2">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Formulario Categoría -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center space-x-2">
                <i data-lucide="plus-circle" class="text-indigo-600"></i>
                <span>Nueva Categoría</span>
            </h2>

            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nombre</label>
                        <input type="text" name="name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" placeholder="Ej. Accesorios" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Descripción</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all" placeholder="Opcional..."></textarea>
                    </div>
                    <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-600/20 transition-all">
                        Guardar Categoría
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla Categorías -->
        <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Categorías Registradas</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider">
                            <th class="p-4 pl-6">ID</th>
                            <th class="p-4">Nombre</th>
                            <th class="p-4">Descripción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="p-4 pl-6 font-semibold text-gray-500">#{{ $category->id }}</td>
                                <td class="p-4 font-semibold text-gray-900">{{ $category->name }}</td>
                                <td class="p-4 text-gray-500">{{ $category->description ?? 'Sin descripción' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-400">
                                    No hay categorías registradas aún.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection