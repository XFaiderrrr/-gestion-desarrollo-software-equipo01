<?php

class AuthServiceClient
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = getenv('AUTH_SERVICE_INTERNAL_URL');

        if ($this->baseUrl === false || $this->baseUrl === '') {
            $this->baseUrl = "http://localhost/-gestion-desarrollo-software-equipo01/auth-service/public/internal.php";
        }

        $this->baseUrl = rtrim($this->baseUrl, '/');
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

        return $this->request("?action=update-user", $data);
    }

    public function deleteUser($authUserId)
    {
        $data = array(
            "auth_user_id" => (int) $authUserId
        );

        return $this->request("?action=delete-user", $data);
    }

    private function request($query, $data)
    {
        $url = $this->baseUrl . $query;
        $jsonData = json_encode($data);
        $secret = getenv('INTERNAL_API_SECRET');

        if (
            $jsonData === false ||
            $secret === false ||
            $secret === ''
        ) {
            return false;
        }

        $headers =
            "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n" .
            "Content-Length: " . strlen($jsonData) . "\r\n" .
            "X-Internal-Secret: " . $secret . "\r\n";

        $context = stream_context_create(
            array(
                "http" => array(
                    "method" => "POST",
                    "header" => $headers,
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
                    $httpCode = (int) $matches[1];
                }
            }
        }

        $result = json_decode($response, true);

        if (!is_array($result)) {
            return false;
        }

        // Si ya no existe en auth-service, permitir
        // que user-service continúe la eliminación local.
        if (
            $query === "?action=delete-user" &&
            $httpCode === 404
        ) {
            return true;
        }

        return isset($result["success"]) &&
            $result["success"] === true;
    }
}
