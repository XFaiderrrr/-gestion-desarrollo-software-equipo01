<?php

require_once __DIR__ . '/../services/AuthServiceClient.php';

$authService = new AuthServiceClient();

$method = $_SERVER['REQUEST_METHOD'];

$authAction = isset($_GET['auth_action'])
    ? $_GET['auth_action']
    : '';

$input = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($input)) {
    $input = array();
}


/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/

if (
    $authAction === 'register' &&
    $method === 'POST'
) {

    $response = $authService->register($input);

    if (
        isset($response['success']) &&
        $response['success'] === false
    ) {
        http_response_code(400);
    }

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if (
    $authAction === 'login' &&
    $method === 'POST'
) {

    $response = $authService->login($input);

    if (
        isset($response['success']) &&
        $response['success'] === false
    ) {
        http_response_code(401);
    }

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDATE
|--------------------------------------------------------------------------
*/

if (
    $authAction === 'validate' &&
    $method === 'GET'
) {

    $authorization = isset(
        $_SERVER['HTTP_AUTHORIZATION']
    )
        ? $_SERVER['HTTP_AUTHORIZATION']
        : '';

    if (
        stripos($authorization, 'Bearer ') !== 0
    ) {

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

    $token = trim(
        substr($authorization, 7)
    );

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

    $response = $authService->validate($token);

    if (
        isset($response['success']) &&
        $response['success'] === false
    ) {
        http_response_code(401);
    }

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

if (
    $authAction === 'logout' &&
    $method === 'POST'
) {

    $authorization = isset(
        $_SERVER['HTTP_AUTHORIZATION']
    )
        ? $_SERVER['HTTP_AUTHORIZATION']
        : '';

    if (
        stripos($authorization, 'Bearer ') !== 0
    ) {

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

    $token = trim(
        substr($authorization, 7)
    );

    $response = $authService->logout($token);

    if (
        isset($response['success']) &&
        $response['success'] === false
    ) {
        http_response_code(401);
    }

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
        'message' => 'Ruta de autenticacion no encontrada'
    ),
    JSON_UNESCAPED_UNICODE
);