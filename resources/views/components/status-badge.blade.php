@props(['status' => 'nao_iniciado'])

@php
    $rotulos = \App\Models\Projeto::STATUS;
    $cores = [
        'nao_iniciado' => 'bg-gray-100 text-gray-800',
        'em_andamento' => 'bg-amber-100 text-amber-800',
        'concluido' => 'bg-green-100 text-green-800',
    ];
    $rotulo = $rotulos[$status] ?? $status;
    $cor = $cores[$status] ?? 'bg-gray-100 text-gray-800';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-3 py-1 rounded-full text-sm font-medium $cor"]) }}>
    {{ $rotulo }}
</span>
