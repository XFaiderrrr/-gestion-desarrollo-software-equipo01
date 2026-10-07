<?php

require_once __DIR__ . '/../services/PurchaseServiceClient.php';

$purchaseService = new PurchaseServiceClient();

$method = $_SERVER['REQUEST_METHOD'];

$purchaseAction = isset($_GET['purchase_action'])
    ? $_GET['purchase_action']
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
| CREAR COMPRA
|--------------------------------------------------------------------------
*/

if (
    $purchaseAction === 'create' &&
    $method === 'POST'
) {

    $response = $purchaseService->create(
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
| MIS COMPRAS
|--------------------------------------------------------------------------
*/

if (
    $purchaseAction === 'my-purchases' &&
    $method === 'GET'
) {

    $response = $purchaseService->getMyPurchases(
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
| TODAS LAS COMPRAS
|--------------------------------------------------------------------------
*/

if (
    $purchaseAction === 'all' &&
    $method === 'GET'
) {

    $response = $purchaseService->getAll(
        $token
    );

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
| RUTA NO ENCONTRADA
|--------------------------------------------------------------------------
*/

http_response_code(404);

echo json_encode(
    array(
        'success' => false,
        'message' => 'Ruta de compras no encontrada'
    ),
    JSON_UNESCAPED_UNICODE
);