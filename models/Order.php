<?php
// models/Order.php
class Order
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create($user_id, $total_price, $status = 'pending')
    {
        $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $total_price, $status]);
        return $this->pdo->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM orders");
        return $stmt->fetchAll();
    }
}