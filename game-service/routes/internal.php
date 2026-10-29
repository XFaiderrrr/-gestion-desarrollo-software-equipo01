<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../controllers/GameController.php';


$expectedSecret = getenv('INTERNAL_API_SECRET');

$providedSecret = isset($_SERVER['HTTP_X_INTERNAL_SECRET'])
    ? $_SERVER['HTTP_X_INTERNAL_SECRET']
    : '';

if (
    $expectedSecret === false ||
    $expectedSecret === '' ||
    $providedSecret === '' ||
    !hash_equals($expectedSecret, $providedSecret)
) {
    http_response_code(403);

    echo json_encode(
        array(
            'success' => false,
            'message' => 'Acceso interno no autorizado'
        )
    );

    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode(
        array(
            'success' => false,
            'message' => 'Metodo no permitido'
        )
    );

    exit;
}

$action = isset($_GET['action'])
    ? $_GET['action']
    : '';

$input = json_decode(
    file_get_contents('php://input'),
    true
);

if (!is_array($input)) {
    $input = array();
}

try {

    $database = new Database();
    $db = $database->connect();

   $controller = new GameController($db);
   
    switch ($action) {

        case 'decrease-stock':
            $controller->decreaseStock($input);
            break;

        default:
            http_response_code(404);

            echo json_encode(
                array(
                    'success' => false,
                    'message' => 'Accion interna no encontrada'
                )
            );

            break;
    }

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode(
        array(
            'success' => false,
            'message' => 'Error interno del game-service'
        )
    );
}