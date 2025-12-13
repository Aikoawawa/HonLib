<?php

    class BorrowHistory {
        private $db;
        

        public function __construct() {
            $this->db = new Database(HISTORY_FILE);
        }
        

        public function getAll() {
            $data = $this->db->read();
            return $data['history'] ?? [];
        }
        

        public function getByUserId($userId) {
            $history = $this->getAll();
            return array_filter($history, function($record) use ($userId) {
                return $record['user_id'] == $userId;
            });
        }
        

        public function addBorrow($userId, $username, $bookId, $bookTitle) {
            $history = $this->getAll();
            $record = [
                'id' => generate_id($history),
                'user_id' => $userId,
                'username' => $username,
                'book_id' => $bookId,
                'book_title' => $bookTitle,
                'borrow_date' => date('Y-m-d'),
                'return_date' => null,
                'status' => 'borrowed'
            ];
            $history[] = $record;
            return $this->db->write(['history' => $history]);
        }
        

        public function returnBook($userId, $bookId) {
            $history = $this->getAll();
            foreach ($history as $key => $record) {
                if ($record['book_id'] == $bookId && 
                    $record['user_id'] == $userId && 
                    $record['status'] === 'borrowed') {
                    $history[$key]['return_date'] = date('Y-m-d');
                    $history[$key]['status'] = 'returned';
                    return $this->db->write(['history' => $history]);
                }
            }
            return false;
        }
        
        public function userHasBook($userId, $bookId) {
            $history = $this->getAll();
            foreach ($history as $record) {
                if ($record['book_id'] == $bookId && 
                    $record['user_id'] == $userId && 
                    $record['status'] === 'borrowed') {
                    return true;
                }
            }
            return false;
        }
    }
    ?>