<?php

require_once "../config/database.php";
require_once "../models/User.php";
require_once "../models/Token.php";
require_once "../services/UserServiceClient.php";
require_once "../controllers/AuthController.php";

header(
    "Content-Type: application/json; charset=UTF-8"
);

/*
|--------------------------------------------------------------------------
| Solo permitir solicitudes locales
|--------------------------------------------------------------------------
*/

$remoteAddress = isset($_SERVER["REMOTE_ADDR"])
    ? $_SERVER["REMOTE_ADDR"]
    : "";

if (
    $remoteAddress !== "127.0.0.1" &&
    $remoteAddress !== "::1"
) {

    http_response_code(403);

    echo json_encode(
        array(
            "success" => false,
            "message" =>
                "Endpoint interno no disponible"
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

$database = new Database();
$db = $database->connect();

$authController =
    new AuthController($db);

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "POST") {

    http_response_code(405);

    echo json_encode(
        array(
            "success" => false,
            "message" => "Método no permitido"
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

$action = isset($_GET["action"])
    ? $_GET["action"]
    : "";

$input = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($input)) {
    $input = array();
}

switch ($action) {

    case "update-user":

        $authController->updateFromUserService(
            $input
        );

        break;

    case "delete-user":

        $authController->deleteFromUserService(
            $input
        );

        break;

    default:

        http_response_code(404);

        echo json_encode(
            array(
                "success" => false,
                "message" => "Acción interna no encontrada"
            ),
            JSON_UNESCAPED_UNICODE
        );

        break;
}