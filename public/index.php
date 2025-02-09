<?php
require_once __DIR__ . '/../vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Load environment variables
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Basic routing
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Router
try {
    if (preg_match('#^/orders$#', $uri)) {
        $controller = new \Controllers\OrderController();
        if ($method === 'GET') {
            echo $controller->index();
        } elseif ($method === 'POST') {
            echo $controller->create();
        }
    }
    elseif (preg_match('#^/orders/(\d+)$#', $uri, $matches)) {
        $controller = new \Controllers\OrderController();
        if ($method === 'GET') {
            echo $controller->show($matches[1]);
        } elseif ($method === 'DELETE') {
            echo $controller->delete($matches[1]);
        }
    }
    elseif (preg_match('#^/orders/(\d+)/discounts$#', $uri, $matches)) {
        $controller = new \Controllers\DiscountController();
        echo $controller->calculate($matches[1]);
    }
    else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}