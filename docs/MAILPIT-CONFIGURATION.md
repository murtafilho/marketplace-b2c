# 📧 Configuração do Mailpit no Marketplace B2C

**Data**: 03/01/2025  
**Versão**: 1.0

## 🎯 O que é o Mailpit?

O Mailpit é um servidor SMTP de desenvolvimento que captura todos os emails enviados pela aplicação, permitindo visualizá-los em uma interface web sem enviá-los de verdade. É o substituto moderno do MailHog no Laragon.

## ✅ Configuração Implementada

### 1. **Arquivo .env**
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@marketplace-b2c.test"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. **Classes de Email Criadas**
- `app/Mail/NewMessageNotification.php` - Notificação de nova mensagem
- `app/Mail/DeliveryAgreementProposed.php` - Proposta de entrega

### 3. **Templates de Email**
- `resources/views/emails/new-message.blade.php`
- `resources/views/emails/delivery-agreement-proposed.blade.php`

### 4. **Comando de Teste**
- `php artisan mailpit:test` - Testa o envio de email

## 🚀 Como Usar

### 1. **Iniciar o Mailpit no Laragon**

1. Abra o Laragon
2. Clique com botão direito no ícone do Laragon
3. Vá em **Mail > Mailpit > Start**
4. O Mailpit iniciará nas portas:
   - **1025** - SMTP (para receber emails)
   - **8025** - Interface Web (para visualizar)

### 2. **Acessar a Interface Web**

Abra o navegador e acesse:
```
http://localhost:8025
```

### 3. **Testar o Envio**

Execute o comando de teste:
```bash
php artisan mailpit:test seu-email@teste.com
```

### 4. **Executar Script de Configuração**

```bash
cd C:\laragon\www\marketplace-b2c
configurar-mailpit.bat
```

## 📨 Emails Automáticos Configurados

### 1. **Nova Mensagem no Chat**
- **Quando**: Alguém envia uma mensagem
- **Para**: O outro participante da conversa
- **Template**: `new-message.blade.php`

### 2. **Proposta de Entrega**
- **Quando**: Alguém cria uma proposta de entrega
- **Para**: O outro participante
- **Template**: `delivery-agreement-proposed.blade.php`

## 🔧 Troubleshooting

### Problema: Emails não aparecem no Mailpit

**Soluções:**
1. Verifique se o Mailpit está rodando:
   ```bash
   netstat -an | findstr :1025
   ```

2. Limpe o cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. Verifique o arquivo .env

### Problema: Erro ao enviar email

**Soluções:**
1. Verifique os logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. Teste a conexão:
   ```bash
   telnet 127.0.0.1 1025
   ```

3. Reinicie o Mailpit no Laragon

## 📝 Exemplos de Uso

### Enviar Email Programaticamente

```php
use App\Mail\NewMessageNotification;
use Illuminate\Support\Facades\Mail;

// Enviar notificação de nova mensagem
Mail::to($user->email)->send(new NewMessageNotification($message, $user));
```

### Criar Novo Template de Email

1. Criar a classe Mailable:
```bash
php artisan make:mail NomeDaClasse
```

2. Criar o template em `resources/views/emails/`

3. Usar no código:
```php
Mail::to($email)->send(new NomeDaClasse($dados));
```

## 🎨 Personalização dos Templates

Os templates de email estão em:
```
resources/views/emails/
├── new-message.blade.php
└── delivery-agreement-proposed.blade.php
```

Podem ser editados livremente com HTML/CSS inline.

## 🔒 Segurança

- **Desenvolvimento**: Todos os emails são capturados pelo Mailpit
- **Produção**: Configurar um serviço real (SendGrid, Mailgun, etc.)
- **Nunca commitar** credenciais reais no .env

## 📊 Benefícios do Mailpit

1. **Desenvolvimento Seguro**: Nenhum email real é enviado
2. **Debug Fácil**: Visualize todos os emails em uma interface
3. **Histórico**: Mantém histórico de todos os emails
4. **API REST**: Pode ser acessado programaticamente
5. **Leve**: Consome poucos recursos

## 🌐 Interface do Mailpit

### Funcionalidades da Interface Web:

1. **Lista de Emails**: Todos os emails capturados
2. **Visualização**: HTML, texto plano e código fonte
3. **Pesquisa**: Busca por assunto, remetente, etc.
4. **Download**: Baixar emails em formato .eml
5. **Limpar**: Apagar todos os emails
6. **API**: Endpoints REST para automação

## 🚦 Status da Implementação

- ✅ Mailpit configurado no .env
- ✅ Templates de email criados
- ✅ Classes Mailable implementadas
- ✅ Comando de teste criado
- ✅ Integração com sistema de mensagens
- ✅ Scripts de configuração
- ✅ Documentação completa

## 📞 Suporte

Para problemas com o Mailpit:
1. Verifique a documentação do Laragon
2. Consulte: https://github.com/axllent/mailpit
3. Logs do Laravel: `storage/logs/laravel.log`
