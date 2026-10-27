@props(['valor' => 0])

@php
    $pct = max(0, min(100, (float) $valor));
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <div class="w-full bg-rose-100 rounded-full h-4 overflow-hidden" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progresso: {{ number_format($pct, 1, ',', '.') }}%">
        <div class="bg-rose-700 h-4 rounded-full transition-all" style="width: {{ $pct }}%"></div>
    </div>
    <span class="text-sm text-gray-700">{{ number_format($pct, 1, ',', '.') }}%</span>
</div>
