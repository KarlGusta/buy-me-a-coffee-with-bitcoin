<?php

class Database {
    private $host;
    private $username;
    private $password;
    private $dbname;
    private $conn;
    private static $instance;

    // Singleton pattern - private constructor
    private function __construct() {
        require_once __DIR__ . '/../config/database.php';

        $this->host = DB_HOST;
        $this->username = DB_USER;
        $this->password = DB_PASS;
        $this->dbname = DB_NAME;

        $this->connect();
    } 

    // Get instance (singleton pattern)
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Connect to database
    private function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    // Get database connection
    public function getConnection() {
        return $this->conn;
    }

    // Query with prepared statements
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            die("Query failed: " . $e->getMessage()); 
        }
    }

    // Get a single record
    public function getRow($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    // Get multiple records
    public function getRows($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    // Insert a record and return the ID
    public function insert($sql, $params = []) {
        $this->query($sql, $params);
        return $this->conn->lastInsertId();
    }

    // Update records
    public function update($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    // Delete records
    public function delete($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
}