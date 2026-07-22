<div class="relative space-y-6">

    <!-- ORBES DE LUZ NEÓN DE FONDO (Sistema de iluminación glassmorphism universal) -->
    <div class="pointer-events-none absolute -top-12 left-6 h-72 w-72 rounded-full bg-indigo-500/25 blur-3xl dark:bg-indigo-500/20"></div>
    <div class="pointer-events-none absolute top-36 right-6 h-80 w-80 rounded-full bg-emerald-500/20 blur-3xl dark:bg-emerald-500/15"></div>
    <div class="pointer-events-none absolute top-96 left-1/3 h-80 w-80 rounded-full bg-rose-500/20 blur-3xl dark:bg-rose-500/15"></div>

    <!-- CONTENIDO DE LA VISTA -->
    <div class="relative">
        {{ $slot }}
    </div>

</div>