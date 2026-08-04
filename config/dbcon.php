<?php

class DBCon {
    private static $instance = null;

    // Prevent instantiation from outside the class
    private function __construct() {}

    // Prevent cloning of the instance
    private function __clone() {}

    /* 
    * 1. Get the connection instance
    * $db = Database::getConnection();
    * 2. Now use $db just like you would use a $pdo variable
    * $stmt = $db->prepare("SELECT * FROM wallets");
    * $stmt->execute();
    * $results = $stmt->fetchAll(); 
    *
    * $stmt = $db->prepare("SELECT balance FROM wallets WHERE id = ?"");
    * $stmt->execute([$walletId]);
    * $results = $stmt->fetchColumn();
    */
    public static function getConnection() {
        if (self::$instance === null) {
            $envPath = __DIR__ . "/../.env";
            
            // Ensure the file exists before parsing
            if (!file_exists($envPath)) {
                throw new Exception("Environment file not found.");
            }

            // Load env file
            $ENV = parse_ini_file($envPath); 

            // Use the variables from the parsed $ENV array
            $host = $ENV['DB_HOST'] ?? '';
            $db   = $ENV['DB_NAME'] ?? '';
            $user = $ENV['DB_USER'] ?? '';
            $pass = $ENV['DB_PASS'] ?? '';

            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Log the actual error, but show a generic message to the user
                error_log("Connection failed: " . $e->getMessage());
                throw new Exception("Database connection error.");
            }
        }
        return self::$instance;
    }
    
}