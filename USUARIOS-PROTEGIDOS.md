# 🔒 USUÁRIOS PROTEGIDOS DO MARKETPLACE

## ⚡ SOLUÇÕES RÁPIDAS

### Problema: Perdi os usuários após migrate:fresh
```bash
# Solução SUPER RÁPIDA (duplo clique no arquivo)
restore-users.bat

# OU comando direto
php artisan marketplace:ensure-protected-users
```

### Problema: Quero apenas verificar se existem
```bash
php artisan marketplace:ensure-protected-users --verify
```

## 🔑 CREDENCIAIS SEMPRE DISPONÍVEIS

| Tipo | Email | Senha | Acesso |
|------|-------|-------|--------|
| **Admin** | admin@marketplace.com | admin123 | `/admin/dashboard` |
| **Seller** | tech@marketplace.com | seller123 | `/seller/dashboard` |
| **Customer** | cliente@marketplace.com | cliente123 | `/` |

## 🛡️ COMO FUNCIONA A PROTEÇÃO

1. **DatabaseSeeder sempre executa primeiro** os usuários protegidos
2. **ProtectedUsersSeeder** cria/atualiza sem duplicar
3. **Comando dedicado** para restauração rápida
4. **Script .bat** para Windows (duplo clique)

## 📚 COMANDOS ÚTEIS

```bash
# Ver todos os usuários
php artisan users:list

# Restaurar usuários protegidos
php artisan marketplace:ensure-protected-users

# Seed completo (inclui usuários automaticamente)
php artisan db:seed

# Reset completo + seed
php artisan migrate:fresh --seed
```

## 🎯 NUNCA MAIS PERDER USUÁRIOS!

Os usuários protegidos são **SEMPRE** recriados automaticamente em qualquer operação de seed. O sistema foi projetado para ser **infalível**.