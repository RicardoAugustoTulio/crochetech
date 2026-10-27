<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">
                Editar carreira {{ $carreira->numero }}: {{ $receita->titulo }}
            </h2>
            <a href="{{ route('receitas.show', $receita) }}"
                class="text-rose-700 hover:text-rose-900 underline">Voltar à receita</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Dados da carreira --}}
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-900 mb-4">Dados da carreira</h3>
            <form method="POST" action="{{ route('carreiras.update', $carreira) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="numero" :value="'Número'" />
                    <x-text-input id="numero" name="numero" type="number" min="1" step="1"
                        class="mt-1 block w-full" :value="old('numero', $carreira->numero)" required />
                    <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="descricao" :value="'Descrição (opcional)'" />
                    <textarea id="descricao" name="descricao" rows="2"
                        class="mt-1 block w-full border-gray-300 focus:border-rose-600 focus:ring-rose-600 rounded-md shadow-sm text-lg">{{ old('descricao', $carreira->descricao) }}</textarea>
                    <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                </div>
                <x-primary-button>Salvar dados</x-primary-button>
            </form>
        </div>

        {{-- Pontos esperados --}}
        <div class="bg-rose-50 border border-rose-200 sm:rounded-lg p-4 text-gray-900">
            <span class="font-semibold">Pontos esperados nesta carreira:</span>
            {{ $carreira->pontosEsperados() }}
        </div>

        {{-- Pontos --}}
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-900 mb-4">Pontos</h3>

            @forelse ($carreira->pontos as $ponto)
                <div class="flex flex-wrap items-end gap-3 py-3 border-b border-gray-100">
                    <form method="POST" action="{{ route('pontos.update', $ponto) }}"
                        class="flex flex-wrap items-end gap-3 flex-1">
                        @csrf
                        @method('PUT')
                        <div class="w-full sm:w-48">
                            <x-input-label for="tipo_{{ $ponto->id }}" :value="'Tipo'" />
                            <x-select id="tipo_{{ $ponto->id }}" name="tipo" class="mt-1 block w-full">
                                @foreach (\App\Models\Ponto::TIPOS as $t)
                                    <option value="{{ $t }}" @selected($ponto->tipo === $t)>{{ ucfirst($t) }}</option>
                                @endforeach
                            </x-select>
                        </div>
                        <div class="w-24">
                            <x-input-label for="qtd_{{ $ponto->id }}" :value="'Qtd.'" />
                            <x-text-input id="qtd_{{ $ponto->id }}" name="quantidade" type="number" min="1" step="1"
                                class="mt-1 block w-full" :value="$ponto->quantidade" required />
                        </div>
                        <div class="flex-1 min-w-48">
                            <x-input-label for="inst_{{ $ponto->id }}" :value="'Instrução (opcional)'" />
                            <x-text-input id="inst_{{ $ponto->id }}" name="instrucao" type="text"
                                class="mt-1 block w-full" :value="$ponto->instrucao" />
                        </div>
                        <x-secondary-button>Atualizar</x-secondary-button>
                    </form>
                    <form method="POST" action="{{ route('pontos.destroy', $ponto) }}"
                        onsubmit="return confirm('Remover este ponto?')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Remover</x-danger-button>
                    </form>
                </div>
            @empty
                <p class="text-gray-600">Nenhum ponto cadastrado.</p>
            @endforelse

            <form method="POST" action="{{ route('carreiras.pontos.store', $carreira) }}"
                class="flex flex-wrap items-end gap-3 mt-4 pt-4 border-t border-gray-200">
                @csrf
                <div class="w-full sm:w-48">
                    <x-input-label for="novo_tipo" :value="'Tipo'" />
                    <x-select id="novo_tipo" name="tipo" class="mt-1 block w-full">
                        @foreach (\App\Models\Ponto::TIPOS as $t)
                            <option value="{{ $t }}" @selected(old('tipo') === $t)>{{ ucfirst($t) }}</option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                </div>
                <div class="w-24">
                    <x-input-label for="nova_qtd" :value="'Qtd.'" />
                    <x-text-input id="nova_qtd" name="quantidade" type="number" min="1" step="1"
                        class="mt-1 block w-full" :value="old('quantidade', 1)" required />
                    <x-input-error :messages="$errors->get('quantidade')" class="mt-2" />
                </div>
                <div class="flex-1 min-w-48">
                    <x-input-label for="nova_inst" :value="'Instrução (opcional)'" />
                    <x-text-input id="nova_inst" name="instrucao" type="text"
                        class="mt-1 block w-full" :value="old('instrucao')" />
                </div>
                <x-primary-button>Adicionar ponto</x-primary-button>
            </form>
        </div>

        {{-- Repetições --}}
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="font-semibold text-lg text-gray-900 mb-4">Repetições</h3>

            @forelse ($carreira->repeticoes as $repeticao)
                <div class="flex flex-wrap items-end gap-3 py-3 border-b border-gray-100">
                    <form method="POST" action="{{ route('repeticoes.update', $repeticao) }}"
                        class="flex flex-wrap items-end gap-3 flex-1">
                        @csrf
                        @method('PUT')
                        <div class="w-24">
                            <x-input-label for="rqtd_{{ $repeticao->id }}" :value="'Qtd.'" />
                            <x-text-input id="rqtd_{{ $repeticao->id }}" name="quantidade" type="number" min="1" step="1"
                                class="mt-1 block w-full" :value="$repeticao->quantidade" required />
                        </div>
                        <div class="flex-1 min-w-48">
                            <x-input-label for="rinst_{{ $repeticao->id }}" :value="'Instrução (opcional)'" />
                            <x-text-input id="rinst_{{ $repeticao->id }}" name="instrucao" type="text"
                                class="mt-1 block w-full" :value="$repeticao->instrucao" />
                        </div>
                        <x-secondary-button>Atualizar</x-secondary-button>
                    </form>
                    <form method="POST" action="{{ route('repeticoes.destroy', $repeticao) }}"
                        onsubmit="return confirm('Remover esta repetição?')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Remover</x-danger-button>
                    </form>
                </div>
            @empty
                <p class="text-gray-600">Nenhuma repetição cadastrada.</p>
            @endforelse

            <form method="POST" action="{{ route('carreiras.repeticoes.store', $carreira) }}"
                class="flex flex-wrap items-end gap-3 mt-4 pt-4 border-t border-gray-200">
                @csrf
                <div class="w-24">
                    <x-input-label for="nova_rqtd" :value="'Qtd.'" />
                    <x-text-input id="nova_rqtd" name="quantidade" type="number" min="1" step="1"
                        class="mt-1 block w-full" :value="old('quantidade', 1)" required />
                    <x-input-error :messages="$errors->get('quantidade')" class="mt-2" />
                </div>
                <div class="flex-1 min-w-48">
                    <x-input-label for="nova_rinst" :value="'Instrução (opcional)'" />
                    <x-text-input id="nova_rinst" name="instrucao" type="text"
                        class="mt-1 block w-full" :value="old('instrucao')" />
                </div>
                <x-primary-button>Adicionar repetição</x-primary-button>
            </form>
        </div>

    </div>
</x-app-layout>
