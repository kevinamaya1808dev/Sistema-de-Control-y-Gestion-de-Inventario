<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SCGI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 antialiased font-sans">

    <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-xl p-8 space-y-6">
        <div class="text-center space-y-2">
            <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-600 text-white text-xl font-bold shadow-md">
                
            </span>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Ingresar al Sistema</h2>
            <p class="text-sm text-slate-500">SCGI - Control y Gestión de Inventarios</p>
        </div>

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 text-sm p-4 rounded-xl space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="font-medium">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Correo Electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                    </span>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="tu@correo.com" 
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="password" class="text-xs font-semibold uppercase tracking-wider text-slate-500 block">Contraseña</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                    <input type="password" id="password" name="password" required
                        placeholder="••••••••" 
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white transition-all">
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs text-slate-600 font-medium">Recordar sesión</span>
                </label>
            </div>

            <button type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-xl shadow-md cursor-pointer transition-colors pt-2">
                Iniciar Sesión
            </button>
        </form>
    </div>

</body>
</html>