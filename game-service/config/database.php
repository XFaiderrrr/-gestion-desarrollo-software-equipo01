<?php

class Database
{
    private $host;
    private $dbName;
    private $username;
    private $password;

    public function __construct()
    {
        $this->host = getenv("GAME_DB_HOST");

        if ($this->host === false || $this->host === "") {
            $this->host = "localhost";
        }

        $this->dbName = getenv("GAME_DB_NAME");

        if ($this->dbName === false || $this->dbName === "") {
            $this->dbName = "tienda_games";
        }

        $this->username = getenv("GAME_DB_USER");

        if ($this->username === false || $this->username === "") {
            $this->username = "root";
        }

        $this->password = getenv("GAME_DB_PASSWORD");

        if ($this->password === false) {
            $this->password = "12345678";
        }
    }

    public function connect()
    {
        try {

            $dsn =
                "mysql:host=" . $this->host .
                ";dbname=" . $this->dbName .
                ";charset=utf8mb4";

            $connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                )
            );

            return $connection;

        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode(
                array(
                    "success" => false,
                    "message" => "Error de conexión con la base de datos"
                ),
                JSON_UNESCAPED_UNICODE
            );

            exit;
        }
    }
}