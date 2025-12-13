<?php

require_once 'includes/config.php';
require_once 'includes/Database.php';
require_once 'includes/User.php';
require_once 'includes/Book.php';
require_once 'includes/BorrowHistory.php';
require_once 'includes/Auth.php';

$auth = new Auth();
$bookModel = new Book();
$historyModel = new BorrowHistory();

// Require login
$auth->requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    
    // Get book
    $book = $bookModel->getById($book_id);
    if ($book && $book['available']) {
        // Mark book as unavailable
        $bookModel->update($book_id, ['available' => false]);
        
        // Add borrow record
        $historyModel->addBorrow(
            $_SESSION['user_id'],
            $_SESSION['username'],
            $book_id,
            $book['title']
        );
    }
}

redirect('dashboard.php');
?>