<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\SellerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SellerOnboardingSubmittedNotification extends Notification
{
    use Queueable;

    protected $user;
    protected $sellerProfile;

    public function __construct(User $user, SellerProfile $sellerProfile)
    {
        $this->user = $user;
        $this->sellerProfile = $sellerProfile;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Dados da Loja Recebidos - Aprovação em até 24 horas')
                    ->greeting('Ótimo, ' . $this->user->name . '!')
                    ->line('Recebemos todas as informações da sua loja e documentos.')
                    ->line('Nossa equipe irá analisar seus dados e sua loja será aprovada em até 24 horas.')
                    ->line('Enquanto isso, você já pode:')
                    ->line('• Cadastrar seus produtos')
                    ->line('• Configurar formas de pagamento')
                    ->line('• Personalizar sua loja')
                    ->action('Acessar Painel de Vendedor', url('/seller/dashboard'))
                    ->line('Você receberá um email assim que sua loja for aprovada.')
                    ->line('Obrigado pela paciência!');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'seller_profile_id' => $this->sellerProfile->id,
            'message' => 'Onboarding de vendedor submetido para aprovação'
        ];
    }
}