<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Editar projeto</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('projetos.update', $projeto) }}" class="bg-white rounded-lg shadow p-6 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="titulo" value="Título" />
                <x-text-input id="titulo" name="titulo" type="text" class="w-full mt-1" maxlength="150"
                              :value="old('titulo', $projeto->titulo)" required autofocus />
                <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="receita_id" value="Receita" />
                @if ($temProgresso)
                    <p class="mt-1 text-lg text-gray-900">{{ $projeto->receita->titulo }}</p>
                    <p class="text-sm text-gray-600">A receita não pode ser alterada porque o projeto já tem progresso.</p>
                @else
                    <x-select name="receita_id" id="receita_id" class="w-full mt-1" required>
                        <option value="">Selecione uma receita</option>
                        @foreach ($receitas as $receita)
                            <option value="{{ $receita->id }}"
                                    @selected((string) old('receita_id', $projeto->receita_id) === (string) $receita->id)>
                                {{ $receita->titulo }} ({{ \App\Models\Receita::TIPO_ADAPTADA === $receita->tipo ? 'adaptada' : 'original' }})
                            </option>
                        @endforeach
                    </x-select>
                    <x-input-error :messages="$errors->get('receita_id')" class="mt-2" />
                @endif
            </div>

            <div class="flex items-center gap-3">
                <x-primary-button class="min-h-12">Salvar</x-primary-button>
                <a href="{{ route('projetos.show', $projeto) }}"
                   class="inline-flex items-center justify-center min-h-12 px-5 text-lg text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
