@props(['class' => ''])
<div {{ $attributes->merge([
    'class' => 'min-h-screen w-full bg-slate-50 text-slate-800 ' . $class
]) }}>
    {{ $slot }}
</div>