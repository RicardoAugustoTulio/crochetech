<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">
            Editar material: {{ $receita->titulo }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <form method="POST" action="{{ route('materiais.update', $material) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="nome" :value="'Nome'" />
                    <x-text-input id="nome" name="nome" type="text" maxlength="100"
                        class="mt-1 block w-full" :value="old('nome', $material->nome)" required autofocus />
                    <x-input-error :messages="$errors->get('nome')" class="mt-2" />
                </div>

                <div class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-32">
                        <x-input-label for="quantidade" :value="'Quantidade'" />
                        <x-text-input id="quantidade" name="quantidade" type="number" min="0.01" step="0.01"
                            class="mt-1 block w-full" :value="old('quantidade', $material->quantidade)" required />
                        <x-input-error :messages="$errors->get('quantidade')" class="mt-2" />
                    </div>
                    <div class="flex-1 min-w-32">
                        <x-input-label for="unidade" :value="'Unidade'" />
                        <x-select id="unidade" name="unidade" class="mt-1 block w-full">
                            @foreach (\App\Models\Material::UNIDADES as $u)
                                <option value="{{ $u }}" @selected(old('unidade', $material->unidade) === $u)>{{ $u }}</option>
                            @endforeach
                        </x-select>
                        <x-input-error :messages="$errors->get('unidade')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>Salvar material</x-primary-button>
                    <a href="{{ route('receitas.show', $receita) }}#materiais"
                        class="text-gray-600 hover:text-gray-900 underline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
