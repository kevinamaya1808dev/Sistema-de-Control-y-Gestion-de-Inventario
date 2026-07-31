<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SCGI - Control y Gestión de Inventarios')</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="h-dvh w-full max-w-full overflow-hidden overscroll-none font-sans antialiased selection:bg-orange-500 selection:text-white">

    <x-app-container class="h-dvh flex overflow-hidden">

        <div x-data="{
                 sidebarOpen: false,
                 sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                 isDesktop: window.matchMedia('(min-width: 1024px)').matches,
                 toggleCollapse() {
                     this.sidebarCollapsed = !this.sidebarCollapsed;
                     localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
                 },
                 get railCollapsed() {
                     return this.sidebarCollapsed && this.isDesktop;
                 }
             }"
             x-init="window.addEventListener('resize', () => { isDesktop = window.matchMedia('(min-width: 1024px)').matches })"
             class="h-dvh flex w-full max-w-full overflow-hidden">

            {{-- Overlay Móvil --}}
            <div x-show="sidebarOpen"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-neutral-900/40 backdrop-blur-xs lg:hidden"
                 style="display: none;"></div>

            <!-- SIDEBAR -->
            <aside :class="{
                        'translate-x-0': sidebarOpen,
                        '-translate-x-full': !sidebarOpen,
                        'lg:w-72': !railCollapsed,
                        'lg:w-20': railCollapsed
                    }"
                    class="fixed lg:static lg:translate-x-0 inset-y-0 left-0 z-50 w-72 bg-white text-neutral-600 flex-shrink-0 flex flex-col border-r border-neutral-200/80 h-dvh transition-all duration-300 ease-in-out overflow-hidden shadow-xs">

                {{-- LOGO Y TÍTULO --}}
                <div :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'justify-between px-6'"
                     class="h-20 flex items-center bg-neutral-50/80 border-b border-neutral-200/80 flex-shrink-0">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-orange-600 text-white font-extrabold flex items-center justify-center text-xl shadow-md shadow-orange-600/30 flex-shrink-0">
                            S
                        </div>
                        <div x-show="!railCollapsed" x-transition.opacity class="lg:block min-w-0">
                            <span class="font-bold text-neutral-900 text-base tracking-tight block whitespace-nowrap">SCGI Negocios</span>
                            <span class="text-xs text-neutral-500 block whitespace-nowrap">Control de Inventarios</span>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" x-show="!railCollapsed" class="lg:hidden text-neutral-400 hover:text-neutral-600 p-1 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- NAVEGACIÓN --}}
                <nav class="flex-1 px-4 py-4 space-y-4 overflow-y-auto overflow-x-hidden [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">

                    {{-- GENERAL --}}
                    <div class="space-y-1">
                        <p x-show="!railCollapsed" x-transition.opacity class="px-3 text-[11px] font-extrabold uppercase tracking-wider text-neutral-400 mb-1.5 whitespace-nowrap">General</p>

                        @can('manage-dashboard')
                            <a href="{{ route('dashboard') }}" title="Dashboard"
                               :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'px-4'"
                               class="flex items-center gap-3.5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-neutral-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Dashboard</span>
                            </a>
                        @endcan
                    </div>

                    {{-- OPERACIONES E INVENTARIO --}}
                    <div class="space-y-1">
                        <p x-show="!railCollapsed" x-transition.opacity class="px-3 text-[11px] font-extrabold uppercase tracking-wider text-neutral-400 mb-1.5 whitespace-nowrap">Operaciones e Inventario</p>

                        @can('process-sales')
                            <a href="{{ route('pos.index') }}" title="Punto de Venta (POS)"
                               :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'px-4'"
                               class="flex items-center gap-3.5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('pos.*') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('pos.*') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Punto de Venta (POS)</span>
                            </a>
                        @endcan

                        @can('manage-categories')
                            <a href="{{ route('categories.index') }}" title="Categorías"
                               :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'px-4'"
                               class="flex items-center gap-3.5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('categories.*') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-neutral-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Categorías</span>
                            </a>
                        @endcan

                        @can('manage-products')
                            <a href="{{ route('products.index') }}" title="Productos"
                               :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'px-4'"
                               class="flex items-center gap-3.5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('products.*') ? 'text-white' : 'text-neutral-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Productos</span>
                            </a>
                        @endcan

                        <a href="{{ route('caja.index') }}" title="Control de Caja"
                           :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'justify-between px-4'"
                           class="flex items-center py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('caja.index') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                            <div class="flex items-center gap-3.5">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('caja.index') ? 'text-white' : 'text-emerald-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Control de Caja</span>
                            </div>
                            <template x-if="!railCollapsed">
                                <span>
                                    @if(auth()->user()->cajaActiva())
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block shadow-xs shadow-emerald-500" title="Caja Abierta"></span>
                                    @else
                                        <span class="text-[10px] bg-rose-50 text-rose-600 px-2 py-0.5 rounded-md font-bold border border-rose-200">Cerrada</span>
                                    @endif
                                </span>
                            </template>
                        </a>

                        @can('register-movements')
                            <a href="{{ route('stock.index') }}" title="Movimientos de Stock"
                               :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'px-4'"
                               class="flex items-center gap-3.5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('stock.*') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('stock.*') ? 'text-white' : 'text-neutral-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Movimientos de Stock</span>
                            </a>
                        @endcan
                    </div>

                    {{-- ADMINISTRACIÓN --}}
                    @can('manage-users')
                        <div class="space-y-1">
                            <p x-show="!railCollapsed" x-transition.opacity class="px-3 text-[11px] font-extrabold uppercase tracking-wider text-neutral-400 mb-1.5 whitespace-nowrap">Administración</p>

                            <a href="{{ route('caja.historial') }}" title="Historial de Turnos"
                               :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'px-4'"
                               class="flex items-center gap-3.5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('caja.historial*') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('caja.historial*') ? 'text-white' : 'text-neutral-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Historial de Turnos</span>
                            </a>

                            <a href="{{ route('users.index') }}" title="Gestión de Usuarios"
                               :class="railCollapsed ? 'lg:justify-center lg:px-0' : 'px-4'"
                               class="flex items-center gap-3.5 py-2.5 rounded-xl text-sm font-bold whitespace-nowrap transition-all duration-150 {{ request()->routeIs('users.*') ? 'bg-orange-600 text-white shadow-md shadow-orange-600/25' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                                <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('users.*') ? 'text-white' : 'text-neutral-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span x-show="!railCollapsed" x-transition.opacity>Gestión de Usuarios</span>
                            </a>
                        </div>
                    @endcan

                </nav>

                {{-- USUARIO LOGUEADO --}}
                <div :class="railCollapsed ? 'lg:justify-center' : 'justify-between'"
                     class="p-4 bg-neutral-50/80 border-t border-neutral-200/80 flex items-center flex-shrink-0">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-xl bg-orange-100 border border-orange-200 flex items-center justify-center font-bold text-orange-600 text-xs flex-shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                        </div>
                        <div x-show="!railCollapsed" x-transition.opacity class="truncate">
                            <p class="text-xs font-bold text-neutral-900 truncate">{{ auth()->user()->name ?? 'Usuario SCGI' }}</p>
                            <p class="text-[11px] font-medium text-neutral-400 truncate">{{ auth()->user()->email ?? 'admin@scgi.local' }}</p>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ÁREA DERECHA -->
            <div class="flex-1 flex flex-col min-w-0 max-w-full h-dvh overflow-hidden">

                {{-- HEADER FIJO --}}
                <header class="h-20 bg-white border-b border-neutral-200/80 px-4 md:px-8 flex items-center justify-between z-10 w-full min-w-0 flex-shrink-0">

                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-neutral-600 hover:text-neutral-900 p-2 rounded-xl border border-neutral-200 bg-neutral-50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>

                        <button @click="toggleCollapse()" class="hidden lg:flex items-center justify-center p-2 rounded-xl text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-colors" title="Colapsar / Expandir menú">
                            <svg class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-180': railCollapsed }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h12M4 18h16"/></svg>
                        </button>

                        <h1 class="text-base md:text-lg font-bold text-neutral-900 tracking-tight truncate">
                            @yield('header_title', 'Panel General')
                        </h1>
                    </div>

                    <div class="flex items-center gap-2 md:gap-4 flex-shrink-0">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-3 md:px-4 py-2 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl transition-colors border border-rose-200/60">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                <span class="hidden sm:inline">Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </header>

                {{-- MAIN --}}
                <main class="flex-1 min-w-0 max-w-full overflow-y-auto overflow-x-hidden [scrollbar-width:none] [&::-webkit-scrollbar]:hidden p-3 sm:p-4 md:p-8 w-full">
                    <div class="w-full max-w-7xl mx-auto box-border min-w-0">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

    </x-app-container>

    <x-toast-alerts />
    @stack('scripts')
</body>
</html>