<?php

require_once "../config/database.php";
require_once "../models/User.php";
require_once "../controllers/UserController.php";
require_once "../middleware/AdminMiddleware.php";

header(
    "Content-Type: application/json; charset=UTF-8"
);

$database = new Database();
$db = $database->connect();

$userController = new UserController($db);
$adminMiddleware = new AdminMiddleware();

$method = $_SERVER["REQUEST_METHOD"];

$pathId = isset($_GET["id"])
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
| Todas las operaciones administrativas requieren ADMIN
|--------------------------------------------------------------------------
*/

$admin = $adminMiddleware->authenticateAdmin();

if ($admin === false) {
    exit;
}

switch ($method) {

    /*
    |--------------------------------------------------------------------------
    | GET
    |--------------------------------------------------------------------------
    */

    case "GET":

        if ($pathId === null) {

            $userController->getAll();

        } else {

            $userController->getById($pathId);
        }

        break;


    /*
    |--------------------------------------------------------------------------
    | PUT
    |--------------------------------------------------------------------------
    */

    case "PUT":

        if ($pathId === null) {

            http_response_code(400);

            echo json_encode(
                array(
                    "success" => false,
                    "message" => "Debe proporcionar un ID"
                ),
                JSON_UNESCAPED_UNICODE
            );

            break;
        }

        $userController->update(
            $pathId,
            $input
        );

        break;


    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    case "DELETE":

        if ($pathId === null) {

            http_response_code(400);

            echo json_encode(
                array(
                    "success" => false,
                    "message" => "Debe proporcionar un ID"
                ),
                JSON_UNESCAPED_UNICODE
            );

            break;
        }

        $userController->delete(
            $pathId
        );

        break;


    default:

        http_response_code(405);

        echo json_encode(
            array(
                "success" => false,
                "message" => "Método no permitido"
            ),
            JSON_UNESCAPED_UNICODE
        );

        break;
}