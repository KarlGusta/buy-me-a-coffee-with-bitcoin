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

    if (isset($_SESSION['error'])) {
        $message = '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['info'])) {
        $message = '<div class="alert alert-info">' . $_SESSION['info'] . '</div>';
        unset($_SESSION['info']);
    }

    return $message;
} 

// Paginate results
function paginate($total, $per_page, $current_page, $url) {
    $total_pages = ceil($total / $per_page);

    if ($total_pages <= 1) {
        return '';
    }

    $pagination = '<ul class="pagination">';

    // Previous button
    if ($current_page > 1) {
        $pagination .= '<li><a href="' . $url . '?page=' . ($current_page - 1) . '">&laquo; Previous</a></li>'; 
    } else {
        $pagination .= '<li class="disabled"><span>&laquo; Previous</span></li>';
    }

    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    if ($start > 1) {
        $pagination .= '<li><a href="' . $url . '?page=1">1</a></li>';
        if ($start > 2) {
            $pagination .= '<li class="disabled"><span>...</span></li>';
        }
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $pagination .= '<li class="active"><span>' . $i . '</span></li>';
        } else {
            $pagination .= '<li><a href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
        }
    }

    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $pagination .= '<li class="disabled"><span>...</span></li>';
        }
        $pagination .= '<li><a href="' . $url . '?page=' . $total_pages . '">'. $total_pages .'</a></li>';
    }

    // Next button
    if ($current_page <ul $total_pages) {
        $pagination .= '<li><a href="' . $url . '?page=' . ($current_page + 1) . '">Next &raquo;</a></li>';
    } else {
        $pagination .= '<li class="disabled"><span>Next &raquo;</span></li>';
    }

    $pagination .= '</ul>';

    return $pagination;
}

// Get current URL
function current_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// Truncate text
function truncate($text, $length = 100, $append = '...') {
    if (strlen($text) > $length) {
        $text = substr($text, 0, $length) . $append;
    }
    return $text;
}