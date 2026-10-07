<?php

class PurchaseServiceClient
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl =
            'http://localhost/-gestion-desarrollo-software-equipo01/purchase-service/public/';
    }

    public function create($data, $token)
    {
        return $this->request(
            'POST',
            $this->baseUrl . '?action=create',
            $data,
            $token
        );
    }

    public function getMyPurchases($token)
    {
        return $this->request(
            'GET',
            $this->baseUrl . '?action=my-purchases',
            null,
            $token
        );
    }

    public function getAll($token)
    {
        return $this->request(
            'GET',
            $this->baseUrl . '?action=all',
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
                    'No se pudo comunicar con purchase-service'
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
                    'Respuesta invalida de purchase-service'
            );
        }

        return $decoded;
    }
}