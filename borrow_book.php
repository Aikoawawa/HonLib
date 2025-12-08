<?php
/**
 * Handle book borrowing
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Require login
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    
    // Get book
    $book = get_book_by_id($book_id);
    if ($book && $book['available']) {
        // Mark book as unavailable
        $book['available'] = false;
        update_book($book_id, $book);
        
        // Add borrow record
        add_borrow_record(
            $_SESSION['user_id'],
            $_SESSION['username'],
            $book_id,
            $book['title']
        );
    }
}

redirect('dashboard.php');
?>