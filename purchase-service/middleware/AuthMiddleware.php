<?php

class AuthMiddleware
{
    private $authServiceUrl;

    public function __construct()
    {
        $this->authServiceUrl =
            'http://localhost/-gestion-desarrollo-software-equipo01/auth-service/public/';
    }

    public function authenticate()
    {
        $headers = $this->getAuthorizationHeader();

        if ($headers === null) {
            return array(
                'success' => false,
                'status' => 401,
                'message' => 'Token de autenticacion requerido'
            );
        }

        if (stripos($headers, 'Bearer ') !== 0) {
            return array(
                'success' => false,
                'status' => 401,
                'message' => 'Formato de token invalido'
            );
        }

        $token = trim(substr($headers, 7));

        if ($token === '') {
            return array(
                'success' => false,
                'status' => 401,
                'message' => 'Token de autenticacion requerido'
            );
        }

        $response = $this->validateToken($token);

        if (!$response['success']) {
            return array(
                'success' => false,
                'status' => 401,
                'message' => $response['message']
            );
        }

        return array(
            'success' => true,
            'status' => 200,
            'user' => $response['user']
        );
    }

    private function validateToken($token)
    {
        $url = $this->authServiceUrl . '?action=validate';

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

        if (
            !isset($decoded['success']) ||
            $decoded['success'] !== true
        ) {
            return array(
                'success' => false,
                'message' =>
                    isset($decoded['message'])
                        ? $decoded['message']
                        : 'Token invalido'
            );
        }

        if (!isset($decoded['user'])) {
            return array(
                'success' => false,
                'message' =>
                    'Auth-service no devolvio informacion del usuario'
            );
        }

        return array(
            'success' => true,
            'user' => $decoded['user']
        );
    }

    private function getAuthorizationHeader()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            foreach ($headers as $name => $value) {
                if (strtolower($name) === 'authorization') {
                    return $value;
                }
            }
        }

        return null;
    }
}