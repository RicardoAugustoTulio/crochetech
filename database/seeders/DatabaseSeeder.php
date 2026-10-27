<?php

namespace Database\Seeders;

use App\Models\Carreira;
use App\Models\Projeto;
use App\Models\Receita;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Popula o banco com dados de demonstração realistas de crochê.
     */
    public function run(): void
    {
        $maria = User::create([
            'name' => 'Maria Artesã',
            'email' => 'demo@crochetech.com.br',
            'password' => Hash::make('croche2026'),
            'email_verified_at' => now(),
        ]);

        $bolinha = $this->criarBolinha($maria);
        $tapete = $this->criarTapete($maria);
        $portaCopos = $this->criarPortaCopos($maria);

        // Receita adaptada: tapete 1,5× maior.
        $tapete->adaptar(1.5, 'Tapete redondo de barbante (×1,5)');

        $this->projetoConcluido($maria, $bolinha);
        $this->projetoEmAndamento($maria, $tapete);
        $this->projetoNaoIniciado($maria, $portaCopos);

        // Segundo usuário, para verificação de isolamento entre contas.
        $outro = User::create([
            'name' => 'João Crocheteiro',
            'email' => 'outro@crochetech.com.br',
            'password' => Hash::make('croche2026'),
            'email_verified_at' => now(),
        ]);
        $this->criarPortaCopos($outro);
    }

    private function criarBolinha(User $user): Receita
    {
        $receita = $user->receitas()->create([
            'titulo' => 'Bolinha amigurumi',
            'descricao' => 'Bolinha básica em ponto baixo, ideal para começar no amigurumi.',
            'tipo' => Receita::TIPO_ORIGINAL,
        ]);

        $this->carreira($receita, 1, 'Anel mágico com 6 pontos baixos.', [['anel mágico', 1], ['ponto baixo', 6]]);
        $this->carreira($receita, 2, '6 aumentos, totalizando 12 pontos.', [['aumento', 6]]);
        $this->carreira($receita, 3, '(1 pb, 1 aum) repetido 6 vezes, totalizando 18 pontos.', [['ponto baixo', 1], ['aumento', 1]], 6);
        $this->carreira($receita, 4, '(2 pb, 1 aum) repetido 6 vezes, totalizando 24 pontos.', [['ponto baixo', 2], ['aumento', 1]], 6);
        $this->carreira($receita, 5, '24 pontos baixos.', [['ponto baixo', 24]]);
        $this->carreira($receita, 6, '(2 pb, 1 dim) repetido 6 vezes, totalizando 18 pontos.', [['ponto baixo', 2], ['diminuição', 1]], 6);
        $this->carreira($receita, 7, '(1 pb, 1 dim) repetido 6 vezes, totalizando 12 pontos. Comece a encher.', [['ponto baixo', 1], ['diminuição', 1]], 6);
        $this->carreira($receita, 8, '6 diminuições e feche, totalizando 6 pontos.', [['diminuição', 6]]);

        $receita->materiais()->createMany([
            ['nome' => 'Fio de algodão', 'quantidade' => 20, 'unidade' => 'g'],
            ['nome' => 'Enchimento acrílico', 'quantidade' => 10, 'unidade' => 'g'],
            ['nome' => 'Agulha de crochê 2,5 mm', 'quantidade' => 1, 'unidade' => 'unidade'],
        ]);

        return $receita;
    }

    private function criarTapete(User $user): Receita
    {
        $receita = $user->receitas()->create([
            'titulo' => 'Tapete redondo de barbante',
            'descricao' => 'Tapete circular em ponto alto, crescimento clássico de 12 pontos por carreira.',
            'tipo' => Receita::TIPO_ORIGINAL,
        ]);

        $this->carreira($receita, 1, 'Anel mágico com 12 pontos altos.', [['anel mágico', 1], ['ponto alto', 12]]);
        $this->carreira($receita, 2, '12 aumentos, totalizando 24 pontos.', [['aumento', 12]]);
        $this->carreira($receita, 3, '(1 pa, 1 aum) repetido 12 vezes, totalizando 36 pontos.', [['ponto alto', 1], ['aumento', 1]], 12);
        $this->carreira($receita, 4, '(2 pa, 1 aum) repetido 12 vezes, totalizando 48 pontos.', [['ponto alto', 2], ['aumento', 1]], 12);
        $this->carreira($receita, 5, '(3 pa, 1 aum) repetido 12 vezes, totalizando 60 pontos.', [['ponto alto', 3], ['aumento', 1]], 12);
        $this->carreira($receita, 6, '(4 pa, 1 aum) repetido 12 vezes, totalizando 72 pontos.', [['ponto alto', 4], ['aumento', 1]], 12);

        $receita->materiais()->createMany([
            ['nome' => 'Barbante nº 6', 'quantidade' => 1, 'unidade' => 'novelo'],
            ['nome' => 'Agulha de crochê 4 mm', 'quantidade' => 1, 'unidade' => 'unidade'],
        ]);

        return $receita;
    }

    private function criarPortaCopos(User $user): Receita
    {
        $receita = $user->receitas()->create([
            'titulo' => 'Porta-copos',
            'descricao' => 'Porta-copos redondo e rápido em ponto baixo.',
            'tipo' => Receita::TIPO_ORIGINAL,
        ]);

        $this->carreira($receita, 1, 'Anel mágico com 8 pontos baixos.', [['anel mágico', 1], ['ponto baixo', 8]]);
        $this->carreira($receita, 2, '8 aumentos, totalizando 16 pontos.', [['aumento', 8]]);
        $this->carreira($receita, 3, '(1 pb, 1 aum) repetido 8 vezes, totalizando 24 pontos.', [['ponto baixo', 1], ['aumento', 1]], 8);
        $this->carreira($receita, 4, '24 pontos baixos e arremate.', [['ponto baixo', 24]]);

        $receita->materiais()->createMany([
            ['nome' => 'Fio de algodão', 'quantidade' => 15, 'unidade' => 'g'],
            ['nome' => 'Agulha de crochê 3 mm', 'quantidade' => 1, 'unidade' => 'unidade'],
        ]);

        return $receita;
    }

    /**
     * Cria uma carreira com seus pontos e, opcionalmente, uma repetição da sequência.
     *
     * @param  array<int, array{0: string, 1: int}>  $pontos
     */
    private function carreira(Receita $receita, int $numero, string $descricao, array $pontos, int $repeticoes = 0): Carreira
    {
        $carreira = $receita->carreiras()->create([
            'numero' => $numero,
            'descricao' => $descricao,
        ]);

        foreach ($pontos as [$tipo, $quantidade]) {
            $carreira->pontos()->create([
                'tipo' => $tipo,
                'quantidade' => $quantidade,
            ]);
        }

        if ($repeticoes > 0) {
            $carreira->repeticoes()->create([
                'quantidade' => $repeticoes,
                'instrucao' => 'Repita a sequência de pontos acima.',
            ]);
        }

        return $carreira;
    }

    /**
     * Projeto 100% concluído, finalizado há 5 dias e criado há ~20 dias.
     */
    private function projetoConcluido(User $user, Receita $receita): void
    {
        $projeto = $user->projetos()->create([
            'receita_id' => $receita->id,
            'titulo' => 'Bolinha para a sobrinha',
        ]);

        foreach ($receita->carreiras as $carreira) {
            $progresso = $projeto->progressoDa($carreira);
            $progresso->registrarPontos($carreira->pontosEsperados());
            $progresso->marcarConcluida();
        }

        // Ajusta a data de conclusão para 5 dias atrás (atualizarStatus usou now()).
        $projeto->forceFill(['data_conclusao' => now()->subDays(5)])->save();
        $this->datarCriacao($projeto, now()->subDays(20));
    }

    /**
     * Projeto em andamento: 3 carreiras concluídas, pontos parciais na 4ª, 2 observações.
     */
    private function projetoEmAndamento(User $user, Receita $receita): void
    {
        $projeto = $user->projetos()->create([
            'receita_id' => $receita->id,
            'titulo' => 'Tapete da sala',
        ]);

        $carreiras = $receita->carreiras;

        foreach ($carreiras->take(3) as $carreira) {
            $progresso = $projeto->progressoDa($carreira);
            $progresso->registrarPontos($carreira->pontosEsperados());
            $progresso->marcarConcluida();
        }

        $quarta = $carreiras->get(3);
        if ($quarta) {
            $projeto->progressoDa($quarta)->registrarPontos((int) floor($quarta->pontosEsperados() / 2));
        }

        $projeto->registrarObservacao('Comecei usando barbante cru, ficou com boa firmeza.');
        $projeto->registrarObservacao('Na carreira 4 tive que contar os pontos duas vezes para não errar o aumento.');

        $this->datarCriacao($projeto, now()->subDays(8));
    }

    /**
     * Projeto não iniciado, criado recentemente.
     */
    private function projetoNaoIniciado(User $user, Receita $receita): void
    {
        $projeto = $user->projetos()->create([
            'receita_id' => $receita->id,
            'titulo' => 'Porta-copos kit 4',
        ]);

        $this->datarCriacao($projeto, now()->subDays(2));
    }

    /**
     * Define created_at/updated_at sem disparar o touch automático.
     */
    private function datarCriacao(Projeto $projeto, Carbon $quando): void
    {
        $projeto->timestamps = false;
        $projeto->forceFill(['created_at' => $quando, 'updated_at' => $quando])->save();
        $projeto->timestamps = true;
    }
}
