<?php

class Database
{
    private $host;
    private $dbName;
    private $username;
    private $password;
    private $debug;

    public function __construct()
    {
        $this->host = getenv("DB_HOST");

        if ($this->host === false || $this->host === "") {
            $this->host = "localhost";
        }

        $this->dbName = getenv("DB_NAME");

        if ($this->dbName === false || $this->dbName === "") {
            $this->dbName = "tienda_auth";
        }

        $this->username = getenv("DB_USER");

        if ($this->username === false || $this->username === "") {
            $this->username = "root";
        }

        $this->password = getenv("DB_PASSWORD");

        if ($this->password === false) {
            $this->password = "12345678";
        }

        $debug = getenv("DB_DEBUG");
        $this->debug = ($debug === "true");
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

            $response = array(
                "success" => false,
                "message" => "Error de conexión con la base de datos"
            );

            if ($this->debug) {

                $response["debug"] = $e->getMessage();
                $response["host"] = $this->host;
                $response["database"] = $this->dbName;
                $response["user"] = $this->username;
            }

            echo json_encode(
                $response,
                JSON_UNESCAPED_UNICODE
            );

            exit;
        }
    }
}