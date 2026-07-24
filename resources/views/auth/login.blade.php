<!DOCTYPE html>
<html lang="es" class="h-full dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SCGI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#070a11] text-slate-100 font-sans antialiased flex items-center justify-center p-4 relative overflow-hidden">

    {{-- Efectos de Luz de Fondo (Glow Orbs) --}}
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative w-full max-w-md bg-[#0d121f]/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-slate-800/80 p-8 sm:p-10 z-10">
        
        {{-- ENCABEZADO / LOGO --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white font-black text-2xl tracking-widest shadow-lg shadow-indigo-500/25 ring-1 ring-white/20 mb-4">
                S
            </div>
            <h1 class="text-2xl font-black tracking-wide text-white uppercase">SCGI Negocios</h1>
            <p class="text-xs font-semibold text-slate-400 mt-1">Sistema de Control y Gestión de Inventarios</p>
        </div>

        {{-- ALERTA DE ERRORES --}}
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs animate-fade-in">
                <div class="font-bold flex items-center gap-2 mb-1 text-sm">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Acceso denegado</span>
                </div>
                <p class="text-slate-400">Las credenciales ingresadas no coinciden con nuestros registros.</p>
            </div>
        @endif

        {{-- FORMULARIO DE ACCESO --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- CORREO ELECTRÓNICO --}}
            <div class="space-y-1.5">
                <label for="email" class="block text-[11px] font-black uppercase tracking-wider text-slate-300">Correo Electrónico</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </span>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="usuario@empresa.com"
                           class="w-full pl-11 pr-4 py-3 bg-[#070a11] border border-slate-800 rounded-xl text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all shadow-inner">
                </div>
            </div>

            {{-- CONTRASEÑA --}}
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-[11px] font-black uppercase tracking-wider text-slate-300">Contraseña</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300 transition-colors">¿Olvidaste tu contraseña?</a>
                    @endif
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500 pointer-events-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                    
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="••••••••"
                           class="w-full pl-11 pr-12 py-3 bg-[#070a11] border border-slate-800 rounded-xl text-sm text-slate-100 placeholder-slate-600 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all shadow-inner">
                    
                    <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500 hover:text-slate-300 transition-colors cursor-pointer focus:outline-none" title="Mostrar/Ocultar contraseña">
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- RECORDAR SESIÓN --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="flex items-center gap-2.5 cursor-pointer select-none group">
                    <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-[#070a11] text-indigo-600 focus:ring-indigo-500 focus:ring-offset-[#0d121f] cursor-pointer">
                    <span class="text-xs font-medium text-slate-400 group-hover:text-slate-300 transition-colors">Recordar sesión</span>
                </label>
            </div>

            {{-- BOTÓN SUBMIT --}}
            <button type="submit" class="w-full mt-2 py-3.5 px-4 bg-indigo-600 hover:bg-indigo-500 active:scale-[0.99] text-white font-black uppercase text-xs tracking-wider rounded-xl shadow-lg shadow-indigo-600/25 transition-all cursor-pointer">
                Iniciar Sesión
            </button>
        </form>

        {{-- PIE DE PÁGINA --}}
        <div class="mt-8 pt-6 border-t border-slate-800/60 text-center">
            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">
                SCGI &copy; 2026 &bull; Todos los derechos reservados
            </p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>