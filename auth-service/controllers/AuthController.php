<?php

class AuthController
{
    private $db;
    private $userModel;
    private $tokenModel;

    public function __construct($db)
    {
        $this->db = $db;

        $this->userModel = new User($db);
        $this->tokenModel = new Token($db);
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRO
    |--------------------------------------------------------------------------
    */

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

            if (
                $nombre === "" ||
                $email === "" ||
                $password === ""
            ) {

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

    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */

    public function login($data)
    {
        try {

            $email = isset($data["email"])
                ? trim($data["email"])
                : "";

            $password = isset($data["password"])
                ? $data["password"]
                : "";

            if (
                $email === "" ||
                $password === ""
            ) {

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

            if (!password_verify(
                $password,
                $user["password"]
            )) {

                $this->response(
                    401,
                    array(
                        "success" => false,
                        "message" => "Credenciales incorrectas"
                    )
                );

                return;
            }

            /*
             * Generar token
             */

            $token = $this->tokenModel->create(
                $user["id"]
            );

            if (!$token) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" => "No fue posible generar el token"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" => "Login exitoso",
                    "token" => $token,
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

    /*
    |--------------------------------------------------------------------------
    | VALIDAR TOKEN
    |--------------------------------------------------------------------------
    */

    public function validate()
    {
        try {

            $token = $this->getBearerToken();

            if ($token === false) {

                return;
            }

            $tokenData = $this->tokenModel
                ->findValidToken($token);

            if (!$tokenData) {

                $this->response(
                    401,
                    array(
                        "success" => false,
                        "message" => "Token inválido o expirado"
                    )
                );

                return;
            }

            $user = $this->userModel->findById(
                $tokenData["user_id"]
            );

            if (!$user) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" => "Usuario no encontrado"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" => "Token válido",
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

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */

    public function logout()
    {
        try {

            $token = $this->getBearerToken();

            if ($token === false) {

                return;
            }

            $deleted = $this->tokenModel->delete(
                $token
            );

            if (!$deleted) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" => "No fue posible cerrar la sesión"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" => "Sesión cerrada correctamente"
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER TOKEN BEARER
    |--------------------------------------------------------------------------
    */

    private function getBearerToken()
    {
        $authorization = "";

        /*
         * Apache / AppServ
         */

        if (function_exists("getallheaders")) {

            $headers = getallheaders();

            foreach ($headers as $key => $value) {

                if (strtolower($key) === "authorization") {

                    $authorization = $value;
                    break;
                }
            }
        }

        /*
         * Respaldo para servidores donde Authorization
         * no llega mediante getallheaders()
         */

        if (
            $authorization === "" &&
            isset($_SERVER["HTTP_AUTHORIZATION"])
        ) {

            $authorization =
                $_SERVER["HTTP_AUTHORIZATION"];
        }

        /*
         * Validar existencia
         */

        if ($authorization === "") {

            $this->response(
                401,
                array(
                    "success" => false,
                    "message" => "Token no proporcionado"
                )
            );

            return false;
        }

        /*
         * Validar formato Bearer
         */

        if (
            strpos(
                strtoupper($authorization),
                "BEARER "
            ) !== 0
        ) {

            $this->response(
                401,
                array(
                    "success" => false,
                    "message" => "Formato de token inválido"
                )
            );

            return false;
        }

        $token = trim(
            substr($authorization, 7)
        );

        if ($token === "") {

            $this->response(
                401,
                array(
                    "success" => false,
                    "message" => "Token vacío"
                )
            );

            return false;
        }

        return $token;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPUESTA DE ERROR DE BASE DE DATOS
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | RESPUESTA GENERAL
    |--------------------------------------------------------------------------
    */

    private function response($status, $data)
    {
        http_response_code($status);

        header(
            "Content-Type: application/json; charset=UTF-8"
        );

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );
    }
}