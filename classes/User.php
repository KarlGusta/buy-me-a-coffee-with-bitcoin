<?php

require_once __DIR__ . '/Database.php';

class User {
    private $id;
    private $username;
    private $email;
    private $password;
    private $created_at;
    private $db;

    public function __construct($user_data = null) {
        $this->db = Database::getInstance();

        if ($user_data) {
            $this->id = $user_data['id'] ?? null;
            $this->username = $user_data['username'] ?? null;
            $this->email = $user_data['email'] ?? null;
            $this->password = $user_data['password'] ?? null;
            $this->created_at = $user_data['created_at'] ?? null;
        }
    }

    // Register new user
    public function register() {
        if (!$this->username || !$this->email || !$this->password) {
            return false;
        }

        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $existing = $this->db->getRow($sql, [$this->username, $this->email]);
        
        if ($existing) {
            return false;
        }

        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $params = [$this->username, $this->email, $hashed_password];

        $this->id = $this->db->insert($sql, $params);

        return $this->id ? true : false;
    }

    // Login user
    public static function login($username_or_email, $password) {
        $db = Database::getInstance();

        // Check if input is email or username
        $is_email = 
    }
}