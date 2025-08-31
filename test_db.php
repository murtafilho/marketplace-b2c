<?php

// Teste de conexão com o banco de dados
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "Conexão com MySQL: OK\n";
    
    // Criar banco se não existir
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `marketplace-b2c`");
    echo "Banco 'marketplace-b2c' criado/verificado: OK\n";
    
    // Conectar ao banco específico
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace-b2c', 'root', '');
    echo "Conexão com banco 'marketplace-b2c': OK\n";
    
    // Verificar se tabela sessions existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
    if ($stmt->rowCount() > 0) {
        echo "Tabela 'sessions' existe: OK\n";
    } else {
        echo "Tabela 'sessions' NÃO existe - precisa executar migrations\n";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}