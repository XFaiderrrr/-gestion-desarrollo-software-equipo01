<?php

require_once "../config/database.php";
require_once "../models/Game.php";
require_once "../controllers/GameController.php";
require_once "../middleware/AdminMiddleware.php";

header(
    "Content-Type: application/json; charset=UTF-8"
);

$database = new Database();

$db = $database->connect();

$gameController =
    new GameController($db);

$adminMiddleware =
    new AdminMiddleware();

$method =
    $_SERVER["REQUEST_METHOD"];

$pathId =
    isset($_GET["id"])
        ? $_GET["id"]
        : null;

$input = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($input)) {
    $input = array();
}


/*
|--------------------------------------------------------------------------
| GET
| Público: cualquiera puede consultar el catálogo
|--------------------------------------------------------------------------
*/

if ($method === "GET") {

    if ($pathId === null) {

        $gameController->getAll();

    } else {

        $gameController->getById(
            $pathId
        );
    }

    exit;
}


/*
|--------------------------------------------------------------------------
| Operaciones administrativas
|--------------------------------------------------------------------------
*/

if (
    $method === "POST" ||
    $method === "PUT" ||
    $method === "DELETE"
) {

    $admin =
        $adminMiddleware->authenticateAdmin();

    if ($admin === false) {
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| POST
| Crear videojuego
|--------------------------------------------------------------------------
*/

if ($method === "POST") {

    $gameController->create(
        $input
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| PUT
| Actualizar videojuego
|--------------------------------------------------------------------------
*/

if ($method === "PUT") {

    if ($pathId === null) {

        http_response_code(400);

        echo json_encode(
            array(
                "success" => false,
                "message" =>
                    "Debe proporcionar un ID de videojuego"
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $gameController->update(
        $pathId,
        $input
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| DELETE
| Eliminar videojuego
|--------------------------------------------------------------------------
*/

if ($method === "DELETE") {

    if ($pathId === null) {

        http_response_code(400);

        echo json_encode(
            array(
                "success" => false,
                "message" =>
                    "Debe proporcionar un ID de videojuego"
            ),
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

    $gameController->delete(
        $pathId
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Método no permitido
|--------------------------------------------------------------------------
*/

http_response_code(405);

echo json_encode(
    array(
        "success" => false,
        "message" => "Método no permitido"
    ),
    JSON_UNESCAPED_UNICODE
);