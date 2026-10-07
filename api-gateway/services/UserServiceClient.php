<?php

class UserServiceClient
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl =
            'http://localhost/-gestion-desarrollo-software-equipo01/user-service/public/';
    }

    public function getAll($token)
    {
        return $this->request(
            'GET',
            $this->baseUrl,
            null,
            $token
        );
    }

    public function getById($id, $token)
    {
        return $this->request(
            'GET',
            $this->baseUrl . '?id=' . urlencode($id),
            null,
            $token
        );
    }

    public function update($id, $data, $token)
    {
        return $this->request(
            'PUT',
            $this->baseUrl . '?id=' . urlencode($id),
            $data,
            $token
        );
    }

    public function delete($id, $token)
    {
        return $this->request(
            'DELETE',
            $this->baseUrl . '?id=' . urlencode($id),
            null,
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
                    'No se pudo comunicar con user-service'
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
                    'Respuesta invalida de user-service'
            );
        }

        return $decoded;
    }
}