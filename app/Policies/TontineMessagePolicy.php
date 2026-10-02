<?php

namespace App\Policies;

use App\Models\TontineMessage;
use App\Models\User;

class TontineMessagePolicy
{
    public function before(User $user): ?bool
    {
        return $user->isSuperAdmin() ? true : null;
    }

    public function delete(User $user, TontineMessage $message): bool
    {
        return $message->user_id === $user->id || $message->tontine->canManage($user);
    }
}