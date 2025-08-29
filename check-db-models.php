<?php
/**
 * Script para verificar inconsistências entre Models e Banco de Dados
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "\n" . str_repeat("=", 80) . "\n";
echo "🔍 ANÁLISE DE INCONSISTÊNCIAS ENTRE MODELS E BANCO DE DADOS\n";
echo str_repeat("=", 80) . "\n\n";

$models = [
    'User' => App\Models\User::class,
    'Category' => App\Models\Category::class,
    'Product' => App\Models\Product::class,
    'ProductImage' => App\Models\ProductImage::class,
    'ProductVariation' => App\Models\ProductVariation::class,
    'SellerProfile' => App\Models\SellerProfile::class,
];

$inconsistencies = [];

foreach ($models as $name => $modelClass) {
    echo "📋 Analisando Model: $name\n";
    echo str_repeat("-", 40) . "\n";
    
    $model = new $modelClass;
    $table = $model->getTable();
    
    // Verificar se a tabela existe
    if (!Schema::hasTable($table)) {
        echo "❌ ERRO: Tabela '$table' não existe no banco!\n\n";
        $inconsistencies[$name][] = "Tabela '$table' não existe";
        continue;
    }
    
    // Pegar campos fillable do model
    $fillable = $model->getFillable();
    
    // Pegar colunas da tabela
    $columns = Schema::getColumnListing($table);
    
    // Campos fillable que não existem no banco
    $missingInDb = array_diff($fillable, $columns);
    if (!empty($missingInDb)) {
        echo "⚠️  Campos no Model que NÃO existem no banco:\n";
        foreach ($missingInDb as $field) {
            echo "   - $field\n";
            $inconsistencies[$name][] = "Campo '$field' está no fillable mas não existe no banco";
        }
    }
    
    // Campos no banco que não estão no fillable (exceto campos padrão)
    $standardFields = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'email_verified_at'];
    $dbOnlyFields = array_diff($columns, $fillable, $standardFields);
    if (!empty($dbOnlyFields)) {
        echo "📌 Campos no banco que NÃO estão no fillable:\n";
        foreach ($dbOnlyFields as $field) {
            echo "   - $field\n";
        }
    }
    
    // Verificar tipos de dados
    echo "📊 Estrutura completa da tabela '$table':\n";
    foreach ($columns as $column) {
        $type = Schema::getColumnType($table, $column);
        $nullable = DB::select("SHOW COLUMNS FROM $table WHERE Field = ?", [$column])[0]->Null ?? 'NO';
        $nullableStr = $nullable === 'YES' ? ' (nullable)' : ' (not null)';
        echo "   • $column: $type$nullableStr\n";
    }
    
    echo "\n";
}

// Resumo das inconsistências
if (!empty($inconsistencies)) {
    echo str_repeat("=", 80) . "\n";
    echo "❗ RESUMO DAS INCONSISTÊNCIAS ENCONTRADAS\n";
    echo str_repeat("=", 80) . "\n\n";
    
    foreach ($inconsistencies as $model => $issues) {
        echo "🔴 $model:\n";
        foreach ($issues as $issue) {
            echo "   - $issue\n";
        }
        echo "\n";
    }
    
    echo "⚡ AÇÕES RECOMENDADAS:\n";
    echo "1. Criar migrations para adicionar campos faltantes\n";
    echo "2. Atualizar os models para refletir a estrutura do banco\n";
    echo "3. Verificar se os seeders estão usando os campos corretos\n";
} else {
    echo "\n✅ Nenhuma inconsistência crítica encontrada!\n";
}

echo "\n" . str_repeat("=", 80) . "\n";