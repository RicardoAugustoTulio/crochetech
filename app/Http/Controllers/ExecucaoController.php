<?php

namespace App\Http\Controllers;

use App\Models\Carreira;
use App\Models\Projeto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExecucaoController extends Controller
{
    public function show(Request $request, Projeto $projeto): View
    {
        $this->authorize('view', $projeto);

        $carreiras = $projeto->receita->carreiras()
            ->with(['pontos', 'repeticoes'])
            ->orderBy('numero')
            ->get();

        // EXE-07: receita sem carreiras.
        if ($carreiras->isEmpty()) {
            return view('projetos.executar', [
                'projeto' => $projeto,
                'carreiras' => $carreiras,
                'carreira' => null,
                'progresso' => null,
                'posicao' => 0,
                'total' => 0,
                'anterior' => null,
                'proxima' => null,
            ]);
        }

        $total = $carreiras->count();

        // Carreira indicada em ?carreira={numero}; senão, a primeira não concluída; senão, a primeira.
        $carreira = null;

        if ($request->filled('carreira')) {
            $carreira = $carreiras->firstWhere('numero', (int) $request->query('carreira'));
            abort_if($carreira === null, 404); // carreira não pertence à receita do projeto
        }

        if ($carreira === null) {
            $concluidasIds = $projeto->progressos()
                ->where('concluida', true)
                ->pluck('carreira_id')
                ->all();

            $carreira = $carreiras->first(fn (Carreira $c) => ! in_array($c->id, $concluidasIds, true))
                ?? $carreiras->first();
        }

        $posicao = $carreiras->search(fn (Carreira $c) => $c->id === $carreira->id) + 1;

        return view('projetos.executar', [
            'projeto' => $projeto,
            'carreiras' => $carreiras,
            'carreira' => $carreira,
            'progresso' => $projeto->progressoDa($carreira),
            'posicao' => $posicao,
            'total' => $total,
            'anterior' => $carreiras->get($posicao - 2),
            'proxima' => $carreiras->get($posicao),
        ]);
    }

    public function pontos(Request $request, Projeto $projeto, Carreira $carreira): RedirectResponse
    {
        $this->authorize('update', $projeto);
        $this->garantirCarreiraDoProjeto($projeto, $carreira);

        $delta = (int) $request->input('delta');
        abort_unless(in_array($delta, [1, -1], true), 422);

        $projeto->progressoDa($carreira)->registrarPontos($delta);

        return $this->voltarParaCarreira($projeto, $carreira);
    }

    public function concluir(Request $request, Projeto $projeto, Carreira $carreira): RedirectResponse
    {
        $this->authorize('update', $projeto);
        $this->garantirCarreiraDoProjeto($projeto, $carreira);

        $progresso = $projeto->progressoDa($carreira);
        $progresso->marcarConcluida();

        // Ao concluir, avança para a próxima carreira não concluída, se houver.
        $alvo = $carreira;

        if ($progresso->concluida) {
            $proxima = $projeto->receita->carreiras()
                ->where('numero', '>', $carreira->numero)
                ->orderBy('numero')
                ->first();

            $alvo = $proxima ?? $carreira;
        }

        return $this->voltarParaCarreira($projeto, $alvo);
    }

    private function garantirCarreiraDoProjeto(Projeto $projeto, Carreira $carreira): void
    {
        abort_if($carreira->receita_id !== $projeto->receita_id, 404);
    }

    private function voltarParaCarreira(Projeto $projeto, Carreira $carreira): RedirectResponse
    {
        return redirect()->route('projetos.executar', [
            'projeto' => $projeto,
            'carreira' => $carreira->numero,
        ]);
    }
}
