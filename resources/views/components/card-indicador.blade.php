@props(['titulo' => '', 'valor' => ''])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow p-5 border border-rose-100']) }}>
    <p class="text-sm text-gray-600">{{ $titulo }}</p>
    <p class="text-3xl font-bold text-rose-700 mt-1">{{ $valor }}</p>
</div>
