<?php

namespace App\Policies;

use App\Models\Projeto;
use App\Models\User;

class ProjetoPolicy
{
    public function view(User $user, Projeto $projeto): bool
    {
        return $projeto->user_id === $user->id;
    }

    public function update(User $user, Projeto $projeto): bool
    {
        return $projeto->user_id === $user->id;
    }

    public function delete(User $user, Projeto $projeto): bool
    {
        return $projeto->user_id === $user->id;
    }
}
