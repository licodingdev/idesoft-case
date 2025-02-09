<?php

namespace Models;

use Config\Database;

abstract class BaseModel
{
    protected \PDO $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}