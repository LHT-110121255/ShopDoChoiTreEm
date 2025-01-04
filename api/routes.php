<?php

// Autoload các controller và cấu hình
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/ReviewController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';

// Khởi tạo PDO và các controller
$pdo = require __DIR__ . '/../config/database.php';
$authController = new AuthController($pdo);
$userController = new UserController($pdo);
$productController = new ProductController($pdo);
$orderController = new OrderController($pdo);
$cartController = new CartController($pdo);
$reviewController = new ReviewController($pdo);
$categoryController = new CategoryController($pdo);

// Lấy URL và method hiện tại
$method = $_SERVER['REQUEST_METHOD'];
$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Xử lý route
switch ($url) {
    // ----------------- Auth Routes -----------------
    case '/api/auth/login':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($authController->login($data));
        }
        break;

    case '/api/auth/register':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($authController->register($data));
        }
        break;

    case '/api/auth/logout':
        if ($method === 'POST') {
            echo json_encode(['success' => true, 'message' => 'Logout successful']);
        }
        break;

    // ----------------- User Routes -----------------
    case '/api/users':
        if ($method === 'GET') {
            echo json_encode($userController->getAllUsers());
        }
        break;

    case '/api/users/create':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($userController->createUser($data));
        }
        break;

    case '/api/users/update':
        if ($method === 'PUT') {
            $userId = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($userController->updateUser($userId, $data));
        }
        break;

    case '/api/users/delete':
        if ($method === 'DELETE') {
            $userId = $_GET['id'] ?? null;
            echo json_encode($userController->deleteUser($userId));
        }
        break;

    // ----------------- Product Routes -----------------
    case '/api/products':
        if ($method === 'GET') {
            echo json_encode($productController->getAllProducts());
        }
        break;

    case '/api/products/create':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($productController->createProduct($data, $_FILES));
        }
        break;

    case '/api/products/update':
        if ($method === 'PUT') {
            $productId = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($productController->updateProduct($productId, $data, $_FILES));
        }
        break;

    case '/api/products/delete':
        if ($method === 'DELETE') {
            $productId = $_GET['id'] ?? null;
            echo json_encode($productController->deleteProduct($productId));
        }
        break;

    // ----------------- Order Routes -----------------
    case '/api/orders':
        if ($method === 'GET') {
            echo json_encode($orderController->getAllOrders());
        }
        break;

    case '/api/orders/create':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($orderController->createOrder($data));
        }
        break;

    case '/api/orders/update':
        if ($method === 'PUT') {
            $orderId = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($orderController->updateOrderStatus($orderId, $data['status']));
        }
        break;

    // ----------------- Cart Routes -----------------
    case '/api/cart':
        if ($method === 'GET') {
            $userId = $_GET['user_id'] ?? null;
            echo json_encode($cartController->getCart($userId));
        }
        break;

    case '/api/cart/add':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_GET['user_id'] ?? null;
            echo json_encode($cartController->addToCart($userId, $data));
        }
        break;

    case '/api/cart/update':
        if ($method === 'PUT') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_GET['user_id'] ?? null;
            $cartId = $_GET['cart_id'] ?? null;
            echo json_encode($cartController->updateCartItem($userId, $cartId, $data['quantity']));
        }
        break;

    case '/api/cart/clear':
        if ($method === 'DELETE') {
            $userId = $_GET['user_id'] ?? null;
            echo json_encode($cartController->clearCart($userId));
        }
        break;

    // ----------------- Review Routes -----------------
    case '/api/reviews':
        if ($method === 'GET') {
            $productId = $_GET['product_id'] ?? null;
            echo json_encode($reviewController->getReviewsByProductId($productId));
        }
        break;

    case '/api/reviews/add':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $userId = $_GET['user_id'] ?? null;
            echo json_encode($reviewController->addReview($userId, $data));
        }
        break;

    case '/api/reviews/delete':
        if ($method === 'DELETE') {
            $userId = $_GET['user_id'] ?? null;
            $reviewId = $_GET['review_id'] ?? null;
            echo json_encode($reviewController->deleteReview($userId, $reviewId));
        }
        break;

    // ----------------- Category Routes -----------------
    case '/api/categories':
        if ($method === 'GET') {
            echo json_encode($categoryController->getAllCategories());
        }
        break;

    case '/api/categories/create':
        if ($method === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($categoryController->createCategory($data));
        }
        break;

    case '/api/categories/update':
        if ($method === 'PUT') {
            $categoryId = $_GET['id'] ?? null;
            $data = json_decode(file_get_contents('php://input'), true);
            echo json_encode($categoryController->updateCategory($categoryId, $data));
        }
        break;

    case '/api/categories/delete':
        if ($method === 'DELETE') {
            $categoryId = $_GET['id'] ?? null;
            echo json_encode($categoryController->deleteCategory($categoryId));
        }
        break;

    // ----------------- Default Route -----------------
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}
