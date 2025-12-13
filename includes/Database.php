<?php
    class Database {
        private $file;
        

        public function __construct($file) {
            $this->file = $file;
        }
        
 
        public function read() {
            if (!file_exists($this->file)) {
                $this->createDirectory();
                return [];
            }
            $content = file_get_contents($this->file);
            $data = json_decode($content, true);
            return $data ?: [];
        }
        

        public function write($data) {
            $this->createDirectory();
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $result = file_put_contents($this->file, $json);
            if ($result === false) {
                error_log("Failed to write to file: {$this->file}");
                return false;
            }
            return true;
        }

        private function createDirectory() {
            $dir = dirname($this->file);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
    }
?>