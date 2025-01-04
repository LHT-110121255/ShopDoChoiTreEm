<?php

// models/User.php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($username, $password, $email, $fullname, $phone, $address, $profile_picture, $role = 'user')
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password, email, fullname, phone, address, profile_picture, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $password, $email, $fullname, $phone, $address, $profile_picture, $role]);
        return $this->pdo->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $username, $email, $fullname, $phone, $address, $profile_picture, $role)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET username = ?, email = ?, fullname = ?, phone = ?, address = ?, profile_picture = ?, role = ? WHERE id = ?");
        return $stmt->execute([$username, $email, $fullname, $phone, $address, $profile_picture, $role, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }
}