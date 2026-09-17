<?php
    class BaseDeDonnee {
        private $conn;

        public function __construct() {
            $this->conn = mysqli_connect(
                $_ENV['DB_HOST'],
                $_ENV['DB_USER'],
                $_ENV['DB_PASS'],
                $_ENV['DB_NAME']
            );
            
            
            if(!$this->conn) {
                error_log('Database connection failed: ' . mysqli_connect_error());
                exit;
            }
            
            mysqli_set_charset($this->conn, 'utf8mb4');
        }

        public function getConn(){
            return $this->conn;
        }
    }

        