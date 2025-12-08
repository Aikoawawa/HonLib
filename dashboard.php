<?php
/**
 * Main dashboard - Shows books, search, and pomodoro timer
 */

require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Require login
require_login();

// Get all books
$books = get_books();
$search_results = $books;
$search_query = '';

// Handle search
if (isset($_GET['search'])) {
    $search_query = sanitize_input($_GET['search']);
    if (!empty($search_query)) {
        $search_results = array_filter($books, function($book) use ($search_query) {
            return stripos($book['title'], $search_query) !== false || 
                   stripos($book['author'], $search_query) !== false;
        });
    }
}

// Get user's borrow history
$history = get_borrow_history();
$user_history = array_filter($history, function($record) {
    return $record['user_id'] == $_SESSION['user_id'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Library Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <img src="assets/Logo.png" width="230px" height="98px">
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</span>
                <?php if (is_admin()): ?>
                    <a href="admin/manage_books.php" class="btn btn-secondary">Manage Books</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Pomodoro Timer -->
        <div class="pomodoro-section">
            <h2>Pomodoro Timer</h2>
            <div class="pomodoro-timer">
                <div class="timer-display" id="timerDisplay">25:00</div>
                <div class="timer-controls">
                    <button onclick="startTimer()" class="btn btn-primary">Start</button>
                    <button onclick="pauseTimer()" class="btn btn-secondary">Pause</button>
                    <button onclick="resetTimer()" class="btn btn-danger">Reset</button>
                </div>
            </div>
        </div>
        
        <!-- Search Section -->
        <div class="search-section">
            <h2>Search Books</h2>
            <form method="GET" action="">
                <div class="search-bar">
                    <input type="text" name="search"  autocomplete="off"placeholder="Search by title or author..." 
                           value="<?php echo htmlspecialchars($search_query); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <?php if ($search_query): ?>
                        <a href="dashboard.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Books Table -->
        <div class="books-section">
            <h2>Available Books <?php echo $search_query ? '(Search Results)' : ''; ?></h2>
            
            <?php if (empty($search_results)): ?>
                <p class="no-results">No books found.</p>
            <?php else: ?>
                <table class="books-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($search_results as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['year']); ?></td>
                                <td>
                                    <div class="rating">
                                        <?php 
                                        $avg = $book['average_rating'] ?? 0;
                                        echo number_format($avg, 1); 
                                        ?> ★ (<?php echo $book['total_ratings']; ?>)
                                    </div>
                                </td>
                                <td>
                                    <span class="status <?php echo $book['available'] ? 'available' : 'unavailable'; ?>">
                                        <?php echo $book['available'] ? 'Available' : 'Borrowed'; ?>
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="rate_book.php" style="display: inline;">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <select name="rating" required>
                                            <option value="">Rate</option>
                                            <option value="1">1 ★</option>
                                            <option value="2">2 ★</option>
                                            <option value="3">3 ★</option>
                                            <option value="4">4 ★</option>
                                            <option value="5">5 ★</option>
                                        </select>
                                        <button type="submit" class="btn btn-small">Submit</button>
                                    </form>
                                    
                                    <?php if ($book['available']): ?>
                                        <form method="POST" action="borrow_book.php" style="display: inline;">
                                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                            <button type="submit" class="btn btn-small btn-primary">Borrow</button>
                                        </form>
                                    <?php else: ?>
                                        <?php
                                        // Check if current user has this book borrowed
                                        $user_has_book = false;
                                        foreach ($history as $record) {
                                            if ($record['book_id'] == $book['id'] && 
                                                $record['user_id'] == $_SESSION['user_id'] && 
                                                $record['status'] === 'borrowed') {
                                                $user_has_book = true;
                                                break;
                                            }
                                        }
                                        ?>
                                        <?php if ($user_has_book): ?>
                                            <form method="POST" action="return_book.php" style="display: inline;">
                                                <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                                <button type="submit" class="btn btn-small btn-secondary">Return</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Borrow History -->
        <div class="history-section">
            <h2>My Borrow History</h2>
            
            <?php if (empty($user_history)): ?>
                <p class="no-results">No borrow history yet.</p>
            <?php else: ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Borrow Date</th>
                            <th>Return Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($user_history) as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['book_title']); ?></td>
                                <td><?php echo htmlspecialchars($record['borrow_date']); ?></td>
                                <td><?php echo $record['return_date'] ? htmlspecialchars($record['return_date']) : '-'; ?></td>
                                <td>
                                    <span class="status <?php echo $record['status'] === 'borrowed' ? 'unavailable' : 'available'; ?>">
                                        <?php echo ucfirst($record['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="js/pomodoro.js"></script>
</body>
</html>