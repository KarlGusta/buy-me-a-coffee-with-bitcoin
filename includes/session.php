<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Require login to access page
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['error'] = "You must be logged in to access this page";
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

// Check if user is a creator
function is_creator() {
    if (!is_logged_in()) {
        return false;
    }

    require_once __DIR__ . '/../classes/User.php';
    $user = User::getById($_SESSION['user_id']);

    return $user && $user->isCreator();
}

// Require creator status to access page
function require_creator() {
    require_login();

    if (!is_creator()) {
        $_SESSION['error'] = "You must be a creator to access this page";
        header("Location: " . BASE_URL . "/become-creator.php");
        exit;
    }
}

// Log out user
function logout() {
    // Unset all session variables
    $_SESSION = [];

    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();
}
?>