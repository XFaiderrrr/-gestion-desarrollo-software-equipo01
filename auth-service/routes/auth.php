<?php

require_once "../config/database.php";
require_once "../models/User.php";
require_once "../controllers/AuthController.php";

header(
    "Content-Type: application/json; charset=UTF-8"
);

$database = new Database();
$db = $database->connect();

$authController = new AuthController($db);

$method = $_SERVER["REQUEST_METHOD"];

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

    case "register":

        if ($method !== "POST") {

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

        $authController->register($input);

        break;


    case "login":

        if ($method !== "POST") {

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

        $authController->login($input);

        break;


    default:

        http_response_code(404);

        echo json_encode(
            array(
                "success" => false,
                "message" => "Endpoint no encontrado"
            ),
            JSON_UNESCAPED_UNICODE
        );

        break;
}