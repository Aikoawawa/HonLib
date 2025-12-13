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


$auth->requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = intval($_POST['book_id'] ?? 0);
    
    
    $book = $bookModel->getById($book_id);
    if ($book) {
        
        $bookModel->update($book_id, ['available' => true]);
        
        
        $historyModel->returnBook($_SESSION['user_id'], $book_id);
    }
}

redirect('dashboard.php');
?>