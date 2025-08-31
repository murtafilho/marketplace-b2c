# 🧪 Como Executar Tests no Laravel

## 🚀 Métodos para Executar

### **1. Via Command Line (Recomendado)**
```bash
# Navegar para o diretório do projeto
cd C:\laragon\www\marketplace-b2c

# Executar teste específico
php artisan test tests/Feature/CategoryDisplayTest.php

# Com mais detalhes
php artisan test tests/Feature/CategoryDisplayTest.php --verbose

# Executar método específico
php artisan test --filter test_categories_exist_in_database
```

### **2. Via Script Batch (Windows)**
```bash
# Execute o arquivo que criamos
run-category-test.bat

# Ou clique duplo no arquivo
```

### **3. Via PHPUnit Direto**
```bash
# Se php artisan test não funcionar
./vendor/bin/phpunit tests/Feature/CategoryDisplayTest.php

# Windows
vendor\bin\phpunit.bat tests/Feature/CategoryDisplayTest.php
```

## 🔧 Se PHP não for Encontrado

### **Opção 1: Usar Laragon Terminal**
1. Abra o **Laragon**
2. Clique em **Terminal**
3. Execute os comandos normalmente

### **Opção 2: Usar XAMPP/WAMP Terminal**
1. Abra o terminal do seu servidor local
2. Navegue para o projeto
3. Execute os testes

### **Opção 3: Adicionar PHP ao PATH**
1. Encontre onde está o PHP (ex: `C:\laragon\bin\php\php-8.2.0-Win32-vs16-x64`)
2. Adicione ao PATH do Windows
3. Reinicie o terminal

### **Opção 4: Usar Caminho Completo**
```bash
# Substitua pelo seu caminho do PHP
C:\laragon\bin\php\php-8.2.0-Win32-vs16-x64\php artisan test tests/Feature/CategoryDisplayTest.php
```

## 🎯 Teste Rápido Alternativo

Se não conseguir executar os tests, crie este arquivo PHP simples:

**Arquivo: `debug-categories-simple.php`**
```php
<?php
// Carregar Laravel
require_once 'bootstrap/app.php';
$app = \Illuminate\Foundation\Application::getInstance();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DIAGNÓSTICO SIMPLES DE CATEGORIAS ===\n";

// Contar total
$total = App\Models\Category::count();
echo "📊 Total de categorias: {$total}\n";

if ($total === 0) {
    echo "❌ NENHUMA CATEGORIA ENCONTRADA!\n";
    echo "💡 Execute: php artisan db:seed\n";
} else {
    // Categorias principais
    $principais = App\Models\Category::whereNull('parent_id')->count();
    echo "🏠 Categorias principais: {$principais}\n";
    
    // Com imagens
    $comImagem = App\Models\Category::whereNotNull('image_path')->count();
    echo "🖼️  Com imagens: {$comImagem}\n";
    
    // Primeiras 5
    echo "\n📋 PRIMEIRAS 5 CATEGORIAS:\n";
    foreach (App\Models\Category::take(5)->get() as $cat) {
        $img = $cat->image_path ? '✅' : '❌';
        echo "   {$img} {$cat->name} (parent: {$cat->parent_id})\n";
    }
}
?>
```

**Execute:**
```bash
php debug-categories-simple.php
```

## 📱 Via Browser (Se nada funcionar)

Crie: `public/debug-categories.php`
```php
<?php
require_once '../bootstrap/app.php';
$app = \Illuminate\Foundation\Application::getInstance();
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$total = App\Models\Category::count();
$principais = App\Models\Category::whereNull('parent_id')->count();

echo "<h1>Debug Categorias</h1>";
echo "<p><strong>Total:</strong> {$total}</p>";
echo "<p><strong>Principais:</strong> {$principais}</p>";

if ($total > 0) {
    echo "<h2>Categorias:</h2><ul>";
    foreach (App\Models\Category::take(10)->get() as $cat) {
        $img = $cat->image_path ? 'SIM' : 'NÃO';
        echo "<li>{$cat->name} - Imagem: {$img} - Parent: {$cat->parent_id}</li>";
    }
    echo "</ul>";
}
?>
```

**Acesse:** `http://localhost/marketplace-b2c/public/debug-categories.php`

---

**🎯 Execute qualquer um desses métodos e me informe o resultado!**