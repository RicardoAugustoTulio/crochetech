<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            <a href="{{ route('relatorios.projetos', $filtros) }}" class="text-rose-700 underline text-lg">Ver relatório</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Cartões (DSH-01) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <x-card-indicador titulo="Total de projetos" :valor="$totais['total']" />
                <x-card-indicador titulo="Não iniciados" :valor="$totais['nao_iniciado']" />
                <x-card-indicador titulo="Em andamento" :valor="$totais['em_andamento']" />
                <x-card-indicador titulo="Concluídos" :valor="$totais['concluido']" />
                <x-card-indicador titulo="Média de conclusão" :valor="number_format($mediaConclusao, 1, ',', '.') . '%'" />
                <x-card-indicador
                    titulo="Receitas"
                    :valor="$totalReceitas['total']"
                    title="{{ $totalReceitas['original'] }} originais · {{ $totalReceitas['adaptada'] }} adaptadas" />
            </div>
            <p class="text-sm text-gray-600 -mt-4">
                Receitas: {{ $totalReceitas['original'] }} {{ \Illuminate\Support\Str::plural('original', $totalReceitas['original']) }}
                · {{ $totalReceitas['adaptada'] }} {{ \Illuminate\Support\Str::plural('adaptada', $totalReceitas['adaptada']) }}
            </p>

            {{-- Filtros (DSH-02) --}}
            <form method="GET" action="{{ route('dashboard') }}" class="bg-white rounded-lg shadow p-5 border border-rose-100 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <x-input-label for="status" value="Status" />
                    <x-select id="status" name="status" class="w-full mt-1">
                        <option value="">Todos</option>
                        @foreach (\App\Models\Projeto::STATUS as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(($filtros['status'] ?? '') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label for="receita_id" value="Receita" />
                    <x-select id="receita_id" name="receita_id" class="w-full mt-1">
                        <option value="">Todas</option>
                        @foreach ($receitas as $receita)
                            <option value="{{ $receita->id }}" @selected((string) ($filtros['receita_id'] ?? '') === (string) $receita->id)>{{ $receita->titulo }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div>
                    <x-input-label for="de" value="Criado de" />
                    <x-text-input type="date" id="de" name="de" class="w-full mt-1 text-lg min-h-12" :value="$filtros['de'] ?? ''" />
                </div>
                <div>
                    <x-input-label for="ate" value="Criado até" />
                    <x-text-input type="date" id="ate" name="ate" class="w-full mt-1 text-lg min-h-12" :value="$filtros['ate'] ?? ''" />
                </div>
                <div class="flex gap-2">
                    <x-primary-button class="min-h-12">Filtrar</x-primary-button>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center min-h-12 px-5 text-lg text-gray-700 underline">Limpar</a>
                </div>
            </form>

            {{-- Tabela de projetos --}}
            @if ($projetos->isEmpty())
                {{-- Estado vazio (DSH-03) --}}
                <div class="bg-white rounded-lg shadow p-10 border border-rose-100 text-center">
                    <p class="text-lg text-gray-700">Nenhum projeto encontrado.</p>
                    <div class="mt-5 flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('receitas.create') }}" class="inline-flex items-center justify-center min-h-12 px-5 text-lg bg-rose-700 text-white rounded-md hover:bg-rose-800">Criar receita</a>
                        <a href="{{ route('projetos.create') }}" class="inline-flex items-center justify-center min-h-12 px-5 text-lg border border-rose-700 text-rose-700 rounded-md hover:bg-rose-50">Criar projeto</a>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow border border-rose-100 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-rose-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Projeto</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Receita</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 w-48">Progresso</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($projetos as $projeto)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('projetos.show', $projeto) }}" class="text-rose-700 underline text-lg">{{ $projeto->titulo }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-800">{{ $projeto->receita->titulo }}</td>
                                    <td class="px-4 py-3"><x-status-badge :status="$projeto->status" /></td>
                                    <td class="px-4 py-3"><x-barra-progresso :valor="$projeto->calcularProgresso()" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
