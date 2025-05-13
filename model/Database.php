<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $config = include('../config/config.php');
        $dbConfig = $config['db'];
        
        try {
            $this->conn = new PDO(
                "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", 
                $dbConfig['username'], 
                $dbConfig['password']
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}
?>