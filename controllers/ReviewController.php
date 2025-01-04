<?php

require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

class ReviewController
{
    private $reviewModel;
    private $productModel;
    private $userModel;

    public function __construct($pdo)
    {
        $this->reviewModel = new Review($pdo);
        $this->productModel = new Product($pdo);
        $this->userModel = new User($pdo);
    }

    public function addReview($userId, $data)
    {
        try {
            // Kiểm tra dữ liệu đầu vào
            if (empty($userId) || empty($data['product_id']) || empty($data['rating'])) {
                throw new Exception('User ID, product ID, and rating are required.');
            }

            if (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5) {
                throw new Exception('Rating must be a number between 1 and 5.');
            }

            // Kiểm tra sản phẩm tồn tại
            $product = $this->productModel->getById($data['product_id']);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            // Kiểm tra người dùng tồn tại
            $user = $this->userModel->getById($userId);
            if (!$user) {
                throw new Exception('User not found.');
            }

            // Thêm đánh giá
            $result = $this->reviewModel->addReview(
                $userId,
                $data['product_id'],
                $data['rating'],
                $data['comment'] ?? ''
            );

            return [
                'success' => $result,
                'message' => $result ? 'Review added successfully.' : 'Failed to add review.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getReviewsByProductId($productId)
    {
        try {
            if (empty($productId)) {
                throw new Exception('Product ID is required.');
            }

            $product = $this->productModel->getById($productId);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            $reviews = $this->reviewModel->getByProductId($productId);

            foreach ($reviews as &$review) {
                $user = $this->userModel->getById($review['user_id']);
                if ($user) {
                    $review['user_name'] = $user['username'];
                }
            }

            return $reviews;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function updateReview($userId, $reviewId, $data)
    {
        try {
            if (empty($userId) || empty($reviewId)) {
                throw new Exception('User ID and review ID are required.');
            }

            // Kiểm tra đánh giá tồn tại
            $review = $this->reviewModel->getByProductId($reviewId);
            $foundReview = null;
            foreach ($review as $r) {
                if ($r['id'] == $reviewId) {
                    $foundReview = $r;
                    break;
                }
            }

            if (!$foundReview || $foundReview['user_id'] != $userId) {
                throw new Exception('Review not found or access denied.');
            }

            if (!empty($data['rating']) && (!is_numeric($data['rating']) || $data['rating'] < 1 || $data['rating'] > 5)) {
                throw new Exception('Rating must be a number between 1 and 5.');
            }

            // Cập nhật đánh giá
            $result = $this->reviewModel->addReview(
                $userId,
                $data['product_id'],
                $data['rating'],
                $data['comment'] ?? ''
            );

            return [
                'success' => $result,
                'message' => $result ? 'Review updated successfully.' : 'Failed to update review.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteReview($userId, $reviewId)
    {
        try {
            if (empty($userId) || empty($reviewId)) {
                throw new Exception('User ID and review ID are required.');
            }

            // Kiểm tra đánh giá tồn tại
            $review = $this->reviewModel->getByProductId($reviewId);
            $foundReview = null;
            foreach ($review as $r) {
                if ($r['id'] == $reviewId) {
                    $foundReview = $r;
                    break;
                }
            }

            if (!$foundReview || $foundReview['user_id'] != $userId) {
                throw new Exception('Review not found or access denied.');
            }

            // Xóa đánh giá
            $result = $this->reviewModel->delete($reviewId);

            return [
                'success' => $result,
                'message' => $result ? 'Review deleted successfully.' : 'Failed to delete review.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}