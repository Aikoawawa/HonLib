<?php
/**
 * Authentication functions
 */

require_once 'db.php';

/**
 * Login user
 */
function login_user($username, $password) {
    $user = get_user_by_username($username);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        return true;
    }
    return false;
}

/**
 * Logout user
 */
function logout_user() {
    session_unset();
    session_destroy();
}

/**
 * Require login
 */
function require_login() {
    if (!is_logged_in()) {
        redirect('index.php');
    }
}

/**
 * Require admin
 */
function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect('dashboard.php');
    }
}
?>