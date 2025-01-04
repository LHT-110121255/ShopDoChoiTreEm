<?php

require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/ProductImage.php';

class ProductController
{
    private $productModel;
    private $productImageModel;

    public function __construct($pdo)
    {
        $this->productModel = new Product($pdo);
        $this->productImageModel = new ProductImage($pdo);
    }

    public function createProduct($data, $files)
    {
        try {
            // Kiểm tra dữ liệu đầu vào
            if (empty($data['name']) || empty($data['price']) || empty($data['stock']) || empty($data['category_id'])) {
                throw new Exception('Name, price, stock, and category ID are required.');
            }

            if (!is_numeric($data['price']) || $data['price'] <= 0) {
                throw new Exception('Invalid price.');
            }

            if (!is_numeric($data['stock']) || $data['stock'] < 0) {
                throw new Exception('Invalid stock value.');
            }

            // Tạo sản phẩm mới
            $productId = $this->productModel->create(
                $data['name'],
                $data['description'] ?? '',
                $data['price'],
                $data['stock'],
                $data['category_id']
            );

            // Xử lý hình ảnh
            if (!empty($files['images'])) {
                $this->uploadImages($productId, $files['images']);
            }

            return [
                'success' => true,
                'message' => 'Product created successfully.',
                'product_id' => $productId,
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getProductById($id)
    {
        try {
            if (empty($id)) {
                throw new Exception('Product ID is required.');
            }

            $product = $this->productModel->getById($id);

            if (!$product) {
                throw new Exception('Product not found.');
            }

            $product['images'] = $this->productImageModel->getByProductId($id);

            return $product;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function updateProduct($id, $data, $files)
    {
        try {
            if (empty($id)) {
                throw new Exception('Product ID is required.');
            }

            // Kiểm tra sản phẩm tồn tại
            $product = $this->productModel->getById($id);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            // Cập nhật sản phẩm
            $result = $this->productModel->update(
                $id,
                $data['name'] ?? $product['name'],
                $data['description'] ?? $product['description'],
                $data['price'] ?? $product['price'],
                $data['stock'] ?? $product['stock'],
                $data['category_id'] ?? $product['category_id']
            );

            // Xử lý hình ảnh mới (nếu có)
            if (!empty($files['images'])) {
                $this->uploadImages($id, $files['images']);
            }

            return [
                'success' => $result,
                'message' => $result ? 'Product updated successfully.' : 'Failed to update product.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteProduct($id)
    {
        try {
            if (empty($id)) {
                throw new Exception('Product ID is required.');
            }

            // Kiểm tra sản phẩm tồn tại
            $product = $this->productModel->getById($id);
            if (!$product) {
                throw new Exception('Product not found.');
            }

            // Xóa hình ảnh liên quan
            $images = $this->productImageModel->getByProductId($id);
            foreach ($images as $image) {
                if (file_exists($image['image_url'])) {
                    unlink($image['image_url']);
                }
                $this->productImageModel->delete($image['id']);
            }

            // Xóa sản phẩm
            $result = $this->productModel->delete($id);

            return [
                'success' => $result,
                'message' => $result ? 'Product deleted successfully.' : 'Failed to delete product.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllProducts()
    {
        try {
            $products = $this->productModel->getAll();
            foreach ($products as &$product) {
                $product['images'] = $this->productImageModel->getByProductId($product['id']);
            }
            return $products;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function uploadImages($productId, $images)
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $uploadDir = __DIR__ . '/../uploads/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($images['tmp_name'] as $key => $tmpName) {
            if ($images['error'][$key] === UPLOAD_ERR_OK) {
                $extension = pathinfo($images['name'][$key], PATHINFO_EXTENSION);
                if (!in_array(strtolower($extension), $allowedExtensions)) {
                    throw new Exception('Invalid file type for image: ' . $images['name'][$key]);
                }

                $filename = uniqid() . '.' . $extension;
                $filePath = $uploadDir . $filename;

                if (!move_uploaded_file($tmpName, $filePath)) {
                    throw new Exception('Failed to upload image: ' . $images['name'][$key]);
                }

                // Lưu thông tin ảnh vào database
                $this->productImageModel->create($productId, $filePath);
            }
        }
    }
}