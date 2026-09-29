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
        $authorization = $this->getAuthorizationHeader();

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

            return false;
        }

        return $tokenData;
    }

    private function getAuthorizationHeader()
    {
        $authorization = "";

        if (function_exists("getallheaders")) {

            $headers = getallheaders();

            foreach ($headers as $key => $value) {

                if (strtolower($key) === "authorization") {

                    $authorization = $value;

                    break;
                }
            }
        }

        if (
            $authorization === "" &&
            isset($_SERVER["HTTP_AUTHORIZATION"])
        ) {

            $authorization =
                $_SERVER["HTTP_AUTHORIZATION"];
        }

        return $authorization;
    }

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