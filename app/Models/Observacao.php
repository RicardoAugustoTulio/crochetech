<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Observacao extends Model
{
    use HasFactory;

    protected $table = 'observacoes';

    protected $fillable = [
        'projeto_id',
        'texto',
    ];

    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }
}
