<?php

class UserServiceClient
{
    private $url;

    public function __construct()
    {
        $this->url =
            "http://localhost/" .
            "-gestion-desarrollo-software-equipo01/" .
            "user-service/public/internal.php";
    }

    public function syncUser(
        $authUserId,
        $nombre,
        $email,
        $rol
    ) {
        $data = array(
            "auth_user_id" => (int) $authUserId,
            "nombre" => $nombre,
            "email" => $email,
            "rol" => $rol
        );

        $jsonData = json_encode($data);

        $context = stream_context_create(
            array(
                "http" => array(
                    "method" => "POST",

                    "header" =>
                        "Content-Type: application/json\r\n" .
                        "Content-Length: " .
                        strlen($jsonData) .
                        "\r\n",

                    "content" => $jsonData,

                    "ignore_errors" => true,

                    "timeout" => 5
                )
            )
        );

        $response = @file_get_contents(
            $this->url,
            false,
            $context
        );

        if ($response === false) {
            return false;
        }

        $result = json_decode(
            $response,
            true
        );

        if (
            !is_array($result) ||
            !isset($result["success"])
        ) {
            return false;
        }

        return $result["success"] === true;
    }
}