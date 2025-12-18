<?php
/**
 * Database Connection Tester
 * Kiểm tra kết nối database và hiển thị thông tin
 */

require_once 'config/config.php';
require_once 'config/database.php';

header('Content-Type: application/json; charset=utf-8');

$response = [
    'success' => false,
    'message' => '',
    'database' => [
        'host' => '127.0.0.1:3306',
        'name' => 'meo_clothingstore',
        'status' => 'disconnected'
    ],
    'tables' => [],
    'errors' => []
];

try {
    // Test database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Failed to establish database connection');
    }
    
    $response['database']['status'] = 'connected';
    $response['message'] = 'Database connection successful!';
    $response['success'] = true;
    
    // Get list of tables
    $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'meo_clothingstore'";
    $stmt = $conn->query($sql);
    $tables = $stmt->fetchAll();
    
    $expectedTables = [
        'categories', 'customers', 'employees', 'inventory', 
        'orderdetails', 'orders', 'payments', 'products', 
        'productvariants', 'promotions'
    ];
    
    foreach ($tables as $table) {
        $tableName = $table['TABLE_NAME'];
        
        // Get row count
        $countSql = "SELECT COUNT(*) as cnt FROM `$tableName`";
        $countStmt = $conn->query($countSql);
        $countResult = $countStmt->fetch();
        $rowCount = $countResult['cnt'];
        
        // Get columns
        $colSql = "DESCRIBE `$tableName`";
        $colStmt = $conn->query($colSql);
        $columns = $colStmt->fetchAll();
        
        $response['tables'][] = [
            'name' => $tableName,
            'rows' => (int) $rowCount,
            'columns' => count($columns),
            'status' => in_array($tableName, $expectedTables) ? 'expected' : 'extra',
            'columnDetails' => array_map(fn($col) => [
                'name' => $col['Field'],
                'type' => $col['Type'],
                'null' => $col['Null'],
                'key' => $col['Key'],
                'default' => $col['Default']
            ], $columns)
        ];
    }
    
    // Check for missing expected tables
    $tableNames = array_map(fn($t) => $t['name'], $response['tables']);
    $missingTables = array_diff($expectedTables, $tableNames);
    
    if (count($missingTables) > 0) {
        $response['errors'][] = 'Missing tables: ' . implode(', ', $missingTables);
        $response['success'] = false;
    }
    
    // Test sample queries
    $response['sample_data'] = [];
    
    // Sample from categories
    try {
        $stmt = $conn->query("SELECT * FROM categories LIMIT 1");
        $catSample = $stmt->fetch();
        if ($catSample) {
            $response['sample_data']['categories'] = $catSample;
        }
    } catch (Exception $e) {
        // Table might be empty
    }
    
    // Sample from promotions
    try {
        $stmt = $conn->query("SELECT * FROM promotions LIMIT 1");
        $promoSample = $stmt->fetch();
        if ($promoSample) {
            $response['sample_data']['promotions'] = $promoSample;
        }
    } catch (Exception $e) {
        // Table might be empty
    }
    
    // Sample from products
    try {
        $stmt = $conn->query("SELECT * FROM products LIMIT 1");
        $prodSample = $stmt->fetch();
        if ($prodSample) {
            $response['sample_data']['products'] = $prodSample;
        }
    } catch (Exception $e) {
        // Table might be empty
    }
    
    $database->closeConnection();
    
} catch (Exception $e) {
    $response['database']['status'] = 'error';
    $response['errors'][] = $e->getMessage();
    $response['success'] = false;
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
