<?php
/**
 * Script para comparar estrutura do banco vs migrations vs models
 * FONTE DA VERDADE: Banco de dados atual
 */

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace-b2c', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Obter estrutura atual do banco
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    
    echo "=== ANÁLISE COMPARATIVA: BANCO vs MIGRATIONS vs MODELS ===\n";
    echo "Data: " . date('Y-m-d H:i:s') . "\n\n";
    
    $databaseStructure = [];
    $systemTables = ['cache', 'cache_locks', 'migrations', 'jobs', 'job_batches', 'failed_jobs', 'password_reset_tokens', 'sessions'];
    
    foreach($tables as $table) {
        if (in_array($table, $systemTables)) continue;
        
        $columns = $pdo->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        $databaseStructure[$table] = $columns;
        
        echo "TABLE: $table\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach($columns as $column) {
            $field = $column['Field'];
            $type = $column['Type'];
            $null = $column['Null'];
            $key = $column['Key'];
            $default = $column['Default'];
            $extra = $column['Extra'];
            
            printf("  %-25s | %-20s | %-5s | %-5s | %-10s | %s\n", 
                $field, $type, $null, $key, $default ?: 'NULL', $extra
            );
        }
        echo "\n";
    }
    
    // Verificar models existentes
    echo "\n=== VERIFICANDO MODELS ===\n";
    $modelPath = __DIR__ . '/app/Models/';
    if (is_dir($modelPath)) {
        $models = glob($modelPath . '*.php');
        foreach($models as $model) {
            $modelName = basename($model, '.php');
            echo "Model: $modelName\n";
        }
    }
    
    // Verificar migrations
    echo "\n=== VERIFICANDO MIGRATIONS ===\n";
    $migrationPath = __DIR__ . '/database/migrations/';
    if (is_dir($migrationPath)) {
        $migrations = glob($migrationPath . '*.php');
        foreach($migrations as $migration) {
            $migrationName = basename($migration);
            if (!in_array($migrationName, [
                '0001_01_01_000000_create_users_table.php',
                '0001_01_01_000001_create_cache_table.php', 
                '0001_01_01_000002_create_jobs_table.php'
            ])) {
                echo "Migration: $migrationName\n";
            }
        }
    }
    
} catch(Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>