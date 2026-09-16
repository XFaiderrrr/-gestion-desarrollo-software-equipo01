```php
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

    public function register($data)
    {
        $nombre = isset($data["nombre"]) ? trim($data["nombre"]) : "";
        $email = isset($data["email"]) ? trim($data["email"]) : "";
        $password = isset($data["password"]) ? $data["password"] : "";

        if ($nombre === "" || $email === "" || $password === "") {
            $this->response(400, [
                "success" => false,
                "message" => "Todos los campos son obligatorios"
            ]);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->response(400, [
                "success" => false,
                "message" => "El correo electrónico no es válido"
            ]);
            return;
        }

        if (strlen($password) < 6) {
            $this->response(400, [
                "success" => false,
                "message" => "La contraseña debe tener al menos 6 caracteres"
            ]);
            return;
        }

        if ($this->userModel->emailExists($email)) {
            $this->response(409, [
                "success" => false,
                "message" => "El correo ya está registrado"
            ]);
            return;
        }

        $created = $this->userModel->create(
            $nombre,
            $email,
            $password
        );

        if (!$created) {
            $this->response(500, [
                "success" => false,
                "message" => "No fue posible registrar al usuario"
            ]);
            return;
        }

        $this->response(201, [
            "success" => true,
            "message" => "Usuario registrado correctamente"
        ]);
    }

    public function login($data)
    {
        $email = isset($data["email"]) ? trim($data["email"]) : "";
        $password = isset($data["password"]) ? $data["password"] : "";

        if ($email === "" || $password === "") {
            $this->response(400, [
                "success" => false,
                "message" => "Correo y contraseña son obligatorios"
            ]);
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->response(401, [
                "success" => false,
                "message" => "Credenciales incorrectas"
            ]);
            return;
        }

        if (!password_verify($password, $user["password"])) {
            $this->response(401, [
                "success" => false,
                "message" => "Credenciales incorrectas"
            ]);
            return;
        }

        /*
         * Login correcto.
         * Generamos un token para identificar al usuario
         * durante las siguientes peticiones.
         */
        $token = $this->tokenModel->create($user["id"]);

        if (!$token) {
            $this->response(500, [
                "success" => false,
                "message" => "No fue posible generar el token"
            ]);
            return;
        }

        $this->response(200, [
            "success" => true,
            "message" => "Login exitoso",
            "token" => $token,
            "user" => [
                "id" => $user["id"],
                "nombre" => $user["nombre"],
                "email" => $user["email"],
                "rol" => $user["rol"]
            ]
        ]);
    }

    private function response($status, $data)
    {
        http_response_code($status);

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );
    }
    public function logout()
{
    $headers = getallheaders();

    $authorization = isset($headers["Authorization"])
        ? $headers["Authorization"]
        : "";

    if ($authorization === "") {
        $this->response(401, [
            "success" => false,
            "message" => "Token no proporcionado"
        ]);
        return;
    }

    if (strpos($authorization, "Bearer ") !== 0) {
        $this->response(401, [
            "success" => false,
            "message" => "Formato de token inválido"
        ]);
        return;
    }

    $token = trim(substr($authorization, 7));

    if ($token === "") {
        $this->response(401, [
            "success" => false,
            "message" => "Token vacío"
        ]);
        return;
    }

    $tokenData = $this->tokenModel->findValidToken($token);

    if (!$tokenData) {
        $this->response(401, [
            "success" => false,
            "message" => "Token inválido o expirado"
        ]);
        return;
    }

    $deleted = $this->tokenModel->delete($token);

    if (!$deleted) {
        $this->response(500, [
            "success" => false,
            "message" => "No fue posible cerrar la sesión"
        ]);
        return;
    }

    $this->response(200, [
        "success" => true,
        "message" => "Logout exitoso"
    ]);
}
}
