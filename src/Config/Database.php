<?php

namespace Config;

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host = getenv('DB_HOST') ?: 'mysql';
        $dbname = getenv('DB_DATABASE') ?: 'ideasoft';
        $username = getenv('DB_USERNAME') ?: 'ideasoft';
        $password = getenv('DB_PASSWORD') ?: 'ideasoft123';

        try {
            $this->connection = new \PDO(
                "mysql:host=$host;dbname=$dbname",
                $username,
                $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        } catch (\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}