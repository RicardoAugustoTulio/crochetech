<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">{{ $receita->titulo }}</h2>
                @if ($receita->isAdaptada())
                    <p class="mt-1 text-gray-700">
                        Receita adaptada (fator ×{{ number_format((float) $receita->fator_adaptacao, 2, ',', '.') }})
                        @if ($receita->original)
                            a partir de
                            <a href="{{ route('receitas.show', $receita->original) }}" class="text-rose-700 font-medium hover:underline">{{ $receita->original->titulo }}</a>
                        @else
                            (a receita original foi removida)
                        @endif
                    </p>
                @else
                    <p class="mt-1 text-gray-700">Receita original</p>
                @endif
            </div>
            <a href="{{ route('receitas.index') }}" class="text-rose-700 font-medium hover:underline">← Voltar</a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Ações --}}
        <div class="flex flex-wrap gap-3">
            @unless ($receita->isAdaptada())
                <a href="{{ route('receitas.adaptar.create', $receita) }}"
                   class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md font-semibold hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:ring-offset-2">
                    Adaptar
                </a>
            @endunless
            <a href="{{ route('relatorios.materiais', $receita) }}"
               class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-gray-100 text-gray-800 rounded-md font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                Relatório de materiais
            </a>
            <a href="{{ route('projetos.create', ['receita_id' => $receita->id]) }}"
               class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-gray-100 text-gray-800 rounded-md font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                Novo projeto com esta receita
            </a>
            <a href="{{ route('receitas.edit', $receita) }}"
               class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-gray-100 text-gray-800 rounded-md font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                Editar
            </a>
            @php($totalProjetos = $receita->projetos()->count())
            <form method="POST" action="{{ route('receitas.destroy', $receita) }}"
                  onsubmit="return confirm('{{ $totalProjetos > 0 ? 'Esta receita tem '.$totalProjetos.' projeto(s) vinculado(s), que também serão excluídos. Deseja continuar?' : 'Tem certeza que deseja excluir esta receita?' }}')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-red-600 text-white rounded-md font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Excluir
                </button>
            </form>
        </div>

        @if ($receita->descricao)
            <div class="bg-white shadow sm:rounded-lg p-6">
                <h3 class="font-semibold text-lg text-gray-900 mb-2">Descrição</h3>
                <p class="text-lg text-gray-800 whitespace-pre-line">{{ $receita->descricao }}</p>
            </div>
        @endif

        {{-- Comparação (só adaptada) — partial de T5 --}}
        @if ($receita->isAdaptada())
            @include('receitas.partials.comparacao', ['receita' => $receita])
        @endif

        {{-- Carreiras --}}
        <div class="bg-white shadow sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-900 mb-4">Carreiras</h3>

            @if ($receita->carreiras->isEmpty())
                <p class="text-lg text-gray-700">Esta receita ainda não tem carreiras.</p>
            @else
                <ol class="space-y-4">
                    @foreach ($receita->carreiras as $carreira)
                        <li class="border border-gray-200 rounded-md p-4">
                            <div class="flex items-center justify-between gap-3 flex-wrap">
                                <h4 class="font-semibold text-lg text-gray-900">Carreira {{ $carreira->numero }}</h4>
                                <span class="text-gray-700">Pontos esperados: <strong>{{ $carreira->pontosEsperados() }}</strong></span>
                            </div>
                            @if ($carreira->descricao)
                                <p class="mt-1 text-gray-800">{{ $carreira->descricao }}</p>
                            @endif

                            @if ($carreira->pontos->isNotEmpty())
                                <div class="mt-3">
                                    <p class="text-sm font-semibold text-gray-700">Pontos</p>
                                    <ul class="list-disc list-inside text-gray-800">
                                        @foreach ($carreira->pontos as $ponto)
                                            <li>
                                                {{ $ponto->quantidade }}× {{ $ponto->tipo }}
                                                @if ($ponto->instrucao)
                                                    : <span class="text-gray-600">{{ $ponto->instrucao }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if ($carreira->repeticoes->isNotEmpty())
                                <div class="mt-3">
                                    <p class="text-sm font-semibold text-gray-700">Repetições</p>
                                    <ul class="list-disc list-inside text-gray-800">
                                        @foreach ($carreira->repeticoes as $repeticao)
                                            <li>
                                                {{ $repeticao->quantidade }}×
                                                @if ($repeticao->instrucao)
                                                    : <span class="text-gray-600">{{ $repeticao->instrucao }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @unless ($receita->isAdaptada())
                                <div class="mt-3">
                                    <a href="{{ route('carreiras.edit', $carreira) }}" class="text-rose-700 font-medium hover:underline">Editar carreira</a>
                                </div>
                            @endunless
                        </li>
                    @endforeach
                </ol>
            @endif

            @unless ($receita->isAdaptada())
                <div class="mt-4">
                    <a href="{{ route('receitas.carreiras.create', $receita) }}"
                       class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md font-semibold hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:ring-offset-2">
                        Adicionar carreira
                    </a>
                </div>
            @endunless
        </div>

        {{-- Materiais — partial de T4 --}}
        @include('receitas.partials.materiais', ['receita' => $receita])

    </div>
</x-app-layout>
