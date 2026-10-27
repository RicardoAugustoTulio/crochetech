<x-impressao-layout>
    <h1 class="text-2xl font-bold text-gray-900 mb-1">Lista de materiais</h1>
    <p class="text-lg text-gray-800 mb-1">{{ $receita->titulo }}</p>
    <p class="text-sm text-gray-600 mb-6">
        {{ \App\Models\Receita::TIPO_ADAPTADA === $receita->tipo ? 'Receita adaptada' : 'Receita original' }}
        · Gerado em {{ now()->format('d/m/Y H:i') }}
    </p>

    @if ($materiais->isEmpty())
        <p class="text-lg text-gray-700">Esta receita não possui materiais cadastrados.</p>
    @else
        <table class="min-w-full border border-gray-300 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 text-left">Material</th>
                    <th class="border border-gray-300 px-3 py-2 text-right">Quantidade</th>
                    <th class="border border-gray-300 px-3 py-2 text-left">Unidade</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($materiais as $material)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $material->nome }}</td>
                        <td class="border border-gray-300 px-3 py-2 text-right">{{ number_format((float) $material->quantidade, 2, ',', '.') }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $material->unidade }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-impressao-layout>
