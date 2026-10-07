<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Purchase.php';
require_once __DIR__ . '/../services/GameServiceClient.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/PurchaseController.php';

header('Content-Type: application/json; charset=utf-8');

$database = new Database();
$db = $database->connect();

$gameService = new GameServiceClient();

$controller = new PurchaseController(
    $db,
    $gameService
);

$authMiddleware = new AuthMiddleware();

$action = isset($_GET['action'])
    ? $_GET['action']
    : '';

$method = $_SERVER['REQUEST_METHOD'];

if ($action === 'create' && $method === 'POST') {

    $auth = $authMiddleware->authenticate();

    if (!$auth['success']) {
        http_response_code($auth['status']);

        echo json_encode(
            array(
                'success' => false,
                'message' => $auth['message']
            )
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

    $response = $controller->create(
        $auth['user'],
        $input
    );

    http_response_code($response['status']);

    unset($response['status']);

    echo json_encode($response);

    exit;
}

if ($action === 'my-purchases' && $method === 'GET') {

    $auth = $authMiddleware->authenticate();

    if (!$auth['success']) {
        http_response_code($auth['status']);

        echo json_encode(
            array(
                'success' => false,
                'message' => $auth['message']
            )
        );

        exit;
    }

    $response = $controller->getByUser(
        $auth['user']
    );

    http_response_code($response['status']);

    unset($response['status']);

    echo json_encode($response);

    exit;
}

if ($action === 'all' && $method === 'GET') {

    $auth = $authMiddleware->authenticate();

    if (!$auth['success']) {
        http_response_code($auth['status']);

        echo json_encode(
            array(
                'success' => false,
                'message' => $auth['message']
            )
        );

        exit;
    }

    if (
        !isset($auth['user']['rol']) ||
        $auth['user']['rol'] !== 'admin'
    ) {
        http_response_code(403);

        echo json_encode(
            array(
                'success' => false,
                'message' =>
                    'Acceso permitido solamente para administradores'
            )
        );

        exit;
    }

    $response = $controller->getAll();

    http_response_code($response['status']);

    unset($response['status']);

    echo json_encode($response);

    exit;
}

http_response_code(404);

echo json_encode(
    array(
        'success' => false,
        'message' => 'Ruta de purchase-service no encontrada'
    )
);