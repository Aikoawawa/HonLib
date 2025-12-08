<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Require admin
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    
    if ($book_id > 0) {
        delete_book($book_id);
    }
}

redirect('manage_books.php?success=deleted');
?>