<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=marketplace-b2c', 'root', '');

echo "=== ESTRUTURA ORDERS ===\n";
$orders = $pdo->query('DESCRIBE orders')->fetchAll(PDO::FETCH_ASSOC);
foreach($orders as $col) {
    printf("%-25s | %-20s | %-5s\n", $col['Field'], $col['Type'], $col['Null']);
}

echo "\n=== ESTRUTURA SUB_ORDERS ===\n";
$subOrders = $pdo->query('DESCRIBE sub_orders')->fetchAll(PDO::FETCH_ASSOC);
foreach($subOrders as $col) {
    printf("%-25s | %-20s | %-5s\n", $col['Field'], $col['Type'], $col['Null']);
}

echo "\n=== ESTRUTURA ORDER_ITEMS ===\n";
$orderItems = $pdo->query('DESCRIBE order_items')->fetchAll(PDO::FETCH_ASSOC);
foreach($orderItems as $col) {
    printf("%-25s | %-20s | %-5s\n", $col['Field'], $col['Type'], $col['Null']);
}

echo "\n=== RELACIONAMENTOS E REGRAS ===\n";
echo "1. Order (1) -> User (N) - Um usuário pode ter várias orders\n";
echo "2. Order (1) -> SubOrders (N) - Uma order pode ter vários sub-orders\n";
echo "3. SubOrder (1) -> Seller/User (N) - Cada sub-order pertence a um vendedor\n";  
echo "4. OrderItem (1) -> Order (N) - Items pertencem à order principal\n";
echo "5. OrderItem (1) -> SubOrder (N) - Items também pertencem ao sub-order\n";
echo "6. OrderItem (1) -> Product (N) - Items referenciam produtos\n";

echo "\n=== VERIFICAÇÃO MULTI-VENDEDOR ===\n";
// Verificar se há orders com múltiplos vendedores
try {
    $stmt = $pdo->query("
        SELECT 
            o.id as order_id,
            o.order_number,
            COUNT(DISTINCT so.seller_id) as num_sellers,
            GROUP_CONCAT(DISTINCT so.seller_id) as seller_ids
        FROM orders o
        JOIN sub_orders so ON o.id = so.order_id
        GROUP BY o.id, o.order_number
        HAVING COUNT(DISTINCT so.seller_id) > 1
        LIMIT 5
    ");
    
    $multiVendorOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($multiVendorOrders) {
        echo "ENCONTRADAS ORDERS COM MÚLTIPLOS VENDEDORES:\n";
        foreach($multiVendorOrders as $order) {
            printf("Order %s: %d vendedores (IDs: %s)\n", 
                $order['order_number'], 
                $order['num_sellers'], 
                $order['seller_ids']
            );
        }
    } else {
        echo "NENHUMA ORDER COM MÚLTIPLOS VENDEDORES ENCONTRADA\n";
    }
    
    // Total de orders
    $totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    echo "\nTotal de Orders no sistema: $totalOrders\n";
    
    // Total de sub-orders
    $totalSubOrders = $pdo->query("SELECT COUNT(*) FROM sub_orders")->fetchColumn();
    echo "Total de Sub-Orders no sistema: $totalSubOrders\n";
    
} catch(Exception $e) {
    echo "Erro ao verificar: " . $e->getMessage() . "\n";
}
?>