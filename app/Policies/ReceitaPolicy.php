<?php

namespace App\Policies;

use App\Models\Receita;
use App\Models\User;

class ReceitaPolicy
{
    public function view(User $user, Receita $receita): bool
    {
        return $receita->user_id === $user->id;
    }

    public function update(User $user, Receita $receita): bool
    {
        return $receita->user_id === $user->id;
    }

    public function delete(User $user, Receita $receita): bool
    {
        return $receita->user_id === $user->id;
    }

    public function adaptar(User $user, Receita $receita): bool
    {
        return $receita->user_id === $user->id && ! $receita->isAdaptada();
    }

    public function editarEstrutura(User $user, Receita $receita): bool
    {
        return $receita->user_id === $user->id && ! $receita->isAdaptada();
    }
}
