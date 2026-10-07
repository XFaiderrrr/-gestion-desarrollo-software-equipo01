<?php

class GameServiceClient
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl =
            'http://localhost/-gestion-desarrollo-software-equipo01/game-service/public/';
    }

    public function getGame($gameId)
    {
        $url = $this->baseUrl . '?id=' . urlencode($gameId);

        $response = $this->request(
            'GET',
            $url,
            null
        );

        if (!$response['success']) {
            return array(
                'success' => false,
                'message' => 'No se pudo consultar el videojuego'
            );
        }

        if (
            !isset($response['data']) &&
            !isset($response['game'])
        ) {
            return array(
                'success' => false,
                'message' => 'Respuesta invalida de game-service'
            );
        }

        if (isset($response['game'])) {
            return array(
                'success' => true,
                'game' => $response['game']
            );
        }

        return array(
            'success' => true,
            'game' => $response['data']
        );
    }

    public function decreaseStock($gameId, $cantidad)
    {
        $url =
            $this->baseUrl .
            'internal.php?action=decrease-stock';

        $response = $this->request(
            'POST',
            $url,
            array(
                'game_id' => (int) $gameId,
                'cantidad' => (int) $cantidad
            )
        );

        if (
            !isset($response['success']) ||
            $response['success'] !== true
        ) {
            return array(
                'success' => false,
                'message' =>
                    isset($response['message'])
                        ? $response['message']
                        : 'No fue posible actualizar el stock'
            );
        }

        return array(
            'success' => true,
            'message' =>
                isset($response['message'])
                    ? $response['message']
                    : 'Stock actualizado correctamente',
            'game' =>
                isset($response['game'])
                    ? $response['game']
                    : null
        );
    }

    private function request($method, $url, $data = null)
    {
        $options = array(
            'http' => array(
                'method' => $method,
                'header' =>
                    "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n",
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
                    'No se pudo comunicar con game-service'
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
                    'Respuesta invalida de game-service'
            );
        }

        return $decoded;
    }
}