<?php 
// models/Cart.php
class Cart
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addItem($user_id, $product_id, $quantity)
    {
        $stmt = $this->pdo->prepare("INSERT INTO carts (user_id, product_id, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
        return $stmt->execute([$user_id, $product_id, $quantity]);
    }

    public function getByUserId($user_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carts WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function removeItem($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM carts WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function clearCart($user_id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM carts WHERE user_id = ?");
        return $stmt->execute([$user_id]);
    }
}
