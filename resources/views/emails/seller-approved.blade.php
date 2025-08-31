@component('mail::message')
# 🎉 Parabéns! Sua loja foi aprovada!

Olá **{{ $seller->name }}**,

Temos o prazer de informar que sua solicitação para se tornar vendedor no {{ config('app.name') }} foi **APROVADA**! 

@component('mail::panel')
## 🏪 Dados da sua loja:
**Nome da Empresa:** {{ $seller->sellerProfile->company_name }}  
**Documento:** {{ $seller->sellerProfile->document_number }}  
**Status:** ✅ **ATIVA**
@endcomponent

## Próximos passos:

@component('mail::table')
| Etapa | Ação | Status |
|:------|:-----|:-------|
| 1 | ✅ Cadastro enviado | Concluído |
| 2 | ✅ Análise da documentação | Concluído |
| 3 | ✅ Aprovação da loja | Concluído |
| 4 | 📦 Cadastrar produtos | Fazer agora |
| 5 | 💰 Começar a vender | Fazer agora |
@endcomponent

@component('mail::button', ['url' => route('seller.dashboard')])
Acessar Painel da Loja
@endcomponent

## 🚀 Como começar a vender:

1. **Acesse seu painel de vendedor**
2. **Cadastre seus produtos** com fotos e descrições detalhadas
3. **Configure preços** competitivos
4. **Publique seus produtos** para começar a vender
5. **Gerencie pedidos** e mantenha contato com clientes

@component('mail::panel')
### 💡 Dicas para o sucesso:
- **Fotos de qualidade** vendem mais
- **Descrições detalhadas** geram confiança
- **Preços justos** atraem compradores
- **Atendimento rápido** fideliza clientes
@endcomponent

## 📊 Informações importantes:

- **Taxa do marketplace:** {{ config('mercadopago.marketplace_commission', 10) }}% sobre cada venda
- **Pagamentos:** Receba automaticamente via Mercado Pago
- **Suporte:** Estamos aqui para te ajudar sempre!

@component('mail::button', ['url' => route('seller.products.create')])
Cadastrar Primeiro Produto
@endcomponent

---

**Bem-vindo à família {{ config('app.name') }}!** 🚀

Estamos ansiosos para ver suas vendas decolar!

Atenciosamente,  
Equipe {{ config('app.name') }}

@component('mail::subcopy')
Precisa de ajuda? Entre em contato conosco:  
📧 Email: {{ config('mail.from.address') }}  
📱 WhatsApp: (11) 99999-9999
@endcomponent
@endcomponent