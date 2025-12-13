<?php

    class User {
        private $db;
        
        public function __construct() {
            $this->db = new Database(USERS_FILE);
        }
        

        public function getAll() {
            $data = $this->db->read();
            return $data['users'] ?? [];
        }

        public function getByUsername($username) {
            $users = $this->getAll();
            foreach ($users as $user) {
                if ($user['username'] === $username) {
                    return $user;
                }
            }
            return null;
        }
        

        public function verify($username, $password) {
            $user = $this->getByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                return $user;
            }
            return null;
        }
    }
?>