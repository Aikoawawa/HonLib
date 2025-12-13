<?php

require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/User.php';
require_once 'includes/Book.php';
require_once 'includes/Auth.php';

$auth = new Auth();
$bookModel = new Book();

// Require login
$auth->requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        redirect('dashboard.php');
    }
    
    // Add rating
    $bookModel->addRating($book_id, $rating);
}

redirect('dashboard.php');
?>