<?php

class AdminMiddleware
{
    private $authUrl;

    public function __construct()
    {
        $this->authUrl =
            "http://localhost/-gestion-desarrollo-software-equipo01/auth-service/public/?action=validate";
    }

    public function authenticateAdmin()
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

        $context = stream_context_create(
            array(
                "http" => array(
                    "method" => "GET",
                    "header" =>
                        "Authorization: " . $authorization . "\r\n" .
                        "Content-Type: application/json\r\n",
                    "ignore_errors" => true,
                    "timeout" => 5
                )
            )
        );

        $result = @file_get_contents(
            $this->authUrl,
            false,
            $context
        );

        if ($result === false) {

            $this->response(
                503,
                array(
                    "success" => false,
                    "message" => "No fue posible comunicarse con auth-service"
                )
            );

            return false;
        }

        $data = json_decode(
            $result,
            true
        );

        if (
            !is_array($data) ||
            !isset($data["success"]) ||
            $data["success"] !== true
        ) {

            $this->response(
                401,
                array(
                    "success" => false,
                    "message" => "Token inválido o expirado"
                )
            );

            return false;
        }

        if (
            !isset($data["user"]) ||
            !isset($data["user"]["rol"])
        ) {

            $this->response(
                401,
                array(
                    "success" => false,
                    "message" => "No fue posible identificar el usuario"
                )
            );

            return false;
        }

        if ($data["user"]["rol"] !== "admin") {

            $this->response(
                403,
                array(
                    "success" => false,
                    "message" => "Acceso denegado. Se requiere rol admin"
                )
            );

            return false;
        }

        return $data["user"];
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