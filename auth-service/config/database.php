<?php

class Database
{
    private $host = "localhost";
    private $dbName = "tienda_auth";
    private $username = "root";
    private $password = "12345678";

    public function connect()
    {
        try {
            $connection = new PDO(
                "mysql:host=" . $this->host .
                ";dbname=" . $this->dbName .
                ";charset=utf8mb4",
                $this->username,
                $this->password
            );

            $connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $connection->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return $connection;

        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                "success" => false,
                "message" => "Error de conexión con la base de datos"
            ]);

            exit;
        }
    }
}