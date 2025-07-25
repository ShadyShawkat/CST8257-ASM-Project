<?php

// PHP file containing the database configuration

// Use singleton database connection for real world scenario
// REFERENCES: 
// https://dev.to/websilvercraft/implementing-singletons-in-php-using-classes-or-functions-23mh
// https://medium.com/@bandarans2000/singleton-design-pattern-in-php-a-practical-guide-with-database-connection-example-b35adfade4ec



define('APP_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'CST8257');

$db = parse_ini_file('database.ini');

class Database
{
    // private $host = $db['host'];
    // private $dbname
    // $dbname = $db['dbname'];
    // $user = $db['username'];
    // $pass = $db['password'];
    private $host;
    private $dbName;
    private $user;
    private $pass;

    // Store the single instance
    private static ?Database $instance = null;
    
    // Database connection object
    private mysqli $connection;

    // Private constructor to prevent direct instantiation
    private function __construct()
    {
        $this->connection = new mysqli(
            APP_SERVER,
            DB_USER,
            DB_PASS,
            DB_NAME,
            // '3308'
        );

        if ($this -> connection -> connect_error) {
            die("Connection failed: " . $this -> connection -> connect_error);
        }
    }

    // The method to get the singleton instance
    public static function getInstance(): ?Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    // Get the database connection
    public function getConnection(): mysqli
    {
        return $this -> connection;
    }
}