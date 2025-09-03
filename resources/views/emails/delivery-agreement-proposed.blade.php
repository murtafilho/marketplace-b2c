{{-- 
    Arquivo: resources/views/emails/delivery-agreement-proposed.blade.php
    Descrição: Template de email para proposta de entrega
    Laravel Version: 12.x
    Criado em: 03/01/2025
--}}

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposta de Entrega</title>
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
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        .proposal-box {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">📦</div>
            <h1>Nova Proposta de Entrega!</h1>
        </div>
        
        <p>Olá!</p>
        
        <p><strong>{{ $proposerName }}</strong> enviou uma proposta de entrega para você.</p>
        
        <div class="proposal-box">
            <h3 style="margin-top: 0; color: #f5576c;">Detalhes da Proposta:</h3>
            
            <div class="detail-row">
                <span class="detail-label">Tipo de Entrega:</span>
                <span class="detail-value">{{ $agreementType }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Descrição:</span>
                <span class="detail-value">{{ $description }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Taxa de Entrega:</span>
                <span class="detail-value">R$ {{ number_format($deliveryFee, 2, ',', '.') }}</span>
            </div>
            
            @if($estimatedDate)
            <div class="detail-row">
                <span class="detail-label">Data Prevista:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($estimatedDate)->format('d/m/Y') }}</span>
            </div>
            @endif
            
            @if($estimatedTime)
            <div class="detail-row">
                <span class="detail-label">Horário:</span>
                <span class="detail-value">{{ $estimatedTime }}</span>
            </div>
            @endif
        </div>
        
        <p>Para aceitar ou recusar esta proposta, acesse a conversa:</p>
        
        <div style="text-align: center;">
            <a href="{{ $conversationUrl }}" class="button">
                Ver Proposta Completa
            </a>
        </div>
        
        <p><strong>Importante:</strong> Responda o quanto antes para confirmar os detalhes da entrega.</p>
        
        <div class="footer">
            <p>Este é um email automático do {{ config('app.name') }}.</p>
            <p>Por favor, não responda diretamente a este email.</p>
            <p>
                <a href="{{ config('app.url') }}" style="color: #f5576c; text-decoration: none;">
                    Visitar o Marketplace
                </a>
            </p>
        </div>
    </div>
</body>
</html>
