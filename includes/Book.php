<?php


    class Book {
        private $db;

        public function __construct() {
            $this->db = new Database(BOOKS_FILE);
        }

        public function getAll() {
            $data = $this->db->read();
            return $data['books'] ?? [];
        }
    
        public function getById($id) {
            $books = $this->getAll();
            foreach ($books as $book) {
                if ($book['id'] == $id) {
                    return $book;
                }
            }
            return null;
        }

        public function add($bookData) {
            $books = $this->getAll();
            $bookData['id'] = generate_id($books);
            $bookData['ratings'] = [];
            $bookData['total_ratings'] = 0;
            $bookData['average_rating'] = 0;
            $books[] = $bookData;
            return $this->db->write(['books' => $books]);
        }
        

        public function update($id, $updatedData) {
            $books = $this->getAll();
            foreach ($books as $key => $book) {
                if ($book['id'] == $id) {
                    // Preserve ratings if not provided
                    if (!isset($updatedData['ratings'])) {
                        $updatedData['ratings'] = $book['ratings'];
                        $updatedData['total_ratings'] = $book['total_ratings'];
                        $updatedData['average_rating'] = $book['average_rating'];
                    }
                    $books[$key] = array_merge($book, $updatedData);
                    return $this->db->write(['books' => $books]);
                }
            }
            return false;
        }
        

        public function delete($id) {
            $books = $this->getAll();
            $filtered = array_filter($books, function($book) use ($id) {
                return $book['id'] != $id;
            });
            return $this->db->write(['books' => array_values($filtered)]);
        }
        

        public function addRating($id, $rating) {
            $book = $this->getById($id);
            if (!$book) {
                return false;
            }
            
            $book['ratings'][] = $rating;
            $book['total_ratings'] = count($book['ratings']);
            $book['average_rating'] = array_sum($book['ratings']) / $book['total_ratings'];
            
            return $this->update($id, $book);
        }

        public function search($query) {
            $books = $this->getAll();
            if (empty($query)) {
                return $books;
            }
            
            return array_filter($books, function($book) use ($query) {
                return stripos($book['title'], $query) !== false || 
                    stripos($book['author'], $query) !== false;
            });
        }
    }
?>