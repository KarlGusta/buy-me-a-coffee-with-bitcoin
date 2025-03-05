<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Creator.php';
require_once __DIR__ . '/../classes/BitcoinWallet.php';
require_once __DIR__ . '/../classes/Donation.php';
require_once __DIR__ . '/../includes/bitcoin_helper.php';
require_once __DIR__ . '/../includes/session.php';

// Handle donation form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'donate') {
    // Get POST data
    $creator_id = $_POST['creator_id'] ?? null;
    $amount = floatval($_POST['amount'] ?? 0);
    $message = $_POST['message'] ?? '';
    $is_anonymous = isset($_POST['anonymous']) ? true : false;

    // Validate data
    if (!$creator_id || $amount <= 0) {
        $_SESSION['error'] = "Invalid donation data";
        header("Location: " . BASE_URL . "/donate.php?creator=" . $creator_id);
        exit;
    }

    // Get creator
    $creator = Creator::getById($creator_id);
    if (!$creator) {
        $_SESSION['error'] = "Creator not found";
        header("Location: " . BASE_URL);
        exit;
    }

    // Get current Bitcoin price
    $btc_price = get_bitcoin_price();
    if (!$btc_price) {
        $_SESSION['error'] = "Could not get Bitcoin price. Please try again later.";
        header("Location: " . BASE_URL . "/donate.php?creator=" . $creator_id);
        exit;
    }

    // Calculate Bitcoin amount
    $bitcoin_amount = $amount / $btc_price;

    // Get minimum donation from settings
    $db = Database::getInstance();
    $sql = "SELECT setting_value FROM settings WHERE setting_name = 'min_donation'";
    $result = $db->getRow($sql);
    $min_donation = floatval($result['setting_value'] ?? 0.0001);

    // Check if donation is above minimum
    if ($bitcoin_amount < $min_donation) {
        $_SESSION['error'] = "Minimum donation is " . number_format($min_donation * $btc_price, 2) . " USD";
        header("Location: " . BASE_URL . "/donate.php?creator=" . $creator_id);
        exit;
    }

    // Format Bitcoin amount to 8 decimal places
    $bitcoin_amount = number_format($bitcoin_amount, 8, '.', '');

    // Get creator's wallet
    $creator_user_id = $creator->getUserId();
    $wallet = BitcoinWallet::getPrimaryForUser($creator_user_id);

    if (!$wallet) {
        $_SESSION['error'] = "Creator has no wallet set up";
        header("Location: " . BASE_URL . "/donate.php?creator=" . $creator_id);
        exit;
    }

    // Get payment address
    $payment_address = $wallet->getUnusedAddress();

    if (!$payment_address) {
        $_SESSION['error'] = "Could not generate payment address";
        header("Location: " . BASE_URL . "/donate.php?creator=" . $creator_id);
        exit;
    }

    // Store donation info in session
    $_SESSION['pending_donation'] = [
        'creator_id' => $creator_id,
        'creator_name' => $creator->getDisplayName(),
        'amount' => $amount,
        'bitcoin_amount' => $bitcoin_amount,
        'payment_address' => $payment_address,
        'message' => $message,
        'is_anonymous' => $is_anonymous,
        'timestamp' => time()
    ];

    // Redirect to payment page
    header("Location: " . BASE_URL . "/payment.php");
    exit;
}