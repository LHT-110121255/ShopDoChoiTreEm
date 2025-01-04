<?php

require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function createUser($data)
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
            $existingUsers = $this->userModel->getAll();
            foreach ($existingUsers as $user) {
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
            $userId = $this->userModel->create(
                $data['username'],
                $hashedPassword,
                $data['email'],
                $data['fullname'] ?? '',
                $data['phone'] ?? '',
                $data['address'] ?? '',
                $data['profile_picture'] ?? '',
                $data['role'] ?? 'user'
            );

            return [
                'success' => true,
                'message' => 'User created successfully.',
                'user_id' => $userId,
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getUserById($id)
    {
        try {
            if (empty($id)) {
                throw new Exception('User ID is required.');
            }

            $user = $this->userModel->getById($id);

            if (!$user) {
                throw new Exception('User not found.');
            }

            return $user;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function updateUser($id, $data)
    {
        try {
            if (empty($id)) {
                throw new Exception('User ID is required.');
            }

            if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format.');
            }

            // Kiểm tra người dùng tồn tại
            $user = $this->userModel->getById($id);
            if (!$user) {
                throw new Exception('User not found.');
            }

            // Cập nhật người dùng
            $result = $this->userModel->update(
                $id,
                $data['username'] ?? $user['username'],
                $data['email'] ?? $user['email'],
                $data['fullname'] ?? $user['fullname'],
                $data['phone'] ?? $user['phone'],
                $data['address'] ?? $user['address'],
                $data['profile_picture'] ?? $user['profile_picture'],
                $data['role'] ?? $user['role']
            );

            return [
                'success' => $result,
                'message' => $result ? 'User updated successfully.' : 'Failed to update user.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function deleteUser($id)
    {
        try {
            if (empty($id)) {
                throw new Exception('User ID is required.');
            }

            // Kiểm tra người dùng tồn tại
            $user = $this->userModel->getById($id);
            if (!$user) {
                throw new Exception('User not found.');
            }

            // Xóa người dùng
            $result = $this->userModel->delete($id);

            return [
                'success' => $result,
                'message' => $result ? 'User deleted successfully.' : 'Failed to delete user.',
            ];
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getAllUsers()
    {
        try {
            $users = $this->userModel->getAll();
            return $users;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
