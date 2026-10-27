{{-- Materiais da receita. Espera $receita. Edição só em receita original (editarEstrutura). --}}
@php($podeEditar = auth()->user()?->can('editarEstrutura', $receita))
<div id="materiais" class="bg-white shadow-sm sm:rounded-lg p-6">
    <h3 class="font-semibold text-lg text-gray-900 mb-4">Materiais</h3>

    @forelse ($receita->materiais as $material)
        <div class="flex flex-wrap items-center justify-between gap-3 py-3 border-b border-gray-100">
            <span class="text-gray-900">
                {{ $material->nome }}:
                {{ rtrim(rtrim(number_format($material->quantidade, 2, ',', '.'), '0'), ',') }}
                {{ $material->unidade }}
            </span>
            @if ($podeEditar)
                <div class="flex items-center gap-2">
                    <a href="{{ route('materiais.edit', $material) }}"
                        class="text-rose-700 hover:text-rose-900 underline">Editar</a>
                    <form method="POST" action="{{ route('materiais.destroy', $material) }}"
                        onsubmit="return confirm('Remover este material?')">
                        @csrf
                        @method('DELETE')
                        <x-danger-button>Remover</x-danger-button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <p class="text-gray-600">Nenhum material cadastrado.</p>
    @endforelse

    @if ($podeEditar)
        <form method="POST" action="{{ route('receitas.materiais.store', $receita) }}"
            class="flex flex-wrap items-end gap-3 mt-4 pt-4 border-t border-gray-200">
            @csrf
            <div class="flex-1 min-w-48">
                <x-input-label for="mat_nome" :value="'Nome'" />
                <x-text-input id="mat_nome" name="nome" type="text" maxlength="100"
                    class="mt-1 block w-full" :value="old('nome')" />
                <x-input-error :messages="$errors->get('nome')" class="mt-2" />
            </div>
            <div class="w-28">
                <x-input-label for="mat_qtd" :value="'Quantidade'" />
                <x-text-input id="mat_qtd" name="quantidade" type="number" min="0.01" step="0.01"
                    class="mt-1 block w-full" :value="old('quantidade')" />
                <x-input-error :messages="$errors->get('quantidade')" class="mt-2" />
            </div>
            <div class="w-32">
                <x-input-label for="mat_unidade" :value="'Unidade'" />
                <x-select id="mat_unidade" name="unidade" class="mt-1 block w-full">
                    @foreach (\App\Models\Material::UNIDADES as $u)
                        <option value="{{ $u }}" @selected(old('unidade') === $u)>{{ $u }}</option>
                    @endforeach
                </x-select>
                <x-input-error :messages="$errors->get('unidade')" class="mt-2" />
            </div>
            <x-primary-button>Adicionar material</x-primary-button>
        </form>
    @endif
</div>
