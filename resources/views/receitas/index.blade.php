<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">Receitas</h2>
            <a href="{{ route('receitas.create') }}"
               class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md font-semibold hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:ring-offset-2">
                Nova receita
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('receitas.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3 sm:items-end">
            <div class="flex-1">
                <x-input-label for="busca" value="Buscar por título" />
                <x-text-input id="busca" name="busca" type="search" class="block w-full mt-1 text-lg min-h-12"
                              :value="$busca" placeholder="Digite o título…" />
            </div>
            <div>
                <x-input-label for="tipo" value="Tipo" />
                <x-select id="tipo" name="tipo" class="block w-full mt-1">
                    <option value="">Todos</option>
                    <option value="original" @selected($tipo === 'original')>Original</option>
                    <option value="adaptada" @selected($tipo === 'adaptada')>Adaptada</option>
                </x-select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md font-semibold hover:bg-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-600 focus:ring-offset-2">
                    Filtrar
                </button>
                @if ($tipo || $busca)
                    <a href="{{ route('receitas.index') }}"
                       class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-gray-100 text-gray-800 rounded-md font-semibold hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2">
                        Limpar
                    </a>
                @endif
            </div>
        </form>

        @if ($receitas->isEmpty())
            <div class="bg-white shadow sm:rounded-lg p-8 text-center text-lg text-gray-700">
                <p>Nenhuma receita encontrada.</p>
                <a href="{{ route('receitas.create') }}" class="text-rose-700 font-semibold hover:underline">Criar a primeira receita</a>
            </div>
        @else
            <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Título</th>
                            <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tipo</th>
                            <th scope="col" class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Carreiras</th>
                            <th scope="col" class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($receitas as $receita)
                            <tr>
                                <td class="px-4 py-3 text-lg text-gray-900">
                                    <a href="{{ route('receitas.show', $receita) }}" class="text-rose-700 font-medium hover:underline">
                                        {{ $receita->titulo }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    @if ($receita->isAdaptada())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-rose-100 text-rose-800">Adaptada</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Original</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $receita->carreiras_count ?? $receita->carreiras()->count() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('receitas.show', $receita) }}" class="text-rose-700 font-medium hover:underline">Ver</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $receitas->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
