<?php

require_once __DIR__ . '/../services/UserServiceClient.php';

$userService = new UserServiceClient();

$method = $_SERVER['REQUEST_METHOD'];

$userAction = isset($_GET['user_action'])
    ? $_GET['user_action']
    : '';

$authorization = '';

if (isset($_SERVER['HTTP_AUTHORIZATION'])) {

    $authorization =
        $_SERVER['HTTP_AUTHORIZATION'];

}

if (
    $authorization === '' &&
    isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])
) {

    $authorization =
        $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

}

if (
    $authorization === '' &&
    function_exists('getallheaders')
) {

    $headers = getallheaders();

    foreach ($headers as $name => $value) {

        if (
            strtolower($name) ===
            'authorization'
        ) {

            $authorization = $value;

            break;
        }
    }
}

if (stripos($authorization, 'Bearer ') !== 0) {

    http_response_code(401);

    echo json_encode(
        array(
            'success' => false,
            'message' => 'Token de autenticacion requerido'
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
            'message' => 'Token de autenticacion requerido'
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
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
| GET ALL USERS
|--------------------------------------------------------------------------
*/

if (
    $userAction === 'all' &&
    $method === 'GET'
) {

    $response = $userService->getAll($token);

    if (
        isset($response['success']) &&
        $response['success'] === false
    ) {
        http_response_code(403);
    }

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| GET USER BY ID
|--------------------------------------------------------------------------
*/

if (
    $userAction === 'one' &&
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
                'message' => 'ID de usuario requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $response = $userService->getById(
        $id,
        $token
    );

    if (
        isset($response['success']) &&
        $response['success'] === false
    ) {
        http_response_code(404);
    }

    echo json_encode(
        $response,
        JSON_UNESCAPED_UNICODE
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| UPDATE USER
|--------------------------------------------------------------------------
*/

if (
    $userAction === 'update' &&
    $method === 'PUT'
) {

    $id = isset($_GET['id'])
        ? $_GET['id']
        : '';

    if ($id === '') {

        http_response_code(400);

        echo json_encode(
            array(
                'success' => false,
                'message' => 'ID de usuario requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $response = $userService->update(
        $id,
        $input,
        $token
    );

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
| DELETE USER
|--------------------------------------------------------------------------
*/

if (
    $userAction === 'delete' &&
    $method === 'DELETE'
) {

    $id = isset($_GET['id'])
        ? $_GET['id']
        : '';

    if ($id === '') {

        http_response_code(400);

        echo json_encode(
            array(
                'success' => false,
                'message' => 'ID de usuario requerido'
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $response = $userService->delete(
        $id,
        $token
    );

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
| ROUTE NOT FOUND
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo json_encode(
    array(
        'success' => false,
        'message' => 'Ruta de usuarios no encontrada'
    ),
    JSON_UNESCAPED_UNICODE
);