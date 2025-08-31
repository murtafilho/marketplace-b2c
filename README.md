# 🌅 Vale do Sol Marketplace

> Plataforma B2C de marketplace desenvolvida em Laravel com sistema avançado de customização de layout

---

## 📋 Sobre o Projeto

O **Vale do Sol Marketplace** é uma plataforma completa de e-commerce B2C desenvolvida em Laravel, focada em conectar vendedores locais com consumidores. O projeto inclui um sistema administrativo robusto com funcionalidades avançadas de customização de layout em tempo real.

### 🎯 Características Principais

- **Multi-Auth System**: Autenticação separada para admins, vendedores e compradores
- **Layout Customization**: Sistema completo de personalização visual em tempo real
- **Responsive Design**: Interface mobile-first com Tailwind CSS
- **Admin Dashboard**: Painel administrativo completo
- **Seller Onboarding**: Sistema de cadastro e aprovação de vendedores
- **Product Management**: CRUD completo de produtos (em desenvolvimento)

---

## 🎨 Sistema de Layout Customization

### ✨ Funcionalidades

- **🎭 Gestão de Temas**: 4 temas pré-configurados (Default, Dark, Minimal, Colorful)
- **🎨 Customização de Cores**: Paleta completa com suporte a Tailwind CSS e hexadecimais
- **📱 Controle de Seções**: Gerenciamento de visibilidade de componentes
- **👁️ Preview em Tempo Real**: Visualização instantânea das mudanças
- **💾 Persistência**: Configurações salvas no banco de dados
- **🔄 Import/Export**: Backup e restauração de configurações

### 🛠️ Tecnologias Utilizadas

- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Alpine.js, Tailwind CSS, Axios
- **Database**: MySQL/PostgreSQL
- **Cache**: Redis (opcional)

### 📍 Acesso ao Sistema

```bash
# URL do sistema de customização
http://marketplace-b2c.test/admin/layout/customize
```

---

## 🚀 Instalação e Configuração

### 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- MySQL/PostgreSQL
- Laragon (recomendado para desenvolvimento local)

### ⚙️ Configuração do Ambiente

1. **Clone o repositório**
```bash
git clone <repository-url>
cd marketplace-b2c
```

2. **Instale as dependências**
```bash
composer install
npm install
```

3. **Configure o ambiente**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure o banco de dados**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=marketplace_b2c
DB_USERNAME=root
DB_PASSWORD=
```

5. **Execute as migrações**
```bash
php artisan migrate --seed
```

6. **Compile os assets**
```bash
npm run dev
# ou para produção
npm run build
```

7. **Inicie o servidor**
```bash
php artisan serve
# ou use Laragon
```

---

## 📚 Documentação

### 📖 Documentos Disponíveis

- **[Layout Customization](docs/LAYOUT-CUSTOMIZATION.md)**: Documentação completa do sistema de customização
- **[Project Status](docs/PROJECT-STATUS.md)**: Status atual do desenvolvimento
- **[Implementações](docs/IMPLEMENTACOES.md)**: Histórico detalhado de implementações
- **[Correções Aplicadas](docs/CORRECTIONS_APPLIED.md)**: Relatório de correções e melhorias

### 🔗 Links Úteis

- **Admin Dashboard**: `/admin`
- **Layout Customization**: `/admin/layout/customize`
- **Seller Onboarding**: `/seller/onboarding`
- **API Documentation**: Em desenvolvimento

---

## 🏗️ Arquitetura do Projeto

### 📁 Estrutura Principal

```
marketplace-b2c/
├── app/
│   ├── Http/Controllers/Admin/     # Controllers administrativos
│   ├── Models/                     # Models Eloquent
│   ├── Services/                   # Serviços de negócio
│   └── Http/Middleware/           # Middlewares customizados
├── resources/
│   ├── views/admin/               # Views administrativas
│   └── js/                        # Assets JavaScript
├── database/
│   ├── migrations/                # Migrações do banco
│   └── seeders/                   # Seeders de dados
└── docs/                          # Documentação
```

### 🎯 Componentes Principais

- **LayoutSetting Model**: Gerenciamento de configurações

- **LayoutCustomizationController**: API REST
- **ApplyLayoutSettings Middleware**: Aplicação automática

---

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Testes específicos
php artisan test --filter=LayoutCustomization

# Com coverage
php artisan test --coverage
```

---

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

---

## 📄 Licença

Este projeto está licenciado sob a [MIT License](LICENSE).

---

## 👥 Equipe

- **Desenvolvimento**: Equipe Vale do Sol
- **Design**: Sistema baseado em Tailwind CSS
- **Arquitetura**: Laravel 11 + Alpine.js

---

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o projeto:

- **Email**: suporte@valedosol.org
- **Documentação**: [docs/](docs/)
- **Issues**: GitHub Issues

---

*Projeto desenvolvido com ❤️ pela equipe Vale do Sol*
*Última atualização: Janeiro 2025*
