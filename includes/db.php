<?php
require_once 'config.php';

function read_json($file) {
    if (!file_exists($file)) {
        // Try to create the file with empty structure
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return [];
    }
    $content = file_get_contents($file);
    $data = json_decode($content, true);
    return $data ?: [];
}

function write_json($file, $data) {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $result = file_put_contents($file, $json);
    if ($result === false) {
        error_log("Failed to write to file: $file");
        return false;
    }
    return true;
}

function get_users() {
    $data = read_json(USERS_FILE);
    return $data['users'] ?? [];
}

function get_user_by_username($username) {
    $users = get_users();
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return $user;
        }
    }
    return null;
}

function get_books() {
    $data = read_json(BOOKS_FILE);
    return $data['books'] ?? [];
}

function get_book_by_id($id) {
    $books = get_books();
    foreach ($books as $book) {
        if ($book['id'] == $id) {
            return $book;
        }
    }
    return null;
}

function save_books($books) {
    return write_json(BOOKS_FILE, ['books' => $books]);
}

function add_book($book) {
    $books = get_books();
    $book['id'] = generate_id($books);
    $book['ratings'] = [];
    $book['total_ratings'] = 0;
    $book['average_rating'] = 0;
    $books[] = $book;
    return save_books($books);
}

function update_book($id, $updated_book) {
    $books = get_books();
    foreach ($books as $key => $book) {
        if ($book['id'] == $id) {
            $books[$key] = array_merge($book, $updated_book);
            return save_books($books);
        }
    }
    return false;
}

/**
 * Delete book
 */
function delete_book($id) {
    $books = get_books();
    $filtered = array_filter($books, function($book) use ($id) {
        return $book['id'] != $id;
    });
    return save_books(array_values($filtered));
}

/**
 * Get borrow history
 */
function get_borrow_history() {
    $data = read_json(HISTORY_FILE);
    return $data['history'] ?? [];
}

/**
 * Save borrow history
 */
function save_borrow_history($history) {
    return write_json(HISTORY_FILE, ['history' => $history]);
}

/**
 * Add borrow record
 */
function add_borrow_record($user_id, $username, $book_id, $book_title) {
    $history = get_borrow_history();
    $record = [
        'id' => generate_id($history),
        'user_id' => $user_id,
        'username' => $username,
        'book_id' => $book_id,
        'book_title' => $book_title,
        'borrow_date' => date('Y-m-d'),
        'return_date' => null,
        'status' => 'borrowed'
    ];
    $history[] = $record;
    return save_borrow_history($history);
}
?>