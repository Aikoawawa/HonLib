<?php
/**
 * Login page - Entry point of the application
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if (login_user($username, $password)) {
            redirect('dashboard.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HonLib - Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header style="background-color: #1C1C1C">
        <img src="assets/Logo.png" width="230px" height="98px" style="margin: 16px";>
    </header>
    <div class="container">
        <div class="login-box">
            <h1>Login</h1>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="login-info">
                <p><strong>Demo Accounts:</strong></p><br>
                <p><strong>Admin Account:</strong></p>
                <p>Admin: username: <code>admin</code> , password: <code>password</code></p><br>
                <hr><br>
                <p><strong>User Accounts:</strong></p>
                <p>User: username: <code>john</code> , password: <code>password</code></p>
                <p>User: username: <code>Jamie</code> , password: <code>password</code></p>
            </div>
        </div>
    </div>
</body>
</html>