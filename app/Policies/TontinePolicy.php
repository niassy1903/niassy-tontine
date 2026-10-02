<?php

namespace App\Policies;

use App\Models\Tontine;
use App\Models\User;

class TontinePolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function view(User $user, Tontine $tontine): bool
    {
        return $tontine->visibility === 'public' || $tontine->isMember($user);
    }

    public function manage(User $user, Tontine $tontine): bool
    {
        return $tontine->canManage($user);
    }

    public function createMessage(User $user, Tontine $tontine): bool
    {
        return $tontine->isMember($user);
    }
}