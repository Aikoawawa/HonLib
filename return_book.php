<?php
/**
 * Handle book return
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
    if ($book) {
        // Mark book as available
        $book['available'] = true;
        update_book($book_id, $book);
        
        // Update borrow history - mark as returned
        $history = get_borrow_history();
        foreach ($history as $key => $record) {
            if ($record['book_id'] == $book_id && 
                $record['user_id'] == $_SESSION['user_id'] && 
                $record['status'] === 'borrowed') {
                $history[$key]['return_date'] = date('Y-m-d');
                $history[$key]['status'] = 'returned';
                break;
            }
        }
        save_borrow_history($history);
    }
}

redirect('dashboard.php');
?>