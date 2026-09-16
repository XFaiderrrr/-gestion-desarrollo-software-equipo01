<?php

class AuthMiddleware
{
    private $tokenModel;

    public function __construct($db)
    {
        $this->tokenModel = new Token($db);
    }

    public function authenticate()
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
            return false;
        }

        if (strpos($authorization, "Bearer ") !== 0) {
            $this->response(401, [
                "success" => false,
                "message" => "Formato de token inválido"
            ]);
            return false;
        }

        $token = trim(substr($authorization, 7));

        if ($token === "") {
            $this->response(401, [
                "success" => false,
                "message" => "Token vacío"
            ]);
            return false;
        }

        $tokenData = $this->tokenModel->findValidToken($token);

        if (!$tokenData) {
            $this->response(401, [
                "success" => false,
                "message" => "Token inválido o expirado"
            ]);
            return false;
        }

        return $tokenData;
    }

    private function response($status, $data)
    {
        http_response_code($status);

        header("Content-Type: application/json; charset=UTF-8");

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );
    }
}