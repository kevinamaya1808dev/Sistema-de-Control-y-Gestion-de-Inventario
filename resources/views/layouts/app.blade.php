<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SCGI - Sistema de Control y Gestión de Inventarios</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Alpine.js (necesario para dropdowns y modales globales) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased overflow-x-hidden">

    <div class="flex min-h-screen">
        <!-- Sidebar Izquierdo -->
        <aside id="sidebar" class="hidden md:flex md:w-64 bg-slate-900 text-slate-100 flex-col fixed inset-y-0 left-0 z-50 transition-all duration-300 shadow-xl">
            <!-- Logo y Cierre móvil -->
            <div class="p-5 border-b border-slate-800 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-extrabold text-lg tracking-widest shadow-sm">S</span>
                    <div class="leading-tight">
                        <p class="text-xl font-bold tracking-wide text-white">SCGI</p>
                        <p class="text-xxs uppercase tracking-[0.4em] text-slate-300 font-semibold">Inventarios</p>
                    </div>
                </div>
                <div class="inline-flex items-center justify-center rounded-2xl bg-slate-800/70 px-4 py-2 text-[10px] uppercase tracking-[0.25em] font-bold text-slate-300">
                    Administrador
                </div>
                <button onclick="toggleSidebar()" class="md:hidden self-end text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors">
                    ✕
                </button>
            </div>
            
            <!-- Navegación del Sistema -->
            <nav class="flex-1 p-4 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white font-medium' }} transition-all text-sm">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                @php $canCategories = Auth::user()->isAdmin() || (method_exists(Auth::user(), 'hasPermissionTo') && Auth::user()->hasPermissionTo('manage-categories')); @endphp
                @if($canCategories)
                    <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white font-medium' }} transition-all text-sm">
                        <svg class="w-5 h-5 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Categorías</span>
                    </a>
                @else
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-500 opacity-40 cursor-not-allowed text-sm">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span>Categorías</span>
                    </div>
                @endif

                @php $canProducts = Auth::user()->isAdmin() || (method_exists(Auth::user(), 'hasPermissionTo') && Auth::user()->hasPermissionTo('manage-products')); @endphp
                @if($canProducts)
                    <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white font-medium' }} transition-all text-sm">
                        <svg class="w-5 h-5 {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Productos</span>
                    </a>
                @else
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-500 opacity-40 cursor-not-allowed text-sm">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span>Productos</span>
                    </div>
                @endif

                <a href="{{ route('movements.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('movements.*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white font-medium' }} transition-all text-sm">
                    <svg class="w-5 h-5 {{ request()->routeIs('movements.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2-2-2m8 4H6m10 0l2-2-2-2"></path>
                    </svg>
                    <span>Control de Stock</span>
                </a>

                @php $canUsers = Auth::user()->isAdmin() || (method_exists(Auth::user(), 'hasPermissionTo') && Auth::user()->hasPermissionTo('manage-users')); @endphp
                @if($canUsers)
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/20' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white font-medium' }} transition-all text-sm">
                        <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Usuarios</span>
                    </a>
                @else
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-500 opacity-40 cursor-not-allowed text-sm">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Usuarios</span>
                    </div>
                @endif
            </nav>


            <!-- Pie de Sidebar: al hacer click cierra sesión y regresa al login -->
            <div class="mt-auto p-4 border-t border-slate-800/80 bg-slate-950/30">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left flex items-center gap-3 p-3 rounded-3xl bg-slate-900/90 hover:bg-slate-800 transition-colors">
                        <div class="w-12 h-12 rounded-3xl bg-indigo-600 text-white font-bold flex items-center justify-center">{{ strtoupper(substr(Auth::user()->name ?? 'US', 0, 2)) }}</div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                            <p class="text-xxs text-slate-400 truncate">{{ Auth::user()->email ?? 'email@scgi.mx' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenedor Principal -->
        <div class="flex-1 flex flex-col md:pl-64 min-w-0">
            
            <!-- Header Superior -->
            <header class="h-18 bg-white border-b border-slate-200/80 flex items-center justify-between px-6 sticky top-0 z-40 shadow-xs">
                
                <!-- Hamburguesa móvil y Título -->
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="md:hidden p-2 rounded-xl hover:bg-slate-100 text-slate-600 transition-colors cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <h1 class="text-base font-bold text-slate-800 hidden sm:block">Panel de Control</h1>
                </div>

                <!-- Buscador Global del Header -->
                <div class="flex-1 max-w-md mx-4 hidden md:block">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" placeholder="Buscar productos, movimientos..." class="w-full pl-10 pr-4 py-2 bg-slate-50/80 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all shadow-inner-xs">
                    </div>
                </div>

                <!-- Perfil, Notificaciones y Acciones de Usuario -->
                <div class="flex items-center gap-3 ml-auto sm:ml-0" x-data="{ profileOpen: false }">
                    <button class="relative p-2.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100/80 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

                    <!-- Dropdown del Perfil -->
                    <div class="relative">
                        <button @click="profileOpen = !profileOpen" class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer select-none">
                            <div class="text-right hidden sm:block">
                                <div class="text-sm font-bold text-slate-800 leading-tight">
                                    {{ Auth::user()->name ?? 'Saúl Pérez' }}
                                </div>
                                <div class="text-xxs text-slate-400 font-semibold uppercase tracking-wider">
                                    {{ optional(Auth::user()->role)->name ?? 'Administrador' }}
                                </div>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 font-extrabold flex items-center justify-center border border-indigo-100 text-sm shadow-xs uppercase">
                                {{ substr(Auth::user()->name ?? 'SP', 0, 2) }}
                            </div>
                        </button>

                        <!-- Menú Desplegable de Sesión -->
                        <div x-show="profileOpen" @click.away="profileOpen = false" 
                             class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 text-sm"
                             style="display: none;">
                            <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                                <p class="font-bold text-slate-800">{{ Auth::user()->name ?? 'Saúl' }}</p>
                                <p class="text-xs text-slate-400">{{ Auth::user()->email ?? '' }}</p>
                            </div>
                            <a href="#" class="block px-4 py-2 text-slate-600 hover:bg-slate-50 transition-colors">Mi Perfil</a>
                            <a href="#" class="block px-4 py-2 text-slate-600 hover:bg-slate-50 transition-colors">Configuración</a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 transition-colors font-medium">
                                    Cerrar sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenido Dinámico de las Vistas -->
            <main class="flex-1 p-6 lg:p-8 max-w-7xl w-full mx-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script para control responsivo del Sidebar en Móviles -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('flex');
            sidebar.classList.toggle('w-full');
            sidebar.classList.toggle('bg-slate-900/95');
            sidebar.classList.toggle('backdrop-blur-md');
        }
    </script>
</body>
</html>