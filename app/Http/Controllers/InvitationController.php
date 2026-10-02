<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvitationRequest;
use App\Models\Invitation;
use App\Models\Tontine;
use App\Models\User;
use App\Notifications\TontineInvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function store(StoreInvitationRequest $request, Tontine $tontine): RedirectResponse
    {
        $this->authorize('manage', $tontine);
        $email = mb_strtolower($request->string('email')->toString());
        abort_if($tontine->members()->where('email', $email)->exists(), 422, 'Cette personne est déjà membre de la tontine.');

        $invitation = Invitation::updateOrCreate(
            ['tontine_id' => $tontine->id, 'email' => $email, 'accepted_at' => null],
            ['invited_by' => auth()->id(), 'role' => $request->string('role')->toString(), 'declined_at' => null, 'expires_at' => now()->addDays(7), 'token' => \Illuminate\Support\Str::random(64)],
        );

        $notifiable = User::where('email', $email)->first();
        if ($notifiable) {
            $notifiable->notify(new TontineInvitationNotification($invitation));
        } else {
            Notification::route('mail', $email)->notify(new TontineInvitationNotification($invitation));
        }

        return back()->with('success', 'Invitation envoyée à '.$email.'.');
    }

    public function show(string $token): View
    {
        $invitation = Invitation::where('token', $token)->with(['tontine', 'inviter'])->firstOrFail();
        return view('invitations.show', compact('invitation'));
    }

    public function accept(string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->with('tontine')->firstOrFail();
        abort_unless($invitation->isPending(), 410, 'Cette invitation n’est plus valide.');
        abort_unless(auth()->user()->email === $invitation->email, 403, 'Connectez-vous avec l’adresse email invitée.');

        $invitation->tontine->members()->syncWithoutDetaching([
            auth()->id() => ['role' => $invitation->role, 'status' => 'active', 'joined_at' => now()],
        ]);
        $invitation->update(['accepted_at' => now()]);

        return redirect()->route('tontines.show', $invitation->tontine)->with('success', 'Vous avez rejoint la tontine.');
    }

    public function decline(string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();
        abort_unless($invitation->isPending(), 410, 'Cette invitation n’est plus valide.');
        $invitation->update(['declined_at' => now()]);
        return redirect()->route('home')->with('success', 'Invitation refusée.');
    }
}