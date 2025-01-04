<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function register($data)
    {
        try {
            // Kiểm tra dữ liệu đầu vào
            if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
                throw new Exception('Username, password, and email are required.');
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format.');
            }

            // Kiểm tra tài khoản đã tồn tại
            $existingUser = $this->userModel->getAll();
            foreach ($existingUser as $user) {
                if ($user['username'] === $data['username']) {
                    throw new Exception('Username already exists.');
                }
                if ($user['email'] === $data['email']) {
                    throw new Exception('Email already registered.');
                }
            }

            // Mã hóa mật khẩu
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);

            // Tạo người dùng mới
            return $this->userModel->create(
                $data['username'],
                $hashedPassword,
                $data['email'],
                $data['fullname'] ?? '',
                $data['phone'] ?? '',
                $data['address'] ?? '',
                $data['profile_picture'] ?? '',
                $data['role'] ?? 'user'
            );
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function login($data)
    {
        try {
            // Kiểm tra dữ liệu đầu vào
            if (empty($data['username']) || empty($data['password'])) {
                throw new Exception('Username and password are required.');
            }

            // Lấy thông tin người dùng
            $users = $this->userModel->getAll();
            $user = null;
            foreach ($users as $u) {
                if ($u['username'] === $data['username']) {
                    $user = $u;
                    break;
                }
            }

            if (!$user) {
                throw new Exception('User not found.');
            }

            // Kiểm tra mật khẩu
            if (!password_verify($data['password'], $user['password'])) {
                throw new Exception('Invalid password.');
            }

            // Tạo token JWT (giả sử bạn sử dụng JWT)
            $payload = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'exp' => time() + 3600 // Token hết hạn sau 1 giờ
            ];
            $jwt = $this->generateJWT($payload);

            return [
                'message' => 'Login successful.',
                'token' => $jwt,
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function logout()
    {
        // Trong trường hợp JWT, client chỉ cần xóa token khỏi storage (localStorage hoặc cookies)
        return ['message' => 'Logout successful.'];
    }

    private function generateJWT($payload)
    {
        // Giả sử bạn sử dụng JWT với thư viện firebase/php-jwt
        $secretKey = $_ENV['JWT_SECRET'] ?? 'default_secret';
        return \Firebase\JWT\JWT::encode($payload, $secretKey, 'HS256');
    }
}
