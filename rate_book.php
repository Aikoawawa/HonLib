<?php
/**
 * Handle book rating
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Require login
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    
    // Validate rating
    if ($rating < 1 || $rating > 5) {
        redirect('dashboard.php');
    }
    
    // Get book
    $book = get_book_by_id($book_id);
    if (!$book) {
        redirect('dashboard.php');
    }
    
    // Add rating
    $book['ratings'][] = $rating;
    $book['total_ratings'] = count($book['ratings']);
    $book['average_rating'] = array_sum($book['ratings']) / $book['total_ratings'];
    
    // Update book
    update_book($book_id, $book);
}

redirect('dashboard.php');
?>