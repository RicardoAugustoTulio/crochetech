<x-impressao-layout>
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Relatório de projetos</h1>
    <p class="text-sm text-gray-600 mb-1">Gerado em {{ now()->format('d/m/Y H:i') }}</p>

    @php
        $dataBr = fn ($d) => rescue(fn () => \Illuminate\Support\Carbon::parse($d)->format('d/m/Y'), $d, false);
        $aplicados = array_filter([
            isset(\App\Models\Projeto::STATUS[$filtros['status'] ?? '']) ? 'Status: '.\App\Models\Projeto::STATUS[$filtros['status']] : null,
            ($r = $receitas->firstWhere('id', $filtros['receita_id'] ?? null)) ? 'Receita: '.$r->titulo : null,
            ! empty($filtros['de']) ? 'Criado de: '.$dataBr($filtros['de']) : null,
            ! empty($filtros['ate']) ? 'Criado até: '.$dataBr($filtros['ate']) : null,
        ]);
    @endphp
    <p class="text-sm text-gray-600 mb-6">Filtros: {{ $aplicados ? implode(' · ', $aplicados) : 'nenhum (todos os projetos)' }}</p>

    {{-- Filtros (mesmos do dashboard), ocultos na impressão --}}
    <form method="GET" action="{{ route('relatorios.projetos') }}" class="no-print mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end border border-gray-200 rounded-md p-4">
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
            <a href="{{ route('relatorios.projetos') }}" class="inline-flex items-center min-h-12 px-5 text-lg text-gray-700 underline">Limpar</a>
        </div>
    </form>

    @if ($projetos->isEmpty())
        <p class="text-lg text-gray-700">Nenhum projeto encontrado para os filtros aplicados.</p>
    @else
        <table class="min-w-full border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 text-left">Projeto</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Receita</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Status</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">%</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Criado em</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Concluído em</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projetos as $projeto)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $projeto->titulo }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $projeto->receita->titulo }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ \App\Models\Projeto::STATUS[$projeto->status] ?? $projeto->status }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ number_format($projeto->calcularProgresso(), 1, ',', '.') }}%</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $projeto->created_at?->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $projeto->data_conclusao?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-semibold">
                    <td class="border border-gray-300 px-3 py-2" colspan="3">
                        Total: {{ $totais['total'] }} {{ \Illuminate\Support\Str::plural('projeto', $totais['total']) }}
                        · {{ $totais['concluido'] }} {{ \Illuminate\Support\Str::plural('concluído', $totais['concluido']) }}
                    </td>
                    <td class="border border-gray-300 px-3 py-2 text-right">{{ number_format($totais['media'], 1, ',', '.') }}%</td>
                    <td class="border border-gray-300 px-3 py-2" colspan="2">Média de conclusão</td>
                </tr>
            </tfoot>
        </table>
    @endif
</x-impressao-layout>
