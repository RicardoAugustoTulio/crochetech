{{-- Comparação original × adaptada (ADP-06). Recebe $receita adaptada. --}}
@php
    $original = $receita->original;
@endphp

@if ($original)
    @php
        $original->load('carreiras.pontos', 'carreiras.repeticoes', 'materiais');
        $receita->loadMissing('carreiras.pontos', 'carreiras.repeticoes', 'materiais');

        $carreirasOriginal = $original->carreiras->keyBy('numero');
        $carreirasAdaptada = $receita->carreiras->keyBy('numero');
        $numerosCarreiras = $carreirasOriginal->keys()
            ->merge($carreirasAdaptada->keys())
            ->unique()
            ->sort()
            ->values();

        $materiaisOriginal = $original->materiais->keyBy('nome');
        $materiaisAdaptada = $receita->materiais->keyBy('nome');
        $nomesMateriais = $materiaisOriginal->keys()
            ->merge($materiaisAdaptada->keys())
            ->unique()
            ->values();
    @endphp

    <div id="comparacao" class="space-y-6">
        <h3 class="font-semibold text-lg text-gray-900">
            Comparação: {{ $original->titulo }} (original) × adaptada (fator
            {{ number_format($receita->fator_adaptacao, 2, ',', '.') }})
        </h3>

        <div class="overflow-x-auto">
            <h4 class="font-medium text-gray-900 mb-2">Pontos esperados por carreira</h4>
            <table class="min-w-full text-left text-gray-900">
                <caption class="sr-only">Comparação de pontos esperados por carreira</caption>
                <thead>
                    <tr class="border-b border-gray-300">
                        <th scope="col" class="py-2 pr-4">Carreira</th>
                        <th scope="col" class="py-2 pr-4">Original</th>
                        <th scope="col" class="py-2 pr-4">Adaptada</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($numerosCarreiras as $numero)
                        <tr class="border-b border-gray-200">
                            <th scope="row" class="py-2 pr-4 font-normal">Carreira {{ $numero }}</th>
                            <td class="py-2 pr-4">
                                {{ optional($carreirasOriginal->get($numero))->pontosEsperados() ?? '-' }}
                            </td>
                            <td class="py-2 pr-4">
                                {{ optional($carreirasAdaptada->get($numero))->pontosEsperados() ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($nomesMateriais->isNotEmpty())
            <div class="overflow-x-auto">
                <h4 class="font-medium text-gray-900 mb-2">Materiais</h4>
                <table class="min-w-full text-left text-gray-900">
                    <caption class="sr-only">Comparação de materiais</caption>
                    <thead>
                        <tr class="border-b border-gray-300">
                            <th scope="col" class="py-2 pr-4">Material</th>
                            <th scope="col" class="py-2 pr-4">Original</th>
                            <th scope="col" class="py-2 pr-4">Adaptada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nomesMateriais as $nome)
                            @php
                                $mo = $materiaisOriginal->get($nome);
                                $ma = $materiaisAdaptada->get($nome);
                            @endphp
                            <tr class="border-b border-gray-200">
                                <th scope="row" class="py-2 pr-4 font-normal">{{ $nome }}</th>
                                <td class="py-2 pr-4">
                                    {{ $mo ? number_format($mo->quantidade, 2, ',', '.').' '.$mo->unidade : '-' }}
                                </td>
                                <td class="py-2 pr-4">
                                    {{ $ma ? number_format($ma->quantidade, 2, ',', '.').' '.$ma->unidade : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif
