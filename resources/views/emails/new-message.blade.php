{{-- 
    Arquivo: resources/views/emails/new-message.blade.php
    Descrição: Template de email para nova mensagem
    Laravel Version: 12.x
    Criado em: 03/01/2025
--}}

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Mensagem</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin: -30px -30px 30px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .message-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .button:hover {
            opacity: 0.9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .product-info {
            background-color: #e8f4fd;
            padding: 10px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📨 Nova Mensagem Recebida!</h1>
        </div>
        
        <p>Olá!</p>
        
        <p>Você recebeu uma nova mensagem de <strong>{{ $senderName }}</strong>.</p>
        
        @if($productName)
        <div class="product-info">
            <strong>Produto relacionado:</strong> {{ $productName }}
        </div>
        @endif
        
        <div class="message-box">
            <strong>Mensagem:</strong><br>
            {{ $messageContent }}
        </div>
        
        <div style="text-align: center;">
            <a href="{{ $conversationUrl }}" class="button">
                Ver Conversa Completa
            </a>
        </div>
        
        <p>Responda o quanto antes para manter uma boa comunicação com {{ $senderName }}.</p>
        
        <div class="footer">
            <p>Este é um email automático do {{ config('app.name') }}.</p>
            <p>Por favor, não responda diretamente a este email.</p>
            <p>
                <a href="{{ config('app.url') }}" style="color: #667eea; text-decoration: none;">
                    Visitar o Marketplace
                </a>
            </p>
        </div>
    </div>
</body>
</html>
