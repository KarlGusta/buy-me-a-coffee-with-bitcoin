<?php

// Define base path
define('BASE_PATH', dirname(__DIR__));

// Define base URL (change this to your actual URL)
define('BASE_URL', 'http://localhost/bitcoin-coffee');

// Define site settings
define('SITE_NAME', 'Bitcoin Coffee');
define('SITE_DESCRIPTION', 'Support creators with Bitcoin donations');

// Set error reporting for development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/database.php';
?>