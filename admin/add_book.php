<?php

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/User.php';
require_once __DIR__ . '/../includes/Book.php';
require_once __DIR__ . '/../includes/Auth.php';

$auth = new Auth();
$bookModel = new Book();

$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title'] ?? '');
    $author = sanitize_input($_POST['author'] ?? '');
    $year = intval($_POST['year'] ?? 0);
    $available = isset($_POST['available']) && $_POST['available'] == '1';
    
    
    if (empty($title) || empty($author) || $year < 1000) {
        redirect('manage_books.php?error=invalid');
        exit;
    }
    
    
    $book = [
        'title' => $title,
        'author' => $author,
        'year' => $year,
        'available' => $available
    ];
    
   
    if ($bookModel->add($book)) {
        redirect('manage_books.php?success=added');
    } else {
        redirect('manage_books.php?error=failed');
    }
    exit;
}

redirect('manage_books.php');
?>