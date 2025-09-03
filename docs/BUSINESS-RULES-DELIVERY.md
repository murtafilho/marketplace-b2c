# 📦 Regras de Negócio - Sistema de Entrega e Comunicação

**Data de Criação**: 03/01/2025  
**Última Atualização**: 03/01/2025  
**Versão**: 1.0

## 🎯 Visão Geral

Este documento descreve as regras de negócio implementadas para o sistema de comunicação entre vendedores e compradores, e o gerenciamento de entregas do marketplace B2C.

---

## 💬 Sistema de Comunicação

### Objetivo
Permitir que compradores e vendedores se comuniquem diretamente para:
- Esclarecer dúvidas sobre produtos
- Negociar formas de entrega
- Combinar locais e horários de entrega/retirada
- Resolver questões pós-venda

### Estrutura

#### 1. **Conversas (conversations)**
- Cada conversa é única entre um comprador e um vendedor
- Pode estar relacionada a um produto específico ou pedido
- Mantém contadores de mensagens não lidas para cada participante
- Estados: `active`, `archived`, `blocked`
- Prioridades para vendedores: `low`, `normal`, `high`

#### 2. **Mensagens (messages)**
- Tipos de mensagem:
  - `text`: Mensagem de texto simples
  - `image`: Imagem anexada
  - `document`: Documento anexado
  - `delivery_proposal`: Proposta de entrega
  - `system`: Mensagem automática do sistema
- Suporta edição de mensagens
- Rastreamento de leitura com timestamp
- Status: `sent`, `delivered`, `read`, `deleted`

#### 3. **Acordos de Entrega (delivery_agreements)**
- Tipos de entrega disponíveis:
  - `pickup`: Retirada no local do vendedor
  - `meet_location`: Encontro em local combinado
  - `custom_delivery`: Entrega personalizada pelo vendedor
  - `correios`: **[FUTURO]** Integração com Correios
  - `transportadora`: **[FUTURO]** Integração com transportadoras

### Fluxo de Comunicação

1. **Iniciação da Conversa**
   - Comprador pode iniciar conversa a partir de:
     - Página do produto
     - Página do pedido
     - Perfil do vendedor
   - Sistema cria conversa única se não existir

2. **Troca de Mensagens**
   - Mensagens em tempo real (implementar WebSocket/Pusher posteriormente)
   - Notificações para mensagens não lidas
   - Histórico completo mantido

3. **Proposta de Entrega**
   - Vendedor ou comprador pode propor forma de entrega
   - Detalhes incluem:
     - Tipo de entrega
     - Taxa de entrega (pode ser zero)
     - Data e horário estimados
     - Local (se aplicável)
   - Estados da proposta:
     - `proposed`: Aguardando resposta
     - `negotiating`: Em negociação
     - `accepted`: Aceita por ambas as partes
     - `rejected`: Rejeitada
     - `completed`: Entrega realizada
     - `cancelled`: Cancelada

---

## 💰 Regras de Comissionamento

### Taxa de Comissão
- **Taxa padrão**: 10% sobre o valor dos produtos
- **Configuração**: Administrador pode alterar individualmente por vendedor
- **Aplicação**: Automática no momento do split de pagamento

### Split de Pagamento
- **Processo**: Totalmente automático via Mercado Pago
- **Momento**: Ocorre imediatamente após aprovação do pagamento
- **Divisão**:
  - Vendedor recebe: Valor do produto - comissão + taxa de entrega acordada
  - Marketplace recebe: Comissão sobre produtos
  - Taxa de entrega: 100% para o vendedor (sem comissão)

### Configurações por Vendedor
- Cada vendedor pode ter taxa de comissão personalizada
- Administrador define no momento da aprovação ou posteriormente
- Histórico de alterações deve ser mantido para auditoria

---

## 📊 Volumes e Performance

### Capacidade Inicial
- **Meta**: 500 vendas/mês
- **Estimativa**: ~17 vendas/dia
- **Picos esperados**: Fins de semana e datas comemorativas

### Otimizações Necessárias
Para suportar o volume esperado:
1. **Cache**: Implementar cache para produtos mais visualizados
2. **Índices**: Otimizar índices do banco para queries frequentes
3. **CDN**: Usar CDN para imagens de produtos
4. **Queue**: Processar notificações em background

---

## 🚚 Integração com Correios [FUTURO]

### Planejamento
- **Fase 1**: Sistema manual de combinação de entrega (ATUAL)
- **Fase 2**: Integração com API dos Correios
- **Fase 3**: Múltiplas transportadoras

### Requisitos para Integração Correios
1. **Contrato**: Necessário contrato com Correios para usar API
2. **Cálculo de Frete**: 
   - CEP origem (vendedor)
   - CEP destino (comprador)
   - Peso e dimensões do produto
3. **Rastreamento**: Código de rastreio automático
4. **Etiquetas**: Geração de etiquetas para envio

### Implementação Futura
```php
// Estrutura já preparada na tabela delivery_agreements
'type' => 'correios',
'details' => [
    'servico' => 'PAC/SEDEX',
    'codigo_rastreio' => 'BR123456789BR',
    'prazo_dias' => 5,
    'valor_frete' => 25.90
]
```

---

## 📈 Métricas e Relatórios

### KPIs Principais
1. **Taxa de Conversão**: Visitantes → Compradores
2. **Ticket Médio**: Valor médio por pedido
3. **Taxa de Abandono**: Carrinhos abandonados
4. **Tempo de Resposta**: Tempo médio de resposta nas conversas
5. **Satisfação**: Avaliações de entrega

### Relatórios para Vendedores
- Vendas por período
- Produtos mais vendidos
- Receita líquida (após comissão)
- Status de entregas
- Mensagens pendentes

### Relatórios Administrativos
- Receita total do marketplace
- Comissões arrecadadas
- Vendedores mais ativos
- Produtos em destaque
- Problemas de entrega

---

## 🔒 Segurança e Privacidade

### Proteção de Dados
- Mensagens criptografadas em repouso
- Acesso restrito aos participantes da conversa
- Administradores podem visualizar apenas em caso de disputa

### Moderação
- Sistema de denúncia para conteúdo inadequado
- Bloqueio automático de palavras proibidas
- Suspensão de usuários que violam termos

### Backup
- Backup diário de conversas
- Retenção por 90 dias após conclusão
- Exportação disponível para usuários (LGPD)

---

## 📝 Notas de Implementação

### Prioridades Imediatas
1. ✅ Estrutura de banco de dados
2. ⏳ Controllers para mensagens
3. ⏳ Interface de chat
4. ⏳ Notificações em tempo real
5. ⏳ Dashboard de conversas

### Backlog
- [ ] Integração com Correios
- [ ] Chat em tempo real (WebSocket)
- [ ] App mobile
- [ ] Chatbot para respostas automáticas
- [ ] Videochamada para demonstração de produtos
- [ ] Sistema de templates de mensagens

---

## 🔄 Histórico de Alterações

| Data | Versão | Descrição |
|------|--------|-----------|
| 03/01/2025 | 1.0 | Documento inicial com regras de comunicação e entrega |

---

## 📞 Contatos

Para dúvidas sobre as regras de negócio:
- **Product Owner**: [A definir]
- **Tech Lead**: [A definir]
- **Suporte**: suporte@marketplace-b2c.test
