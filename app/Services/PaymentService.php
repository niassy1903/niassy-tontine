<?php

namespace App\Services;

use App\Models\Contribution;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function submit(Contribution $contribution, User $user, array $data): Payment
    {
        return DB::transaction(function () use ($contribution, $user, $data) {
            $remaining = $contribution->remaining_amount;
            abort_if($data['amount'] > $remaining && $remaining > 0, 422, 'Le montant dépasse le reste à payer.');

            return Payment::create([
                'tontine_id' => $contribution->tontine_id,
                'contribution_id' => $contribution->id,
                'user_id' => $user->id,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'paid_at' => $data['paid_at'],
                'comment' => $data['comment'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    public function approve(Payment $payment, User $validator): void
    {
        abort_if($payment->user_id === $validator->id, 403, 'Vous ne pouvez pas valider votre propre paiement.');

        DB::transaction(function () use ($payment, $validator) {
            $payment->update(['status' => 'approved', 'validated_by' => $validator->id, 'validated_at' => now()]);
            $paid = $payment->contribution->payments()->where('status', 'approved')->sum('amount');
            $expected = $payment->contribution->expected_amount;
            $payment->contribution->update(['status' => $paid >= $expected ? 'paid' : 'partial']);
        });
    }

    public function reject(Payment $payment, User $validator): void
    {
        abort_if($payment->user_id === $validator->id, 403, 'Vous ne pouvez pas refuser votre propre paiement.');
        $payment->update(['status' => 'rejected', 'validated_by' => $validator->id, 'validated_at' => now()]);
    }
}