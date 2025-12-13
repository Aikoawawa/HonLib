<?php

    class Auth {
        private $userModel;
        

        public function __construct() {
            $this->userModel = new User();
        }
        

        public function login($username, $password) {
            $user = $this->userModel->verify($username, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
                return true;
            }
            return false;
        }

        public function logout() {
            session_unset();
            session_destroy();
        }
        

        public function isLoggedIn() {
            return is_logged_in();
        }
        

        public function isAdmin() {
            return is_admin();
        }
        

        public function requireLogin() {
            if (!$this->isLoggedIn()) {
                redirect('index.php');
            }
        }
        
        public function requireAdmin() {
            $this->requireLogin();
            if (!$this->isAdmin()) {
                redirect('dashboard.php');
            }
        }
    }
?>