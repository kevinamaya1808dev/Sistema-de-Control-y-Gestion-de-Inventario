<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-100 dark:bg-slate-950 transition-colors duration-200">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SCGI - Control y Gestión de Inventarios')</title>
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="h-full font-sans antialiased text-slate-800 dark:text-slate-100 overflow-hidden bg-slate-100 dark:bg-slate-950">

    <div class="h-screen flex w-full bg-slate-100 dark:bg-slate-950 overflow-hidden">
        
        <aside class="w-72 bg-slate-900 dark:bg-black text-slate-300 flex-shrink-0 flex flex-col border-r border-slate-800 h-full">
            
            <div class="h-20 flex items-center px-6 bg-slate-950/50 border-b border-slate-800 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-xl shadow-lg shadow-indigo-600/30">
                        S
                    </div>
                    <div>
                        <span class="font-bold text-white text-base tracking-wider block">SCGI Negocios</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Control de Inventarios</span>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Menú Principal</p>
                
                {{-- DASHBOARD --}}
                @can('manage-dashboard')
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 dark:hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                @endcan

                {{-- CATEGORÍAS --}}
                @can('manage-categories')
                    <a href="{{ route('categories.index') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 dark:hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('categories.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Categorías
                    </a>
                @endcan

                {{-- PRODUCTOS --}}
                @can('manage-products')
                    <a href="{{ route('products.index') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('products.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 dark:hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('products.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Productos
                    </a>
                @endcan

                <p class="px-3 pt-6 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Operaciones y Administración</p>
                
                {{-- CONTROL DE CAJA --}}
                <a href="{{ route('caja.index') }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('caja.index') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 dark:hover:bg-slate-900 hover:text-white' }}">
                    <div class="flex items-center gap-3.5">
                        <svg class="w-5 h-5 {{ request()->routeIs('caja.index') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Control de Caja</span>
                    </div>
                    @if(auth()->user()->cajaActiva())
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse" title="Caja Abierta"></span>
                    @else
                        <span class="text-[10px] bg-rose-500/20 text-rose-300 px-2 py-0.5 rounded-md border border-rose-500/30">Cerrada</span>
                    @endif
                </a>

                {{-- HISTORIAL DE TURNOS Y CAJAS --}}
                @can('manage-users')
                    <a href="{{ route('caja.historial') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('caja.historial*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 dark:hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('caja.historial*') ? 'text-white' : 'text-amber-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Historial de Turnos</span>
                    </a>
                @endcan

                {{-- MOVIMIENTOS DE STOCK --}}
                @can('register-movements')
                    <a href="{{ route('stock.index') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('stock.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 dark:hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('stock.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Movimientos de Stock
                    </a>
                @endcan

                {{-- GESTIÓN DE USUARIOS --}}
                @can('manage-users')
                    <a href="{{ route('users.index') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 dark:hover:bg-slate-900 hover:text-white' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('users.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        Gestión de Usuarios
                    </a>
                @endcan
            </nav>

            <div class="p-4 bg-slate-950/40 border-t border-slate-800 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-10 h-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-white text-xs">
                        {{ substr(auth()->user()->name ?? 'U', 0, 2) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Usuario SCGI' }}</p>
                        <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@scgi.local' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 h-full">
            <header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 flex items-center justify-between shadow-xs z-10 w-full flex-shrink-0 transition-colors duration-200">
                <h1 class="text-lg font-extrabold text-slate-800 dark:text-white tracking-tight">@yield('header_title', 'Panel General')</h1>

                <div class="flex items-center gap-4">
                    <x-theme-toggle />
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 dark:bg-rose-950/30 dark:hover:bg-rose-950/60 dark:text-rose-400 rounded-xl transition-colors cursor-pointer border border-rose-100 dark:border-rose-900/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-6 md:p-8 overflow-y-auto w-full bg-slate-100 dark:bg-slate-950 transition-colors duration-200">
                <div class="w-full">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <x-toast-alerts />
    @stack('scripts')
</body>
</html>