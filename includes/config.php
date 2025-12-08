<?php
/**
 * Configuration file - Contains constants and helper functions
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base path - works from both root and admin folder
if (!defined('BASE_PATH')) {
    // Check if we're in admin folder
    if (basename(dirname(__FILE__)) === 'includes' && file_exists(__DIR__ . '/../data/')) {
        define('BASE_PATH', __DIR__ . '/../');
    } else {
        define('BASE_PATH', __DIR__ . '/');
    }
}

// Define paths
define('DATA_PATH', BASE_PATH . 'data/');
define('USERS_FILE', DATA_PATH . 'users.json');
define('BOOKS_FILE', DATA_PATH . 'books.json');
define('HISTORY_FILE', DATA_PATH . 'borrowHistory.json');

/**
 * Sanitize input to prevent XSS
 */
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect to a page
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Generate unique ID
 */
function generate_id($array) {
    if (empty($array)) {
        return 1;
    }
    $ids = array_column($array, 'id');
    return max($ids) + 1;
}
?>
