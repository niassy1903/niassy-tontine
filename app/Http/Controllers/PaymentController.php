<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Contribution;
use App\Models\Payment;
use App\Notifications\PaymentStatusNotification;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = auth()->user()->payments()->with(['tontine', 'contribution.period'])->latest()->paginate(15);
        return view('payments.index', compact('payments'));
    }

    public function store(StorePaymentRequest $request, Contribution $contribution, PaymentService $service): RedirectResponse
    {
        abort_unless($contribution->user_id === auth()->id() && $contribution->tontine->isMember(auth()->user()), 403);
        $service->submit($contribution, auth()->user(), $request->validated());
        return back()->with('success', 'Paiement envoyé. Il sera visible après validation par le trésorier.');
    }

    public function approve(Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('approve', $payment);
        $service->approve($payment, auth()->user());
        $payment->load('user', 'tontine');
        $payment->user->notify(new PaymentStatusNotification($payment, 'approved'));
        return back()->with('success', 'Paiement validé et cotisation recalculée.');
    }

    public function reject(Payment $payment, PaymentService $service): RedirectResponse
    {
        $this->authorize('reject', $payment);
        $service->reject($payment, auth()->user());
        $payment->load('user', 'tontine');
        $payment->user->notify(new PaymentStatusNotification($payment, 'rejected'));
        return back()->with('success', 'Paiement refusé.');
    }
}