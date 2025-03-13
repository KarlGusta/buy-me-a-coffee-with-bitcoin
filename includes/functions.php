<?php

// Format date
function format_date($date, $format = 'M j, Y g:i A') {
    return date($format, strtotime($date));
}

// Format currency
function format_currency($amount, $currency = 'USD', $decimals = 2) {
    return number_format($amount, $decimals, '.', ',') . ' ' . $currency;
}

// Sanitize input
function sanitize($input) {
    if (is_array($input)) {
        foreach ($input as $key => $val) {
            $input[$key] = sanitize($val);
        }
        return $input;
    }

    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Flash messages
function flash_message() {
    $message = '';

    if (isset($_SESSION['success'])) {
        $message = '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }

    if ()
} 