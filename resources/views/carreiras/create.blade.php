<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Nova carreira: {{ $receita->titulo }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('receitas.carreiras.store', $receita) }}" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="numero" :value="'Número da carreira'" />
                    <x-text-input id="numero" name="numero" type="number" min="1" step="1"
                        class="mt-1 block w-full"
                        :value="old('numero', $numeroSugerido)" required autofocus />
                    <x-input-error :messages="$errors->get('numero')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="descricao" :value="'Descrição (opcional)'" />
                    <textarea id="descricao" name="descricao" rows="3"
                        class="mt-1 block w-full border-gray-300 focus:border-rose-600 focus:ring-rose-600 rounded-md shadow-sm text-lg">{{ old('descricao') }}</textarea>
                    <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Salvar carreira</x-primary-button>
                    <a href="{{ route('receitas.show', $receita) }}"
                        class="text-gray-600 hover:text-gray-900 underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
