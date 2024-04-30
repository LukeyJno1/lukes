<?php
//db_connect.php

class Database
{
    private $conn;

    public function __construct($host, $db_name, $username, $password)
    {
        try {
            $dsn = "mysql:host={$host};dbname={$db_name}";
            $this->conn = new \PDO($dsn, $username, $password);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            error_log("Database connection established successfully.");
        } catch (\PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            error_log("DSN: " . $dsn);
            error_log("Username: " . $username);
            // You might want to handle the error further, such as displaying a generic error message to the user
            throw $exception; // Re-throw the exception to be caught in the calling code
        }
    }

    public function getConnection()
    {
        if ($this->conn === null) {
            error_log("Database connection is not established.");
            throw new \Exception("Database connection is not established.");
        }
        error_log("Returning PDO connection object.");
        return $this->conn;
    }

    public function inTransaction()
    {
        return $this->conn->inTransaction();
    }
}

// Database configuration
$host = 'localhost';
$db_name = 'lukejear_test';
$username = 'root';
$password = 'AlfieJohn@2017';

try {
    // Create an instance of the Database class
    $db = new Database($host, $db_name, $username, $password);
    error_log("db_connect.php loaded successfully.");
} catch (PDOException $exception) {
    error_log("Failed to connect to the database: " . $exception->getMessage());
    // Handle the error gracefully, such as displaying a user-friendly message
    die("We apologize, but we are currently experiencing technical difficulties. Please try again later.");
}