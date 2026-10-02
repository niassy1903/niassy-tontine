<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function approve(User $user, Payment $payment): bool
    {
        return $payment->status === 'pending'
            && $payment->user_id !== $user->id
            && $payment->tontine->canManage($user);
    }

    public function reject(User $user, Payment $payment): bool
    {
        return $payment->status === 'pending'
            && $payment->user_id !== $user->id
            && $payment->tontine->canManage($user);
    }
}