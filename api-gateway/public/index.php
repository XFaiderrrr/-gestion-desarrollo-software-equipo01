<?php

header('Content-Type: application/json; charset=utf-8');

$action = isset($_GET['action'])
    ? $_GET['action']
    : '';

switch ($action) {

    case 'auth':
        require_once __DIR__ . '/../routes/auth.php';
        break;

    case 'users':
        require_once __DIR__ . '/../routes/users.php';
        break;

    case 'games':
        require_once __DIR__ . '/../routes/games.php';
        break;

    case 'purchases':
        require_once __DIR__ . '/../routes/purchases.php';
        break;

    default:

        http_response_code(404);

        echo json_encode(
            array(
                'success' => false,
                'message' => 'Ruta del API Gateway no encontrada'
            )
        );

        break;
}