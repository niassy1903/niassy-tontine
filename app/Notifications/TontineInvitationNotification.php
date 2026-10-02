<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TontineInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invitation $invitation) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation à rejoindre '.$this->invitation->tontine->name)
            ->greeting('Bonjour,')
            ->line($this->invitation->inviter->name.' vous invite à rejoindre la tontine '.$this->invitation->tontine->name.'.')
            ->action('Voir l’invitation', route('invitations.show', $this->invitation->token))
            ->line('Cette invitation est valable pendant 7 jours.');
    }
}