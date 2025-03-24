<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Database.php';

class AuthHandler {
    private $db;
    private $user;
    
    public function __construct() {
        $this->db = new Database();
        $this->user = new User();
    }

    public function handleRequest() {
        $action = $_REQUEST['action'] ?? '';

        switch ($action) {
            case 'register':
                return $this->register();
            case 'login':
                return $this->login();
            case 'logout':
                return $this->logout();
            case 'checkAuth':
                return $this->checkAuth();
            default:
                return json_encode(['error' => 'Invalid action']);                
        }
    }

    private function register() {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
            return json_encode(['error' => 'All fields are required']);
        }

        $result = $this->user->registerUser($username, $email, $password);

        if (isset($result['error'])) {
            return json_encode($result);
        }

        $_SESSION['user_id'] = $result['user_id'];

        return json_encode([
            'success' => true,
            'user_id' => $result['user_id']
        ]);
    }

    private function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            return json_encode(['error' => 'Email and password are required']);
        }

        $result = $this->user->loginUser($email, $password);

        if (isset($result['error'])) {
            return json_encode($result);
        }

        $_SESSION['user_id'] = $result['user_id'];

        return json_encode([
            'success' => true,
            'user_id' => $result['user_id']
        ]);
    }

    private function logout() {
        session_start();
        session_destroy();

        return json_encode([
            'success' => true
        ]);
    }

    private function checkAuth() {
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            return json_encode([
                'authenticated' => false
            ]);
        }

        $userData = $this->user->getUserData($userId);

        return json_encode([
            'authenticated' => true,
            'user' => [
                'id' => $userId,
                'username' => $userData['username'],
                'email' => $userData['email']
            ]
        ]);
    }
}