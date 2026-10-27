<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">Projetos</h2>
            <a href="{{ route('projetos.create') }}"
               class="inline-flex items-center justify-center min-h-12 px-5 text-lg font-semibold text-white bg-rose-700 rounded-md hover:bg-rose-800 focus:ring-2 focus:ring-rose-600 focus:ring-offset-2">
                Novo projeto
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">
        <form method="GET" action="{{ route('projetos.index') }}" class="bg-white rounded-lg shadow p-4 flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1">
                <x-input-label for="status" value="Filtrar por status" />
                <x-select name="status" id="status" class="w-full mt-1">
                    <option value="">Todos</option>
                    @foreach ($statusDisponiveis as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected($status === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </x-select>
            </div>
            <div class="flex gap-2">
                <x-primary-button class="min-h-12">Filtrar</x-primary-button>
                @if ($status)
                    <a href="{{ route('projetos.index') }}"
                       class="inline-flex items-center justify-center min-h-12 px-5 text-lg text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Limpar
                    </a>
                @endif
            </div>
        </form>

        @if ($projetos->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <p class="text-lg text-gray-700">Nenhum projeto encontrado.</p>
                <a href="{{ route('projetos.create') }}"
                   class="inline-flex items-center justify-center min-h-12 px-5 mt-4 text-lg font-semibold text-white bg-rose-700 rounded-md hover:bg-rose-800">
                    Criar primeiro projeto
                </a>
            </div>
        @else
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4" role="list">
                @foreach ($projetos as $projeto)
                    <li class="bg-white rounded-lg shadow p-5 border border-rose-100">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <a href="{{ route('projetos.show', $projeto) }}" class="text-lg font-semibold text-rose-700 hover:underline">
                                    {{ $projeto->titulo }}
                                </a>
                                <p class="text-sm text-gray-600">Receita: {{ $projeto->receita->titulo }}</p>
                            </div>
                            <x-status-badge :status="$projeto->status" />
                        </div>
                        <div class="mt-4">
                            <x-barra-progresso :valor="$projeto->calcularProgresso()" />
                        </div>
                    </li>
                @endforeach
            </ul>

            <div>{{ $projetos->links() }}</div>
        @endif
    </div>
</x-app-layout>
