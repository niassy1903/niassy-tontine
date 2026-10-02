<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Payment $payment, public string $status) {}

    public function via(object $notifiable): array { return ['database', 'mail']; }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->status === 'approved' ? 'Paiement validé' : 'Paiement refusé',
            'message' => number_format($this->payment->amount, 0, ',', ' ').' FCFA · '.$this->payment->tontine->name,
            'payment_id' => $this->payment->id,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->status === 'approved' ? 'Votre paiement a été validé' : 'Votre paiement a été refusé')
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->toArray($notifiable)['message'])
            ->line($this->status === 'approved' ? 'Votre cotisation a été mise à jour.' : 'Vous pouvez contacter le responsable de la tontine pour plus de détails.');
    }
}