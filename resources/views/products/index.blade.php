@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto" x-data="productsPage()">
    @if(session('success'))
        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-700 shadow-sm">
            {{ session('success') }}
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

    <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-slate-400">SCGI / Productos</p>
            <h1 class="text-3xl font-bold text-slate-900 mt-3">Registrar nuevo producto</h1>
            <p class="mt-2 text-sm text-slate-500">Completa los datos para agregar un producto al inventario.</p>
        </div>
        <button @click="openModal()" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition-all hover:bg-indigo-700">
            <span class="text-xl">+</span>
            Nuevo producto
        </button>
    </div>

    <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Inventario de Productos</h2>
                <p class="text-sm text-slate-500 mt-1">Total de productos: <span class="font-semibold text-slate-900">{{ $products->count() }}</span></p>
            </div>
            <span class="inline-flex items-center rounded-full bg-slate-100 px-4 py-2 text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">
                {{ auth()->user()->role->name ?? 'Administrador' }}
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-500 text-[11px] uppercase tracking-[0.22em]">
                    <tr>
                        <th class="px-6 py-4">Producto</th>
                        <th class="px-6 py-4">SKU</th>
                        <th class="px-6 py-4">Categoría</th>
                        <th class="px-6 py-4">Precio</th>
                        <th class="px-6 py-4 text-center">Stock</th>
                        <th class="px-6 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="h-full w-full rounded-2xl object-cover">
                                    @else
                                        <span class="text-sm font-bold uppercase">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $product->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-500 text-xs">{{ $product->sku }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $product->category->name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-900">${{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 text-center text-slate-600">{{ $product->stock }}</td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openEdit({{ $product->id }}, '{{ addslashes($product->sku) }}', '{{ addslashes($product->name) }}', '{{ addslashes($product->description ?? '') }}', {{ $product->category_id }}, {{ number_format($product->price, 2, '.', '') }}, {{ $product->stock }})"
                                    class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition-all">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-slate-400">
                                No hay productos registrados aún.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 px-4 py-4">
        <div @click.away="closeModal()" class="w-full max-w-2xl overflow-hidden rounded-xl bg-white p-6 shadow-lg">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900" x-text="modalTitle"></h2>
                    <p class="mt-1 text-sm text-slate-500" x-text="modalSubtitle"></p>
                </div>
                <button @click="closeModal()" class="rounded-full bg-slate-100 p-3 text-slate-500 hover:bg-slate-200 transition-colors">✕</button>
            </div>

            <form :action="formAction" method="POST" enctype="multipart/form-data" class="mt-6 space-y-4">
                @csrf
                <template x-if="isEditMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">SKU *</label>
                        <input x-model="form.sku" name="sku" type="text" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 outline-none focus:border-indigo-500" placeholder="ELC-001">
                    </div>


                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Nombre del producto *</label>
                        <input x-model="form.name" name="name" type="text" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 outline-none focus:border-indigo-500" placeholder="Laptop Dell Latitude 5540">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Descripción</label>
                        <textarea x-model="form.description" name="description" rows="2" class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 outline-none focus:border-indigo-500" placeholder="Descripción opcional del producto..."></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Categoría *</label>
                        <select x-model="form.category_id" name="category_id" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 outline-none focus:border-indigo-500">
                            <option value="">Selecciona categoría</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Precio (MXN) *</label>
                        <input x-model="form.price" name="price" type="number" step="0.01" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 outline-none focus:border-indigo-500" placeholder="0.00">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Stock inicial *</label>
                        <input x-model="form.stock" name="stock" type="number" min="0" required class="w-full rounded-md border border-slate-200 px-3 py-2 text-sm text-slate-900 outline-none focus:border-indigo-500" placeholder="0">
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
    function productsPage() {
        return {
            modalOpen: false,
            isEditMode: false,
            modalTitle: 'Registrar nuevo producto',
            modalSubtitle: 'Llena los datos para agregar un producto al inventario.',
            submitLabel: 'Registrar producto',
            formAction: '{{ route('products.store') }}',
            form: {
                sku: '',
                name: '',
                description: '',
                category_id: '',
                price: '',
                stock: 0,
            },
                openModal() {
                this.modalOpen = true;
                this.isEditMode = false;
                this.modalTitle = 'Registrar nuevo producto';
                this.modalSubtitle = 'Llena los datos para agregar un producto al inventario.';
                this.submitLabel = 'Registrar producto';
                this.formAction = '{{ route('products.store') }}';
                this.form.sku = '';
                this.form.name = '';
                this.form.description = '';
                this.form.category_id = '';
                this.form.price = '';
                this.form.stock = 0;
            },
            closeModal() {
                this.modalOpen = false;
            },
            openEdit(id, sku, name, description, categoryId, price, stock) {
                this.modalOpen = true;
                this.isEditMode = true;
                this.modalTitle = 'Editar producto';
                this.modalSubtitle = 'Actualiza los datos de este producto.';
                this.submitLabel = 'Guardar cambios';
                this.formAction = '/productos/' + id;
                this.form.sku = sku;
                this.form.name = name;
                this.form.description = description;
                this.form.category_id = categoryId;
                this.form.price = price;
                this.form.stock = stock;
            }
        }
    }
</script>
@endsection