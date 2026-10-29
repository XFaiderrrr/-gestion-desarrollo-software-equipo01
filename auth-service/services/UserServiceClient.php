<?php

class UserServiceClient
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = getenv('USER_SERVICE_URL');

        if ($this->baseUrl === false || $this->baseUrl === '') {
            $this->baseUrl = "http://localhost/-gestion-desarrollo-software-equipo01/user-service/public/internal.php";
        }

        $this->baseUrl = rtrim($this->baseUrl, '/');
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

        return $this->request(
            "",
            $data
        );
    }

    public function deleteUser($authUserId)
    {
        $data = array(
            "auth_user_id" => (int) $authUserId
        );

        return $this->request(
            "?action=delete",
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