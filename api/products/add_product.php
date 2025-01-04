<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controllers/ProductController.php';

$pdo = require __DIR__ . '/../../config/database.php';
$productController = new ProductController($pdo);

$data = json_decode(file_get_contents('php://input'), true);
$response = $productController->createProduct($data, $_FILES);

header('Content-Type: application/json');
echo json_encode($response);
