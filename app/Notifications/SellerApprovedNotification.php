<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerApprovedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $sellerProfile;
    protected $approvedBy;

    public function __construct(User $user, SellerProfile $sellerProfile, User $approvedBy = null)
    {
        $this->user = $user;
        $this->sellerProfile = $sellerProfile;
        $this->approvedBy = $approvedBy;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('🎉 Parabéns! Sua loja foi aprovada!')
                    ->greeting('Parabéns, ' . $this->user->name . '!')
                    ->line('Sua loja foi aprovada e já está ativa no marketplace!')
                    ->line('Agora você pode:')
                    ->line('✅ Vender seus produtos')
                    ->line('✅ Receber pedidos de clientes')
                    ->line('✅ Acompanhar suas vendas')
                    ->line('✅ Gerenciar seus produtos')
                    ->action('Acessar Minha Loja', url('/seller/dashboard'))
                    ->line('Desejamos muito sucesso nas suas vendas!')
                    ->line('Equipe do Marketplace');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'seller_profile_id' => $this->sellerProfile->id,
            'approved_by' => $this->approvedBy?->id,
            'message' => 'Loja de vendedor aprovada'
        ];
    }
}