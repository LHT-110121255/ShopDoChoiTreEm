<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/OrderController.php';

$pdo = require __DIR__ . '/../../config/database.php';
$orderController = new OrderController($pdo);

$data = json_decode(file_get_contents('php://input'), true);
$response = $orderController->createOrder($data);

header('Content-Type: application/json');
echo json_encode($response);
