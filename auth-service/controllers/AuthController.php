<?php

class AuthController
{
    private $db;
    private $userModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->userModel = new User($db);
    }

    public function register($data)
    {
        try {

            $nombre = isset($data["nombre"])
                ? trim($data["nombre"])
                : "";

            $email = isset($data["email"])
                ? trim($data["email"])
                : "";

            $password = isset($data["password"])
                ? $data["password"]
                : "";

            if ($nombre === "" || $email === "" || $password === "") {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "Todos los campos son obligatorios"
                    )
                );

                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El correo electrónico no es válido"
                    )
                );

                return;
            }

            if (strlen($password) < 6) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "La contraseña debe tener al menos 6 caracteres"
                    )
                );

                return;
            }

            if ($this->userModel->emailExists($email)) {

                $this->response(
                    409,
                    array(
                        "success" => false,
                        "message" => "El correo ya está registrado"
                    )
                );

                return;
            }

            $created = $this->userModel->create(
                $nombre,
                $email,
                $password
            );

            if (!$created) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" => "No fue posible registrar al usuario"
                    )
                );

                return;
            }

            $this->response(
                201,
                array(
                    "success" => true,
                    "message" => "Usuario registrado correctamente"
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    public function login($data)
    {
        try {

            $email = isset($data["email"])
                ? trim($data["email"])
                : "";

            $password = isset($data["password"])
                ? $data["password"]
                : "";

            if ($email === "" || $password === "") {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "Correo y contraseña son obligatorios"
                    )
                );

                return;
            }

            $user = $this->userModel->findByEmail($email);

            if (!$user) {

                $this->response(
                    401,
                    array(
                        "success" => false,
                        "message" => "Credenciales incorrectas"
                    )
                );

                return;
            }

            if (!password_verify($password, $user["password"])) {

                $this->response(
                    401,
                    array(
                        "success" => false,
                        "message" => "Credenciales incorrectas"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" => "Login exitoso",
                    "user" => array(
                        "id" => $user["id"],
                        "nombre" => $user["nombre"],
                        "email" => $user["email"],
                        "rol" => $user["rol"]
                    )
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    private function databaseError($e)
    {
        http_response_code(500);

        $response = array(
            "success" => false,
            "message" => "Error al acceder a la base de datos"
        );

        if (getenv("DB_DEBUG") === "true") {

            $response["debug"] = $e->getMessage();
        }

        echo json_encode(
            $response,
            JSON_UNESCAPED_UNICODE
        );
    }

    private function response($status, $data)
    {
        http_response_code($status);

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );
    }
}