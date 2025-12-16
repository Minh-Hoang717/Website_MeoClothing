<?php
/**
 * Database Configuration
 * Kết nối với meo_clothingstore database
 */

class Database {
    private $host = "127.0.0.1:3306";
    private $db_name = "meo_clothingstore";
    private $username = "root";
    private $password = "";
    private $conn;

    /**
     * Kết nối database
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                )
            );
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }

    /**
     * Đóng kết nối
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
