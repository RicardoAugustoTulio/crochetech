<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Progresso extends Model
{
    use HasFactory;

    protected $table = 'progressos';

    protected $fillable = [
        'projeto_id',
        'carreira_id',
        'pontos_feitos',
        'concluida',
        'concluida_em',
    ];

    protected function casts(): array
    {
        return [
            'pontos_feitos' => 'integer',
            'concluida' => 'boolean',
            'concluida_em' => 'datetime',
        ];
    }

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function carreira(): BelongsTo
    {
        return $this->belongsTo(Carreira::class);
    }

    public function marcarConcluida(): void
    {
        if ($this->concluida) {
            $this->concluida = false;
            $this->concluida_em = null;
        } else {
            $this->concluida = true;
            $this->concluida_em = now();
        }

        $this->save();
        $this->projeto->atualizarStatus();
    }

    public function registrarPontos(int $delta): void
    {
        $esperados = $this->carreira->pontosEsperados();
        $this->pontos_feitos = max(0, min($esperados, $this->pontos_feitos + $delta));
        $this->save();
        $this->projeto->atualizarStatus();
    }
}
