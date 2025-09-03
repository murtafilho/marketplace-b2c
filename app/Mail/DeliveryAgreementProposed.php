<?php
/**
 * Arquivo: app/Mail/DeliveryAgreementProposed.php
 * Descrição: Email de notificação de proposta de entrega
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\DeliveryAgreement;

class DeliveryAgreementProposed extends Mailable
{
    use Queueable, SerializesModels;

    public $agreement;
    public $conversation;
    public $proposer;

    /**
     * Create a new message instance.
     */
    public function __construct(DeliveryAgreement $agreement)
    {
        $this->agreement = $agreement;
        $this->conversation = $agreement->conversation;
        $this->proposer = $agreement->proposer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '📦 Nova Proposta de Entrega - ' . $this->agreement->getTypeLabel(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.delivery-agreement-proposed',
            with: [
                'agreementType' => $this->agreement->getTypeLabel(),
                'description' => $this->agreement->description,
                'deliveryFee' => $this->agreement->delivery_fee,
                'estimatedDate' => $this->agreement->estimated_date,
                'estimatedTime' => $this->agreement->estimated_time,
                'proposerName' => $this->proposer->name,
                'conversationUrl' => route('conversations.show', $this->conversation),
            ]
        );
    }
}
