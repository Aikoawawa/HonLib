<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Book.php';
require_once __DIR__ . '/../includes/Auth.php';

$auth = new Auth();
$bookModel = new Book();

// Require admin
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    $title = sanitize_input($_POST['title'] ?? '');
    $author = sanitize_input($_POST['author'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $available = isset($_POST['available']) && $_POST['available'] == '1';
    
    // Validate input
    if ($book_id < 1 || empty($title) || empty($author) || $year < 1000) {
        redirect('manage_books.php?error=invalid');
        exit;
    }
    
    // Get existing book to check if it exists
    $existing_book = $bookModel->getById($book_id);
    if (!$existing_book) {
        redirect('manage_books.php?error=notfound');
        exit;
    }
    
    // Create updated book array
    $updated_book = [
        'title' => $title,
        'author' => $author,
        'year' => $year,
        'available' => $available
    ];
    
    // Update book
    if ($bookModel->update($book_id, $updated_book)) {
        redirect('manage_books.php?success=updated');
    } else {
        redirect('manage_books.php?error=failed');
    }
    exit;
}

redirect('manage_books.php');
?>