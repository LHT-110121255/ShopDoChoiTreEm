<?php

require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderDetail.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController
{
    private $orderModel;
    private $orderDetailModel;
    private $productModel;

    public function __construct($pdo)
    {
        $this->orderModel = new Order($pdo);
        $this->orderDetailModel = new OrderDetail($pdo);
        $this->productModel = new Product($pdo);
    }

    public function createOrder($data)
    {
        try {
            // Kiểm tra dữ liệu đầu vào
            if (empty($data['user_id']) || empty($data['items'])) {
                throw new Exception('User ID and items are required.');
            }

            if (!is_array($data['items']) || count($data['items']) === 0) {
                throw new Exception('Invalid items format.');
            }

            // Tính tổng giá trị đơn hàng
            $totalPrice = 0;
            foreach ($data['items'] as $item) {
                $product = $this->productModel->getById($item['product_id']);

                if (!$product) {
                    throw new Exception('Product not found: ' . $item['product_id']);
                }

                if ($product['stock'] < $item['quantity']) {
                    throw new Exception('Insufficient stock for product: ' . $product['name']);
                }

                $totalPrice += $product['price'] * $item['quantity'];
            }

            // Tạo đơn hàng mới
            $orderId = $this->orderModel->create($data['user_id'], $totalPrice);

            // Tạo chi tiết đơn hàng và cập nhật kho
            foreach ($data['items'] as $item) {
                $product = $this->productModel->getById($item['product_id']);
                $this->orderDetailModel->addDetail($orderId, $item['product_id'], $item['quantity'], $product['price']);

                // Giảm số lượng kho
                $this->productModel->update(
                    $item['product_id'],
                    $product['name'],
                    $product['description'],
                    $product['price'],
                    $product['stock'] - $item['quantity'],
                    $product['category_id']
                );
            }

            return [
                'success' => true,
                'message' => 'Order created successfully.',
                'order_id' => $orderId,
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getOrderById($id)
    {
        try {
            if (empty($id)) {
                throw new Exception('Order ID is required.');
            }

            $order = $this->orderModel->getById($id);

            if (!$order) {
                throw new Exception('Order not found.');
            }

            $order['details'] = $this->orderDetailModel->getByOrderId($id);

            return $order;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function updateOrderStatus($id, $status)
    {
        try {
            if (empty($id)) {
                throw new Exception('Order ID is required.');
            }

            if (!in_array($status, ['pending', 'confirmed', 'in_transit', 'completed', 'cancelled'])) {
                throw new Exception('Invalid order status.');
            }

            // Kiểm tra đơn hàng tồn tại
            $order = $this->orderModel->getById($id);
            if (!$order) {
                throw new Exception('Order not found.');
            }

            // Cập nhật trạng thái đơn hàng
            $result = $this->orderModel->update($id, $status);

            return [
                'success' => $result,
                'message' => $result ? 'Order status updated successfully.' : 'Failed to update order status.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteOrder($id)
    {
        try {
            if (empty($id)) {
                throw new Exception('Order ID is required.');
            }

            // Kiểm tra đơn hàng tồn tại
            $order = $this->orderModel->getById($id);
            if (!$order) {
                throw new Exception('Order not found.');
            }

            // Xóa chi tiết đơn hàng
            $details = $this->orderDetailModel->getByOrderId($id);
            foreach ($details as $detail) {
                $this->orderDetailModel->delete($detail['id']);
            }

            // Xóa đơn hàng
            $result = $this->orderModel->delete($id);

            return [
                'success' => $result,
                'message' => $result ? 'Order deleted successfully.' : 'Failed to delete order.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllOrders()
    {
        try {
            $orders = $this->orderModel->getAll();
            foreach ($orders as &$order) {
                $order['details'] = $this->orderDetailModel->getByOrderId($order['id']);
            }
            return $orders;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
