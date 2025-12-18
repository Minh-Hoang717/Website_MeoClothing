<?php
/**
 * API Test Suite - Manual Testing Script
 * Run individual test endpoints to verify functionality
 */

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$action = $_GET['test'] ?? 'list';
$apiBase = 'http://localhost/api';

// Sample admin token for testing
$adminToken = $_GET['token'] ?? 'test-admin-token';

$tests = [
    'list' => [
        'name' => 'List all available tests',
        'url' => $_SERVER['REQUEST_URI'],
        'examples' => [
            '?test=db',
            '?test=promo-list',
            '?test=promo-create&token=YOUR_TOKEN',
            '?test=promo-apply',
            '?test=reports-revenue',
            '?test=reports-customers',
            '?test=reports-products'
        ]
    ],
    'db' => [
        'name' => 'Database Connection Test',
        'url' => $apiBase . '/test-db.php',
        'method' => 'GET',
        'description' => 'Check database connectivity and table structure'
    ],
    'promo-list' => [
        'name' => 'List Promotions',
        'url' => $apiBase . '/promotions',
        'method' => 'GET',
        'params' => ['filter' => 'active', 'page' => 1, 'pageSize' => 20]
    ],
    'promo-create' => [
        'name' => 'Create Promotion',
        'url' => $apiBase . '/promotions',
        'method' => 'POST',
        'headers' => ['Authorization: Bearer ' . $adminToken],
        'body' => [
            'code' => 'TEST' . time(),
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ]
    ],
    'promo-apply' => [
        'name' => 'Apply Promotion Code',
        'url' => $apiBase . '/promotions/apply',
        'method' => 'POST',
        'body' => [
            'code' => 'SUMMER10',
            'total_amount' => 500000
        ]
    ],
    'reports-revenue' => [
        'name' => 'Revenue Report',
        'url' => $apiBase . '/reports/revenue?group_by=daily',
        'method' => 'GET'
    ],
    'reports-customers' => [
        'name' => 'Customer Report',
        'url' => $apiBase . '/reports/customers?metric=summary',
        'method' => 'GET'
    ],
    'reports-products' => [
        'name' => 'Product Report',
        'url' => $apiBase . '/reports/products?metric=best_selling',
        'method' => 'GET'
    ]
];

if ($action === 'list') {
    echo json_encode($tests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else if (isset($tests[$action])) {
    $test = $tests[$action];
    $result = [
        'test' => $action,
        'info' => $test,
        'curl_command' => buildCurlCommand($test),
        'javascript_fetch' => buildFetchCommand($test),
        'timestamp' => date('Y-m-d H:i:s')
    ];
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Test not found']);
}

function buildCurlCommand($test) {
    $cmd = "curl -X " . ($test['method'] ?? 'GET') . " '" . $test['url'] . "'";
    
    if (!empty($test['headers'])) {
        foreach ($test['headers'] as $header) {
            $cmd .= " \\\n  -H '" . $header . "'";
        }
    }
    
    if (!empty($test['body'])) {
        $cmd .= " \\\n  -H 'Content-Type: application/json' \\\n  -d '" . json_encode($test['body']) . "'";
    }
    
    return $cmd;
}

function buildFetchCommand($test) {
    $options = [
        'method' => "'" . ($test['method'] ?? 'GET') . "'"
    ];
    
    if (!empty($test['headers'])) {
        $options['headers'] = $test['headers'];
    }
    
    if (!empty($test['body'])) {
        $options['headers'][] = 'Content-Type: application/json';
        $options['body'] = "JSON.stringify(" . json_encode($test['body']) . ")";
    }
    
    $code = "fetch('" . $test['url'] . "', {\n";
    foreach ($options as $key => $value) {
        if (is_array($value)) {
            $code .= "  $key: " . json_encode($value) . ",\n";
        } else {
            $code .= "  $key: $value,\n";
        }
    }
    $code .= "}).then(r => r.json()).then(d => console.log(d));";
    
    return $code;
}
?>
