<?php

// Script para verificar tabelas criadas
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace-b2c', 'root', '');
    echo "<h2>Tabelas no banco 'marketplace-b2c':</h2>";
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "<p style='color: red;'>Nenhuma tabela encontrada! Migrations não foram executadas.</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
        // Verificar especificamente a tabela sessions
        if (in_array('sessions', $tables)) {
            echo "<p style='color: green;'>✅ Tabela 'sessions' existe!</p>";
        } else {
            echo "<p style='color: red;'>❌ Tabela 'sessions' NÃO existe!</p>";
        }
        
        // Verificar layout_settings
        if (in_array('layout_settings', $tables)) {
            echo "<p style='color: green;'>✅ Tabela 'layout_settings' existe!</p>";
        } else {
            echo "<p style='color: red;'>❌ Tabela 'layout_settings' NÃO existe!</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Erro: " . $e->getMessage() . "</p>";
}