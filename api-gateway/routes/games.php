<?php

require_once __DIR__ . '/../services/GameServiceClient.php';

$gameService = new GameServiceClient();

$method = $_SERVER['REQUEST_METHOD'];

$gameAction = isset($_GET['game_action'])
    ? $_GET['game_action']
    : '';

$authorization = '';

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {

    $authorization = $_SERVER['HTTP_AUTHORIZATION'];

} elseif (function_exists('getallheaders')) {

    $headers = getallheaders();

    foreach ($headers as $name => $value) {

        if (strtolower($name) === 'authorization') {

            $authorization = $value;

            break;
        }
    }
}

$token = '';

if (stripos($authorization, 'Bearer ') === 0) {

    $token = trim(
        substr($authorization, 7)
    );
}

$input = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($input)) {
    $input = array();
}


/*
|--------------------------------------------------------------------------
| LISTAR VIDEOJUEGOS
|--------------------------------------------------------------------------
*/

if (
    $gameAction === 'all' &&
    $method === 'GET'
) {

    $response = $gameService->getAll();

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| OBTENER VIDEOJUEGO
|--------------------------------------------------------------------------
*/

if (
    $gameAction === 'one' &&
    $method === 'GET'
) {

    $id = isset($_GET['id'])
        ? $_GET['id']
        : '';

    if ($id === '') {

        http_response_code(400);

        echo json_encode(
            array(
                'success' => false,
                'message' => 'ID de videojuego requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $response = $gameService->getById($id);

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| CREAR VIDEOJUEGO
|--------------------------------------------------------------------------
*/

if (
    $gameAction === 'create' &&
    $method === 'POST'
) {

    if ($token === '') {

        http_response_code(401);

        echo json_encode(
            array(
                'success' => false,
                'message' =>
                    'Token de autenticacion requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $response = $gameService->create(
        $input,
        $token
    );

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| ACTUALIZAR VIDEOJUEGO
|--------------------------------------------------------------------------
*/

if (
    $gameAction === 'update' &&
    $method === 'PUT'
) {

    if ($token === '') {

        http_response_code(401);

        echo json_encode(
            array(
                'success' => false,
                'message' =>
                    'Token de autenticacion requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $id = isset($_GET['id'])
        ? $_GET['id']
        : '';

    if ($id === '') {

        http_response_code(400);

        echo json_encode(
            array(
                'success' => false,
                'message' => 'ID de videojuego requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $response = $gameService->update(
        $id,
        $input,
        $token
    );

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| ELIMINAR VIDEOJUEGO
|--------------------------------------------------------------------------
*/

if (
    $gameAction === 'delete' &&
    $method === 'DELETE'
) {

    if ($token === '') {

        http_response_code(401);

        echo json_encode(
            array(
                'success' => false,
                'message' =>
                    'Token de autenticacion requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $id = isset($_GET['id'])
        ? $_GET['id']
        : '';

    if ($id === '') {

        http_response_code(400);

        echo json_encode(
            array(
                'success' => false,
                'message' => 'ID de videojuego requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $response = $gameService->delete(
        $id,
        $token
    );

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| RUTA NO ENCONTRADA
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo json_encode(
    array(
        'success' => false,
        'message' => 'Ruta de videojuegos no encontrada'
    ),
    JSON_UNESCAPED_UNICODE
);