<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repeticao extends Model
{
    use HasFactory;

    protected $table = 'repeticoes';

    protected $fillable = [
        'carreira_id',
        'quantidade',
        'instrucao',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'integer',
        ];
    }

    public function carreira(): BelongsTo
    {
        return $this->belongsTo(Carreira::class);
    }

    public function adaptar(float $fator): int
    {
        return max(1, (int) round($this->quantidade * $fator));
    }
}
