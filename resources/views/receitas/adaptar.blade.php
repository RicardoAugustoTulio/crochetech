<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Adaptar receita: {{ $receita->titulo }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <div class="mb-6 rounded-md bg-rose-50 p-4 text-gray-900">
                <h3 class="font-semibold mb-2">Como funciona a adaptação</h3>
                <p class="text-sm">
                    Informe um <strong>fator de 0,1 a 10</strong>. Nas carreiras com repetição, o
                    número de repetições é multiplicado pelo fator; nas demais, a quantidade de cada
                    ponto. O resultado é arredondado para o inteiro mais próximo, com o
                    <strong>mínimo de 1</strong> (nunca zera um ponto), e o anel mágico não muda. As
                    quantidades de materiais são multiplicadas e arredondadas para 2 casas decimais.
                </p>
                <ul class="text-sm mt-2 list-disc list-inside space-y-1">
                    <li>Fator <strong>2</strong>: 6 pontos → 12; 20 g de fio → 40 g (dobro).</li>
                    <li>Fator <strong>0,5</strong>: 6 pontos → 3; 5 pontos → 3 (arredonda 2,5).</li>
                    <li>Fator <strong>1</strong>: gera uma cópia idêntica.</li>
                </ul>
                <p class="text-sm mt-2">
                    A receita original <strong>não é alterada</strong>: uma nova receita adaptada é criada.
                </p>
            </div>

            <form method="POST" action="{{ route('receitas.adaptar.store', $receita) }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="fator" value="Fator de adaptação" />
                    <x-text-input
                        id="fator"
                        name="fator"
                        type="text"
                        inputmode="decimal"
                        class="block mt-1 w-full"
                        :value="old('fator', '2')"
                        required
                        autofocus
                        aria-describedby="fator-ajuda" />
                    <p id="fator-ajuda" class="text-sm text-gray-600 mt-1">
                        Use ponto ou vírgula (ex.: 2 para dobrar, 0,5 para reduzir à metade).
                    </p>
                    <x-input-error :messages="$errors->get('fator')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="titulo" value="Título da receita adaptada (opcional)" />
                    <x-text-input
                        id="titulo"
                        name="titulo"
                        type="text"
                        class="block mt-1 w-full"
                        :value="old('titulo')"
                        maxlength="150"
                        placeholder="Sugestão: {{ $receita->titulo }} (×fator)" />
                    <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button class="min-h-12 px-5 text-lg">
                        Gerar receita adaptada
                    </x-primary-button>
                    <a href="{{ route('receitas.show', $receita) }}"
                       class="text-rose-700 hover:text-rose-900 underline text-lg">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
