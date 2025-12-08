<?php
/**
 * Admin book management page
 */

// Set the correct include path
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Require admin
require_admin();

// Get all books
$books = get_books();

// Success/Error messages
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Library Management System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>Manage Books</h1>
            <div class="user-info">
                <a href="../dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if ($success === 'added'): ?>
            <div class="success">Book added successfully!</div>
        <?php elseif ($success === 'updated'): ?>
            <div class="success">Book updated successfully!</div>
        <?php elseif ($success === 'deleted'): ?>
            <div class="success">Book deleted successfully!</div>
        <?php elseif ($error === 'invalid'): ?>
            <div class="error">Invalid input. Please check your data.</div>
        <?php elseif ($error === 'failed'): ?>
            <div class="error">Operation failed. Please try again.</div>
        <?php elseif ($error === 'notfound'): ?>
            <div class="error">Book not found.</div>
        <?php endif; ?>
        
        <!-- Add New Book Form -->
        <div class="admin-section">
            <h2>Add New Book</h2>
            <form method="POST" action="add_book.php" class="book-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author:</label>
                        <input type="text" id="author" name="author" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Year:</label>
                        <input type="number" id="year" name="year" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="available" value="1" checked>
                            Available
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Add Book</button>
            </form>
        </div>
        
        <!-- Books List -->
        <div class="admin-section">
            <h2>All Books</h2>
            
            <?php if (empty($books)): ?>
                <p class="no-results">No books in the system.</p>
            <?php else: ?>
                <table class="books-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo $book['id']; ?></td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['year']); ?></td>
                                <td>
                                    <span class="status <?php echo $book['available'] ? 'available' : 'unavailable'; ?>">
                                        <?php echo $book['available'] ? 'Available' : 'Borrowed'; ?>
                                    </span>
                                </td>
                                <td><?php echo number_format($book['average_rating'] ?? 0, 1); ?> ★</td>
                                <td>
                                    <button onclick="editBook(<?php echo $book['id']; ?>)" class="btn btn-small btn-secondary">Edit</button>
                                    <form method="POST" action="delete_book.php" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this book?');">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Edit Book Modal (hidden by default) -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h2>Edit Book</h2>
                <form method="POST" action="edit_book.php" id="editForm">
                    <input type="hidden" id="edit_id" name="book_id">
                    
                    <div class="form-group">
                        <label for="edit_title">Title:</label>
                        <input type="text" id="edit_title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_author">Author:</label>
                        <input type="text" id="edit_author" name="author" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_year">Year:</label>
                        <input type="number" id="edit_year" name="year" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="edit_available" name="available" value="1">
                            Available
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Update Book</button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Book data for editing
        const books = <?php echo json_encode($books); ?>;
        
        // Edit book function
        function editBook(id) {
            const book = books.find(b => b.id === id);
            if (!book) return;
            
            document.getElementById('edit_id').value = book.id;
            document.getElementById('edit_title').value = book.title;
            document.getElementById('edit_author').value = book.author;
            document.getElementById('edit_year').value = book.year;
            document.getElementById('edit_available').checked = book.available;
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        // Close modal
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>