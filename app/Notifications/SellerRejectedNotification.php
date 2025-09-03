<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerRejectedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $sellerProfile;
    protected $rejectedBy;
    protected $rejectionReason;

    public function __construct(User $user, SellerProfile $sellerProfile, string $rejectionReason, User $rejectedBy = null)
    {
        $this->user = $user;
        $this->sellerProfile = $sellerProfile;
        $this->rejectionReason = $rejectionReason;
        $this->rejectedBy = $rejectedBy;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Solicitação de Loja Requer Correções')
                    ->greeting('Olá, ' . $this->user->name)
                    ->line('Analisamos sua solicitação para abertura de loja, mas precisamos de algumas correções.')
                    ->line('**Motivo da solicitação de correção:**')
                    ->line($this->rejectionReason)
                    ->line('Não se preocupe! Você pode corrigir as informações e reenviar.')
                    ->action('Corrigir Informações', url('/seller/onboarding'))
                    ->line('Nossa equipe está aqui para ajudar. Entre em contato se tiver dúvidas.')
                    ->line('Obrigado pela compreensão.');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'seller_profile_id' => $this->sellerProfile->id,
            'rejected_by' => $this->rejectedBy?->id,
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Solicitação de loja rejeitada'
        ];
    }
}