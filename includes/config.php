<?php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base path - works from both root and admin folder
if (!defined('BASE_PATH')) {

    if (basename(dirname(__FILE__)) === 'includes' && file_exists(__DIR__ . '/../data/')) {
        define('BASE_PATH', __DIR__ . '/../');
    } else {
        define('BASE_PATH', __DIR__ . '/');
    }
}

define('DATA_PATH', BASE_PATH . 'data/');
define('USERS_FILE', DATA_PATH . 'users.json');
define('BOOKS_FILE', DATA_PATH . 'books.json');
define('HISTORY_FILE', DATA_PATH . 'borrowHistory.json');

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($page) {
    header("Location: $page");
    exit();
}

function generate_id($array) {
    if (empty($array)) {
        return 1;
    }
    $ids = array_column($array, 'id');
    return max($ids) + 1;
}
?>
