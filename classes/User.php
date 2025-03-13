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
        $is_email = filter_var($username_or_email, FILTER_VALIDATE_EMAIL);
        $field = $is_email ? 'email' : 'username';
        
        $sql = "SELECT * FROM users WHERE {$field} = ?"; 
        $user_data = $db->getRow($sql, [$username_or_email]);

        if (!$user_data) {
            return false;
        }

        // Verify password
        if (!password_verify($password, $user_data['password'])) {
            return false;
        }

        return new User($user_data);
    }

    // Update user
    public function update() {
        if (!$this->id) {
            return false;
        }

        $sql = "UPDATE users SET username = ?, email = ? WHERE id = ?";
        $params = [$this->username, $this->email, $this->id];

        return $this->db->update($sql, $params) > 0;
    }

    // Change password
    public function changePassword($current_password, $new_password) {
        if (!$this->id) {
            return false;
        }

        // Get current password hash
        $sql = "SELECT password FROM users WHERE id = ?";
        $result = $this->db->getRow($sql, [$this->id]);

        if (!$result) {
            return false;
        }

        // Verify current password
        if (!password_verify($current_password, $result['password'])) {
            return false;
        }

        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->update($sql, [$hashed_password, $this->id]) > 0;
    }

    // Get user by ID
    public static function getById($user_id) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE id = ?";
        $user_data = $db->getRow($sql, [$user_id]);

        if (!$user_data) {
            return null;
        }

        return new User($user_data);
    }

    // Get user by username
    public static function getByUsername($username) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE username = ?";
        $user_data = $db->getRow($sql, [$username]);
        
        if (!$user_data) {
            return null;
        }

        return new User($user_data);
    }

    // Get user by email
    public static function getByEmail($email) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM users WHERE email = ?";
        $user_data = $db->getRow($sql, [$email]);
        
        if (!$user_data) {
            return null;
        }

        return new User($user_data);
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return  $this->username;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    // Check if user is a creator
    public function isCreator() {
        if (!$this->id) {
            return false;
        }

        $sql = "SELECT id FROM creators WHERE user_id = ?";
        $result = $this->db->getRow($sql, [$this->id]);

        return $result ? true : false;
    }

    // Get creator profile
    public function getCreatorProfile() {
        if (!$this->id) {
            return null;
        }

        require_once __DIR__ . '/Creator.php';
        return Creator::getByUserId($this->id);
    }
}