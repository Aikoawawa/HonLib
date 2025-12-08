<?php
require_once 'db.php';

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

function logout_user() {
    session_unset();
    session_destroy();
}

function require_login() {
    if (!is_logged_in()) {
        redirect('index.php');
    }
}


function require_admin() {
    require_login();
    if (!is_admin()) {
        redirect('dashboard.php');
    }
}
?>