<?php

require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

class CartController
{
    private $cartModel;
    private $productModel;

    public function __construct($pdo)
    {
        $this->cartModel = new Cart($pdo);
        $this->productModel = new Product($pdo);
    }

    public function addToCart($userId, $data)
    {
        try {
            // Kiểm tra dữ liệu đầu vào
            if (empty($userId) || empty($data['product_id']) || empty($data['quantity'])) {
                throw new Exception('User ID, product ID, and quantity are required.');
            }

            if (!is_numeric($data['quantity']) || $data['quantity'] <= 0) {
                throw new Exception('Quantity must be a positive number.');
            }

            // Kiểm tra sản phẩm tồn tại
            $product = $this->productModel->getById($data['product_id']);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            if ($product['stock'] < $data['quantity']) {
                throw new Exception('Insufficient stock for product: ' . $product['name']);
            }

            // Thêm sản phẩm vào giỏ hàng
            $result = $this->cartModel->addItem($userId, $data['product_id'], $data['quantity']);

            return [
                'success' => $result,
                'message' => $result ? 'Product added to cart successfully.' : 'Failed to add product to cart.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function updateCartItem($userId, $cartId, $quantity)
    {
        try {
            if (empty($userId) || empty($cartId) || empty($quantity)) {
                throw new Exception('User ID, cart ID, and quantity are required.');
            }

            if (!is_numeric($quantity) || $quantity <= 0) {
                throw new Exception('Quantity must be a positive number.');
            }

            // Lấy thông tin giỏ hàng
            $cartItem = $this->cartModel->getByUserId($userId);
            $itemToUpdate = null;
            foreach ($cartItem as $item) {
                if ($item['id'] == $cartId) {
                    $itemToUpdate = $item;
                    break;
                }
            }

            if (!$itemToUpdate) {
                throw new Exception('Cart item not found.');
            }

            // Kiểm tra sản phẩm tồn tại và còn đủ số lượng
            $product = $this->productModel->getById($itemToUpdate['product_id']);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            if ($product['stock'] < $quantity) {
                throw new Exception('Insufficient stock for product: ' . $product['name']);
            }

            // Cập nhật số lượng trong giỏ hàng
            $result = $this->cartModel->addItem($userId, $itemToUpdate['product_id'], $quantity - $itemToUpdate['quantity']);

            return [
                'success' => $result,
                'message' => $result ? 'Cart item updated successfully.' : 'Failed to update cart item.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function removeCartItem($userId, $cartId)
    {
        try {
            if (empty($userId) || empty($cartId)) {
                throw new Exception('User ID and cart ID are required.');
            }

            // Lấy thông tin giỏ hàng
            $cartItem = $this->cartModel->getByUserId($userId);
            $itemToRemove = null;
            foreach ($cartItem as $item) {
                if ($item['id'] == $cartId) {
                    $itemToRemove = $item;
                    break;
                }
            }

            if (!$itemToRemove) {
                throw new Exception('Cart item not found.');
            }

            // Xóa sản phẩm khỏi giỏ hàng
            $result = $this->cartModel->removeItem($cartId);

            return [
                'success' => $result,
                'message' => $result ? 'Cart item removed successfully.' : 'Failed to remove cart item.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function clearCart($userId)
    {
        try {
            if (empty($userId)) {
                throw new Exception('User ID is required.');
            }

            // Xóa toàn bộ giỏ hàng
            $result = $this->cartModel->clearCart($userId);

            return [
                'success' => $result,
                'message' => $result ? 'Cart cleared successfully.' : 'Failed to clear cart.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getCart($userId)
    {
        try {
            if (empty($userId)) {
                throw new Exception('User ID is required.');
            }

            $cartItems = $this->cartModel->getByUserId($userId);
            foreach ($cartItems as &$item) {
                $product = $this->productModel->getById($item['product_id']);
                if ($product) {
                    $item['product_name'] = $product['name'];
                    $item['product_price'] = $product['price'];
                }
            }

            return $cartItems;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
