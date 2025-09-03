<?php
/**
 * Arquivo: app/Mail/NewMessageNotification.php
 * Descrição: Email de notificação de nova mensagem
 * Laravel Version: 12.x
 * Criado em: 03/01/2025
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\Conversation;

class NewMessageNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $conversation;
    public $sender;
    public $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message, $recipient)
    {
        $this->message = $message;
        $this->conversation = $message->conversation;
        $this->sender = $message->sender;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nova mensagem de ' . $this->sender->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-message',
            with: [
                'messageContent' => $this->message->content,
                'senderName' => $this->sender->name,
                'conversationUrl' => route('conversations.show', $this->conversation),
                'productName' => $this->conversation->product?->name,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
