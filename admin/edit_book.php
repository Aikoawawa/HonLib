<?php
/**
 * Handle editing book
 */

require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Require admin
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    $title = sanitize_input($_POST['title'] ?? '');
    $author = sanitize_input($_POST['author'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $available = isset($_POST['available']) && $_POST['available'] == '1';
    
    // Validate input
    if ($book_id < 1 || empty($title) || empty($author) || $year < 1000) {
        redirect('manage_books.php');
    }
    
    // Get existing book to preserve ratings
    $existing_book = get_book_by_id($book_id);
    if (!$existing_book) {
        redirect('manage_books.php');
    }
    
    // Create updated book array
    $updated_book = [
        'title' => $title,
        'author' => $author,
        'year' => $year,
        'available' => $available
    ];
    
    // Update book
    update_book($book_id, $updated_book);
    
    redirect('manage_books.php?success=updated');
}

redirect('manage_books.php');
?>