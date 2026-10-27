<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Receita extends Model
{
    use HasFactory;

    public const TIPO_ORIGINAL = 'original';

    public const TIPO_ADAPTADA = 'adaptada';

    protected $fillable = [
        'user_id',
        'receita_original_id',
        'titulo',
        'descricao',
        'tipo',
        'fator_adaptacao',
    ];

    protected function casts(): array
    {
        return [
            'fator_adaptacao' => 'decimal:2',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function original(): BelongsTo
    {
        return $this->belongsTo(Receita::class, 'receita_original_id');
    }

    public function adaptacoes(): HasMany
    {
        return $this->hasMany(Receita::class, 'receita_original_id');
    }

    public function carreiras(): HasMany
    {
        return $this->hasMany(Carreira::class)->orderBy('numero');
    }

    public function materiais(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function projetos(): HasMany
    {
        return $this->hasMany(Projeto::class);
    }

    public function isAdaptada(): bool
    {
        return $this->tipo === self::TIPO_ADAPTADA;
    }

    public function proximoNumeroCarreira(): int
    {
        return (int) $this->carreiras()->max('numero') + 1;
    }

    public function adaptar(float $fator, ?string $titulo = null): Receita
    {
        return DB::transaction(function () use ($fator, $titulo) {
            $this->load('carreiras.pontos', 'carreiras.repeticoes', 'materiais');

            $adaptada = self::create([
                'user_id' => $this->user_id,
                'receita_original_id' => $this->id,
                'titulo' => $titulo ?: $this->titulo.' (×'.rtrim(rtrim(number_format($fator, 2, ',', ''), '0'), ',').')',
                'descricao' => $this->descricao,
                'tipo' => self::TIPO_ADAPTADA,
                'fator_adaptacao' => $fator,
            ]);

            foreach ($this->carreiras as $carreira) {
                $novaCarreira = $adaptada->carreiras()->create([
                    'numero' => $carreira->numero,
                    'descricao' => $carreira->descricao,
                ]);

                // Se a carreira tem repetição, a sequência de pontos é a unidade que se
                // repete: escala só a repetição, senão o fator seria aplicado duas vezes.
                $escalarPontos = $carreira->repeticoes->isEmpty();

                foreach ($carreira->pontos as $ponto) {
                    $novaCarreira->pontos()->create([
                        'tipo' => $ponto->tipo,
                        'quantidade' => $escalarPontos ? $ponto->adaptar($fator) : $ponto->quantidade,
                        'instrucao' => $ponto->instrucao,
                    ]);
                }

                foreach ($carreira->repeticoes as $repeticao) {
                    $novaCarreira->repeticoes()->create([
                        'quantidade' => $repeticao->adaptar($fator),
                        'instrucao' => $repeticao->instrucao,
                    ]);
                }
            }

            foreach ($this->materiais as $material) {
                $adaptada->materiais()->create([
                    'nome' => $material->nome,
                    'quantidade' => $material->calcularProporcional($fator),
                    'unidade' => $material->unidade,
                ]);
            }

            return $adaptada;
        });
    }

    public function calcularMateriais(): Collection
    {
        return $this->materiais;
    }

    public function totalPontosEsperados(): int
    {
        return (int) $this->carreiras->sum(fn (Carreira $c) => $c->pontosEsperados());
    }
}
