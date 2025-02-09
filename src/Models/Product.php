<?php

namespace Models;

class Product extends BaseModel
{
    protected string $table = 'products';

    public function findByCategory($categoryId)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE category = :category");
        $stmt->execute(['category' => $categoryId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}