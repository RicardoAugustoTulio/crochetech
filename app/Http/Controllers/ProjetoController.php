<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjetoRequest;
use App\Models\Projeto;
use Illuminate\Http\Request;

class ProjetoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $projetos = $request->user()->projetos()
            ->with('receita')
            ->when(
                in_array($status, array_keys(Projeto::STATUS), true),
                fn ($q) => $q->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('projetos.index', [
            'projetos' => $projetos,
            'status' => $status,
            'statusDisponiveis' => Projeto::STATUS,
        ]);
    }

    public function create(Request $request)
    {
        $receitas = $request->user()->receitas()->orderBy('titulo')->get();

        return view('projetos.create', [
            'receitas' => $receitas,
            'receitaSelecionada' => $request->query('receita_id'),
        ]);
    }

    public function store(ProjetoRequest $request)
    {
        $projeto = $request->user()->projetos()->create([
            'titulo' => $request->validated('titulo'),
            'receita_id' => $request->validated('receita_id'),
            'status' => 'nao_iniciado',
        ]);

        return redirect()
            ->route('projetos.show', $projeto)
            ->with('sucesso', 'Projeto criado com sucesso.');
    }

    public function show(Projeto $projeto)
    {
        $this->authorize('view', $projeto);

        $projeto->load(['receita.carreiras', 'observacoes']);

        $progressoPorCarreira = $projeto->progressos()
            ->get()
            ->keyBy('carreira_id');

        return view('projetos.show', [
            'projeto' => $projeto,
            'progresso' => $projeto->calcularProgresso(),
            'progressoPorCarreira' => $progressoPorCarreira,
        ]);
    }

    public function edit(Projeto $projeto)
    {
        $this->authorize('update', $projeto);

        $receitas = $projeto->usuario->receitas()->orderBy('titulo')->get();
        $temProgresso = $projeto->progressos()
            ->where(fn ($q) => $q->where('pontos_feitos', '>', 0)->orWhere('concluida', true))
            ->exists();

        return view('projetos.edit', [
            'projeto' => $projeto,
            'receitas' => $receitas,
            'temProgresso' => $temProgresso,
        ]);
    }

    public function update(ProjetoRequest $request, Projeto $projeto)
    {
        $this->authorize('update', $projeto);

        $temProgresso = $projeto->progressos()
            ->where(fn ($q) => $q->where('pontos_feitos', '>', 0)->orWhere('concluida', true))
            ->exists();

        $dados = ['titulo' => $request->validated('titulo')];

        if (! $temProgresso) {
            $dados['receita_id'] = $request->validated('receita_id');
        }

        $projeto->update($dados);

        return redirect()
            ->route('projetos.show', $projeto)
            ->with('sucesso', 'Projeto atualizado com sucesso.');
    }

    public function destroy(Projeto $projeto)
    {
        $this->authorize('delete', $projeto);

        $projeto->delete();

        return redirect()
            ->route('projetos.index')
            ->with('sucesso', 'Projeto excluído com sucesso.');
    }
}
