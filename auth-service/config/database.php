<?php

class Database
{
    private $host;
    private $dbName;
    private $username;
    private $password;

    public function __construct()
    {
        /*
         * En AppServ local:
         *   DB_HOST     no existe  -> localhost
         *   DB_NAME     no existe  -> tienda_auth
         *   DB_USER     no existe  -> root
         *   DB_PASSWORD no existe  -> ""
         *
         * En GitHub Actions:
         *   Estas variables serán proporcionadas por ci.yml
         */

        $this->host = getenv("DB_HOST") ?: "localhost";
        $this->dbName = getenv("DB_NAME") ?: "tienda_auth";
        $this->username = getenv("DB_USER") ?: "root";
        $this->password = getenv("DB_PASSWORD") ?: "";
    }

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