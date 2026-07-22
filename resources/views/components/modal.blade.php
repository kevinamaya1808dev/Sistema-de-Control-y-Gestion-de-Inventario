@props([
    'name', 
    'title', 
    'maxWidth' => 'max-w-md',
    'dotColor' => 'bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.8)]'
])

<div x-show="modals.{{ $name }}" 
     x-cloak
     class="fixed inset-0 z-50 bg-slate-950/70 dark:bg-slate-950/85 backdrop-blur-md flex items-center justify-center p-3 sm:p-5 overflow-hidden" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95">

    <div class="relative w-full {{ $maxWidth }} max-h-[90vh] bg-white dark:bg-[#090d18] text-slate-900 dark:text-slate-100 rounded-[28px] border border-slate-200/80 dark:border-slate-800/60 shadow-2xl flex flex-col overflow-hidden my-auto" 
         @click.outside="closeModal('{{ $name }}')">
        
        {{-- Resplandor Neón Sutil --}}
        <div class="hidden dark:block pointer-events-none absolute -top-20 -left-20 w-72 h-72 bg-indigo-600/15 rounded-full blur-3xl"></div>

        {{-- Header --}}
        <div class="relative z-10 flex items-center justify-between p-5 sm:px-7 border-b border-slate-100 dark:border-white/5 bg-transparent shrink-0">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                <h3 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                    {{ $title }}
                </h3>
            </div>
            <button type="button" @click="closeModal('{{ $name }}')" 
                    class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-900 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center justify-center font-bold transition-all cursor-pointer border border-slate-200/80 dark:border-slate-800/80">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{ $slot }}
    </div>
</div>