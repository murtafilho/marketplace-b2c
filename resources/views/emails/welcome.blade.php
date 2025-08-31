@component('mail::message')
# Bem-vindo ao {{ config('app.name') }}!

Olá **{{ $user->name }}**,

É com grande prazer que damos as boas-vindas ao nosso marketplace! 🎉

Agora você faz parte de uma comunidade de compradores e vendedores que valorizam qualidade, confiança e excelência.

## O que você pode fazer:

@component('mail::panel')
### 🛍️ **Como Comprador**
- Explore milhares de produtos únicos
- Compre diretamente de vendedores locais
- Pagamento seguro com Mercado Pago
- Avalie produtos e vendedores
@endcomponent

@if($user->role === 'customer')
@component('mail::panel')
### 🏪 **Quer vender também?**
Você pode criar sua loja a qualquer momento e começar a vender seus produtos!

@component('mail::button', ['url' => route('seller.register')])
Criar Minha Loja
@endcomponent
@endcomponent
@endif

@component('mail::button', ['url' => route('home')])
Explorar o Marketplace
@endcomponent

## Precisa de ajuda?

Nossa equipe está aqui para te ajudar! Entre em contato conosco:

- 📧 Email: {{ config('mail.from.address') }}
- 📱 WhatsApp: (11) 99999-9999
- 💬 Chat online no site

---

Obrigado por escolher o {{ config('app.name') }}!

Atenciosamente,<br>
Equipe {{ config('app.name') }}

@component('mail::subcopy')
Se você não se cadastrou em nossa plataforma, pode ignorar este email com segurança.
@endcomponent
@endcomponent