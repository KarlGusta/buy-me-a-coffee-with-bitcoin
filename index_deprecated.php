<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/Creator.php';

// Get featured creators
$featured_creators = Creator::getFeatured(6);

// Get total creator count
$db = Database::getInstance();
$creator_count = $db->getRow("SELECT COUNT(*) as count FROM creators")['count'];

// Get donation stats
$donation_stats = $db->getRow("SELECT COUNT(*) as count, SUM(amount) as total
                                    FROM donations
                                    WHERE status = 'confirmed'");

// Load the home view
require_once __DIR__ . '/views/home.php';
?>                                    