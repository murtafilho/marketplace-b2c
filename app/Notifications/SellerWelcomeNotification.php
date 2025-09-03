<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerWelcomeNotification extends Notification
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Bem-vindo ao Marketplace - Cadastro de Vendedor Recebido')
                    ->greeting('Olá, ' . $this->user->name . '!')
                    ->line('Recebemos seu cadastro como vendedor em nosso marketplace.')
                    ->line('Agora você pode completar o processo de configuração da sua loja.')
                    ->action('Configurar Minha Loja', url('/seller/onboarding'))
                    ->line('Complete todas as informações solicitadas para que possamos aprovar sua loja rapidamente.')
                    ->line('Obrigado por escolher nosso marketplace!');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'message' => 'Cadastro de vendedor recebido'
        ];
    }
}