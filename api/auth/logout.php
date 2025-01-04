<?php
header('Content-Type: application/json');

// Với JWT, logout có thể chỉ cần client xóa token.
// API này chỉ để xác nhận rằng người dùng đã đăng xuất.
$response = [
    'success' => true,
    'message' => 'Logout successful.'
];

echo json_encode($response);
