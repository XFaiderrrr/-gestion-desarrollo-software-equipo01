<?php

class Database
{
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $connection;

    public function __construct()
    {
        $this->host = getenv('PURCHASE_DB_HOST') ?: 'localhost';
        $this->dbName = getenv('PURCHASE_DB_NAME') ?: 'tienda_purchases';
        $this->username = getenv('PURCHASE_DB_USER') ?: 'root';
        $this->password = getenv('PURCHASE_DB_PASSWORD') ?: '12345678';
    }

    public function connect()
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4";

            $this->connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                )
            );

            return $this->connection;

        } catch (PDOException $e) {
            throw new Exception(
                'Error de conexión con la base de datos'
            );
        }
    }
}