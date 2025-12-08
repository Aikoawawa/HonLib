<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Require admin
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $author = sanitize_input($_POST['author'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $available = isset($_POST['available']) && $_POST['available'] == '1';
    
    // Validate input
    if (empty($title) || empty($author) || $year < 1000) {
        redirect('manage_books.php');
    }
    
    // Create book array
    $book = [
        'title' => $title,
        'author' => $author,
        'year' => $year,
        'available' => $available
    ];
    
    // Add book
    add_book($book);
    
    redirect('manage_books.php?success=added');
}

redirect('manage_books.php');
?>
