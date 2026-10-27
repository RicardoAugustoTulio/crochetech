<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Editar receita</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg p-6">
            <form method="POST" action="{{ route('receitas.update', $receita) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="titulo" value="Título" />
                    <x-text-input id="titulo" name="titulo" type="text" maxlength="150" required autofocus
                                  class="block w-full mt-1 text-lg min-h-12" :value="old('titulo', $receita->titulo)" />
                    <x-input-error :messages="$errors->get('titulo')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="descricao" value="Descrição (opcional)" />
                    <textarea id="descricao" name="descricao" rows="4"
                              class="block w-full mt-1 text-lg border-gray-300 focus:border-rose-600 focus:ring-rose-600 rounded-md shadow-sm">{{ old('descricao', $receita->descricao) }}</textarea>
                    <x-input-error :messages="$errors->get('descricao')" class="mt-2" />
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md font-semibold hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:ring-offset-2">
                        Salvar alterações
                    </button>
                    <a href="{{ route('receitas.show', $receita) }}"
                       class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-gray-100 text-gray-800 rounded-md font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
