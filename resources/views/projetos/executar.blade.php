<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                Executar: {{ $projeto->titulo }}
            </h2>
            <a href="{{ route('projetos.show', $projeto) }}"
               class="text-rose-700 underline text-lg">Voltar ao projeto</a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">
        @if ($total === 0)
            {{-- EXE-07: receita sem carreiras --}}
            <div class="bg-white rounded-lg shadow p-6 text-center space-y-4" role="status">
                <p class="text-lg text-gray-900">
                    Esta receita ainda não tem carreiras cadastradas, então não há nada para executar.
                </p>
                <a href="{{ route('receitas.carreiras.create', $projeto->receita) }}"
                   class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                    Cadastrar carreiras
                </a>
            </div>
        @else
            {{-- Progresso geral do projeto --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-lg font-semibold text-gray-900">Progresso do projeto</span>
                    <x-status-badge :status="$projeto->status" />
                </div>
                <x-barra-progresso :valor="$projeto->calcularProgresso()" />
            </div>

            {{-- Cabeçalho da carreira --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <h3 class="text-xl font-semibold text-gray-900">
                        Carreira {{ $posicao }} de {{ $total }}
                    </h3>
                    @if ($progresso->concluida)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            Concluída
                        </span>
                    @endif
                </div>

                @if ($carreira->descricao)
                    <p class="text-lg text-gray-900">{{ $carreira->descricao }}</p>
                @endif

                {{-- Pontos --}}
                @if ($carreira->pontos->isNotEmpty())
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Pontos</h4>
                        <ul class="list-disc pl-6 space-y-1 text-lg text-gray-900">
                            @foreach ($carreira->pontos as $ponto)
                                <li>
                                    {{ $ponto->quantidade }} × {{ $ponto->tipo }}
                                    @if ($ponto->instrucao)
                                        <span class="text-gray-700">: {{ $ponto->instrucao }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Repetições --}}
                @if ($carreira->repeticoes->isNotEmpty())
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Repetições</h4>
                        <ul class="list-disc pl-6 space-y-1 text-lg text-gray-900">
                            @foreach ($carreira->repeticoes as $repeticao)
                                <li>
                                    {{ $repeticao->quantidade }}×
                                    @if ($repeticao->instrucao)
                                        <span class="text-gray-700">: {{ $repeticao->instrucao }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Contador grande --}}
                @php $esperados = $carreira->pontosEsperados(); @endphp
                <div class="text-center py-4">
                    <div class="text-5xl font-bold text-rose-700" aria-live="polite">
                        {{ $progresso->pontos_feitos }} / {{ $esperados }}
                    </div>
                    <div class="text-lg text-gray-700 mt-1">pontos feitos / esperados</div>
                </div>

                {{-- Botões -1 / +1 --}}
                <div class="flex items-center justify-center gap-4">
                    <form method="POST" action="{{ route('projetos.executar.pontos', [$projeto, $carreira]) }}">
                        @csrf
                        <input type="hidden" name="delta" value="-1">
                        <button type="submit"
                                aria-label="Remover um ponto"
                                @disabled($progresso->pontos_feitos <= 0)
                                class="min-h-12 min-w-16 px-6 text-2xl font-bold bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed">
                            −1
                        </button>
                    </form>
                    <form method="POST" action="{{ route('projetos.executar.pontos', [$projeto, $carreira]) }}">
                        @csrf
                        <input type="hidden" name="delta" value="1">
                        <button type="submit"
                                aria-label="Adicionar um ponto"
                                @disabled($progresso->pontos_feitos >= $esperados)
                                class="min-h-12 min-w-16 px-6 text-2xl font-bold bg-rose-700 text-white rounded-md hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed">
                            +1
                        </button>
                    </form>
                </div>

                {{-- Concluir / Desfazer carreira --}}
                <form method="POST" action="{{ route('projetos.executar.concluir', [$projeto, $carreira]) }}">
                    @csrf
                    <button type="submit"
                            class="w-full min-h-12 px-5 text-lg font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2
                                {{ $progresso->concluida ? 'bg-gray-200 text-gray-900 hover:bg-gray-300' : 'bg-green-700 text-white hover:bg-green-800' }}">
                        {{ $progresso->concluida ? 'Desfazer conclusão da carreira' : 'Concluir carreira' }}
                    </button>
                </form>

                {{-- Navegação Anterior / Próxima --}}
                <div class="flex items-center justify-between gap-4 pt-2">
                    @if ($anterior)
                        <a href="{{ route('projetos.executar', ['projeto' => $projeto, 'carreira' => $anterior->numero]) }}"
                           class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-white border border-gray-300 text-gray-900 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                            ← Anterior
                        </a>
                    @else
                        <span aria-disabled="true"
                              class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                            ← Anterior
                        </span>
                    @endif

                    @if ($proxima)
                        <a href="{{ route('projetos.executar', ['projeto' => $projeto, 'carreira' => $proxima->numero]) }}"
                           class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-white border border-gray-300 text-gray-900 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                            Próxima →
                        </a>
                    @else
                        <span aria-disabled="true"
                              class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-gray-100 text-gray-400 rounded-md cursor-not-allowed">
                            Próxima →
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
