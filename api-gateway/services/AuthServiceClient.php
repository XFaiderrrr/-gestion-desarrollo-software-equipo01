<?php

class AuthServiceClient
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = getenv('AUTH_SERVICE_URL');

        if ($this->baseUrl === false || $this->baseUrl === '') {
            $this->baseUrl = 'http://localhost/-gestion-desarrollo-software-equipo01/auth-service/public/';
        }

        $this->baseUrl = rtrim($this->baseUrl, '/') . '/';
    }

    public function register($data)
    {
        return $this->request(
            'POST',
            $this->baseUrl . '?action=register',
            $data
        );
    }

    public function login($data)
    {
        return $this->request(
            'POST',
            $this->baseUrl . '?action=login',
            $data
        );
    }

    public function validate($token)
    {
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' =>
                    "Accept: application/json\r\n" .
                    "Authorization: Bearer " . $token . "\r\n",
                'ignore_errors' => true
            )
        );

        $context = stream_context_create($options);

        $result = @file_get_contents(
            $this->baseUrl . '?action=validate',
            false,
            $context
        );

        if ($result === false) {
            return array(
                'success' => false,
                'message' =>
                    'No se pudo comunicar con auth-service'
            );
        }

        $decoded = json_decode(
            $result,
            true
        );

        if (!is_array($decoded)) {
            return array(
                'success' => false,
                'message' =>
                    'Respuesta invalida de auth-service'
            );
        }

        return $decoded;
    }

    public function logout($token)
    {
        return $this->request(
            'POST',
            $this->baseUrl . '?action=logout',
            array(
                'token' => $token
            ),
            $token
        );
    }

    private function request(
        $method,
        $url,
        $data = null,
        $token = null
    ) {
        $header =
            "Content-Type: application/json\r\n" .
            "Accept: application/json\r\n";

        if ($token !== null) {
            $header .=
                "Authorization: Bearer " .
                $token .
                "\r\n";
        }

        $options = array(
            'http' => array(
                'method' => $method,
                'header' => $header,
                'ignore_errors' => true
            )
        );

        if ($data !== null) {
            $options['http']['content'] =
                json_encode($data);
        }

        $context = stream_context_create($options);

        $result = @file_get_contents(
            $url,
            false,
            $context
        );

        if ($result === false) {
            return array(
                'success' => false,
                'message' =>
                    'No se pudo comunicar con auth-service'
            );
        }

        $decoded = json_decode(
            $result,
            true
        );

        if (!is_array($decoded)) {
            return array(
                'success' => false,
                'message' =>
                    'Respuesta invalida de auth-service'
            );
        }

        return $decoded;
    }
}