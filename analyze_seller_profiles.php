<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace-b2c', 'root', '');

echo "=== SELLER_PROFILES - ESTRUTURA ATUAL ===\n";
$columns = $pdo->query('DESCRIBE seller_profiles')->fetchAll(PDO::FETCH_ASSOC);
foreach($columns as $col) {
    printf("%-25s | %-20s | %-5s | %-10s | %s\n", 
        $col['Field'], 
        $col['Type'], 
        $col['Null'], 
        $col['Default'] ?: 'NULL',
        $col['Extra']
    );
}

echo "\n=== COMPARAÇÃO COM DICIONÁRIO ===\n";

$expected = [
    'id' => 'BIGINT UNSIGNED AUTO_INCREMENT',
    'user_id' => 'BIGINT UNSIGNED NOT NULL',
    'document_type' => 'VARCHAR(10) NULL',
    'document_number' => 'VARCHAR(20) NULL UNIQUE',
    'company_name' => 'VARCHAR(255) NULL',
    'address_proof_path' => 'VARCHAR(500) NULL',
    'identity_proof_path' => 'VARCHAR(500) NULL', 
    'phone' => 'VARCHAR(20) NULL',
    'address' => 'TEXT NULL',
    'city' => 'VARCHAR(100) NULL',
    'state' => 'VARCHAR(2) NULL',
    'postal_code' => 'VARCHAR(10) NULL',
    'bank_name' => 'VARCHAR(100) NULL',
    'bank_agency' => 'VARCHAR(10) NULL',
    'bank_account' => 'VARCHAR(20) NULL',
    'status' => 'VARCHAR(20) NOT NULL DEFAULT pending',
    'rejection_reason' => 'TEXT NULL',
    'commission_rate' => 'DECIMAL(5,2) NOT NULL DEFAULT 10.00',
    'product_limit' => 'INT NOT NULL DEFAULT 100',
    'mp_access_token' => 'VARCHAR(500) NULL',
    'mp_user_id' => 'VARCHAR(50) NULL',
    'mp_connected' => 'TINYINT(1) NOT NULL DEFAULT 0',
    'approved_at' => 'TIMESTAMP NULL',
    'rejected_at' => 'TIMESTAMP NULL',
    'submitted_at' => 'TIMESTAMP NULL',
    'rejected_by' => 'BIGINT UNSIGNED NULL FK to users',
    'approved_by' => 'BIGINT UNSIGNED NULL FK to users',
    'created_at' => 'TIMESTAMP NULL',
    'updated_at' => 'TIMESTAMP NULL'
];

$actual = [];
foreach($columns as $col) {
    $type = $col['Type'];
    $null = $col['Null'] === 'YES' ? 'NULL' : 'NOT NULL';
    $default = $col['Default'] ? "DEFAULT {$col['Default']}" : '';
    $extra = $col['Extra'];
    
    $actual[$col['Field']] = "{$type} {$null} {$default} {$extra}";
}

echo "\nCAMPOS FALTANDO NO BANCO:\n";
foreach($expected as $field => $spec) {
    if (!isset($actual[$field])) {
        echo "❌ FALTA: {$field} => {$spec}\n";
    }
}

echo "\nCAMPOS NO BANCO QUE NÃO ESTÃO NO DICIONÁRIO:\n";
foreach($actual as $field => $spec) {
    if (!isset($expected[$field])) {
        echo "⚠️ EXTRA: {$field} => {$spec}\n";
    }
}

echo "\nSTATUS POSSÍVEIS SEGUNDO DICIONÁRIO:\n";
echo "- pending: Aguardando aprovação\n";
echo "- approved: Aprovado e ativo\n";
echo "- rejected: Rejeitado\n";
echo "- suspended: Suspenso temporariamente\n";

echo "\nVERIFICAR SE STATUS ATUAL PERMITE:\n";
$statusQuery = $pdo->query("SELECT DISTINCT status FROM seller_profiles");
$statuses = $statusQuery->fetchAll(PDO::FETCH_COLUMN);
foreach($statuses as $status) {
    echo "✅ Status encontrado: {$status}\n";
}
?>