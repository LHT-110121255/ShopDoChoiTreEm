<?php
// models/Review.php
class Review
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function addReview($user_id, $product_id, $rating, $comment)
    {
        $stmt = $this->pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$user_id, $product_id, $rating, $comment]);
    }

    public function getByProductId($product_id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM reviews WHERE product_id = ?");
        $stmt->execute([$product_id]);
        return $stmt->fetchAll();
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM reviews WHERE id = ?");
        return $stmt->execute([$id]);
    }
}