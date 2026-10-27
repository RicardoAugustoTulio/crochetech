<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">{{ $projeto->titulo }}</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('projetos.executar', $projeto) }}"
                   class="inline-flex items-center justify-center min-h-12 px-5 text-lg font-semibold text-white bg-rose-700 rounded-md hover:bg-rose-800 focus:ring-2 focus:ring-rose-600 focus:ring-offset-2">
                    Executar
                </a>
                <a href="{{ route('projetos.edit', $projeto) }}"
                   class="inline-flex items-center justify-center min-h-12 px-5 text-lg text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Editar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">
        {{-- Resumo --}}
        <section class="bg-white rounded-lg shadow p-6 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <x-status-badge :status="$projeto->status" />
                <span class="text-gray-700">Receita:
                    <a href="{{ route('receitas.show', $projeto->receita) }}" class="text-rose-700 hover:underline">{{ $projeto->receita->titulo }}</a>
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Progresso</p>
                <x-barra-progresso :valor="$progresso" />
            </div>
            @if ($projeto->data_conclusao)
                <p class="text-sm text-gray-600">Concluído em {{ $projeto->data_conclusao->format('d/m/Y H:i') }}</p>
            @endif
        </section>

        {{-- Carreiras --}}
        <section class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Carreiras</h3>
            @php $carreiras = $projeto->receita->carreiras; @endphp
            @if ($carreiras->isEmpty())
                <p class="text-gray-700">Esta receita ainda não possui carreiras.</p>
            @else
                <ul class="divide-y divide-gray-100" role="list">
                    @foreach ($carreiras as $c)
                        @php
                            $prog = $progressoPorCarreira->get($c->id);
                            $feitos = $prog?->pontos_feitos ?? 0;
                            $esperados = $c->pontosEsperados();
                            $concluida = (bool) ($prog?->concluida ?? false);
                        @endphp
                        <li class="py-3 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <a href="{{ route('projetos.executar', [$projeto, 'carreira' => $c->numero]) }}"
                                   class="text-lg font-medium text-rose-700 hover:underline">
                                    Carreira {{ $c->numero }}
                                </a>
                                <span class="text-sm text-gray-600 ml-2">{{ $feitos }} / {{ $esperados }} pontos</span>
                            </div>
                            @if ($concluida)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Concluída</span>
                            @elseif ($feitos > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">Em andamento</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Pendente</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Observações --}}
        <section class="bg-white rounded-lg shadow p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900">Observações</h3>

            <form method="POST" action="{{ route('projetos.observacoes.store', $projeto) }}" class="space-y-3">
                @csrf
                <div>
                    <x-input-label for="texto" value="Nova observação" />
                    <textarea id="texto" name="texto" rows="3" maxlength="1000" required
                              class="w-full mt-1 border-gray-300 focus:border-rose-600 focus:ring-rose-600 rounded-md shadow-sm text-lg">{{ old('texto') }}</textarea>
                    <x-input-error :messages="$errors->get('texto')" class="mt-2" />
                </div>
                <x-primary-button class="min-h-12">Registrar observação</x-primary-button>
            </form>

            @if ($projeto->observacoes->isEmpty())
                <p class="text-gray-700">Nenhuma observação registrada.</p>
            @else
                <ul class="space-y-3" role="list">
                    @foreach ($projeto->observacoes as $obs)
                        <li class="border border-gray-100 rounded-md p-4">
                            <div class="flex items-start justify-between gap-3">
                                <p class="text-gray-900 whitespace-pre-line">{{ $obs->texto }}</p>
                                <form method="POST" action="{{ route('observacoes.destroy', $obs) }}"
                                      onsubmit="return confirm('Excluir esta observação?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" aria-label="Excluir observação"
                                            class="min-h-12 px-3 text-rose-700 hover:underline">Excluir</button>
                                </form>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">{{ $obs->created_at->format('d/m/Y H:i') }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        {{-- Excluir projeto --}}
        <section>
            <form method="POST" action="{{ route('projetos.destroy', $projeto) }}"
                  onsubmit="return confirm('Excluir este projeto? Progressos e observações serão removidos.')">
                @csrf
                @method('DELETE')
                <x-danger-button class="min-h-12">Excluir projeto</x-danger-button>
            </form>
        </section>
    </div>
</x-app-layout>
