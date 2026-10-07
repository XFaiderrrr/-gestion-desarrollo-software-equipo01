<?php

class AuthServiceClient
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl =
            "http://localhost/" .
            "-gestion-desarrollo-software-equipo01/" .
            "auth-service/public/internal.php";
    }

    public function updateUser(
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

        return $this->request(
            "?action=update-user",
            $data
        );
    }

    public function deleteUser($authUserId)
    {
        $data = array(
            "auth_user_id" => (int) $authUserId
        );

        return $this->request(
            "?action=delete-user",
            $data
        );
    }

    private function request($query, $data)
    {
        $url = $this->baseUrl . $query;

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
            $url,
            false,
            $context
        );

        if ($response === false) {
            return false;
        }

        /*
         * Obtener código HTTP de la respuesta.
         */
        $httpCode = 0;

        if (
            isset($http_response_header) &&
            is_array($http_response_header)
        ) {

            foreach ($http_response_header as $header) {

                if (
                    preg_match(
                        '/^HTTP\/\d+\.\d+\s+(\d+)/',
                        $header,
                        $matches
                    )
                ) {

                    $httpCode =
                        (int) $matches[1];
                }
            }
        }

        $result = json_decode(
            $response,
            true
        );

        if (!is_array($result)) {
            return false;
        }

        /*
         * Eliminación:
         *
         * 200 = eliminado correctamente.
         * 404 = ya no existe en auth-service.
         *
         * En ambos casos user-service puede
         * continuar eliminando su copia local.
         */
        if (
            $query === "?action=delete-user" &&
            $httpCode === 404
        ) {

            return true;
        }

        if (
            isset($result["success"]) &&
            $result["success"] === true
        ) {

            return true;
        }

        return false;
    }
}