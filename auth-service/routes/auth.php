<?php

require_once "../config/database.php";
require_once "../models/User.php";
require_once "../controllers/AuthController.php";
require_once "../models/Token.php";

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->connect();

$authController = new AuthController($db);

$method = $_SERVER["REQUEST_METHOD"];
$action = isset($_GET["action"]) ? $_GET["action"] : "";

$input = json_decode(
    file_get_contents("php://input"),
    true
);

switch ($action) {

    case "register":

        if ($method !== "POST") {
            http_response_code(405);

            echo json_encode([
                "success" => false,
                "message" => "Método no permitido"
            ]);

            break;
        }

        $authController->register($input ? $input : []);

        break;


    case "login":

        if ($method !== "POST") {
            http_response_code(405);

            echo json_encode([
                "success" => false,
                "message" => "Método no permitido"
            ]);

            break;
        }

        $authController->login($input ? $input : []);

        break;

    case "logout":

    if ($method !== "POST") {
        http_response_code(405);

        echo json_encode([
            "success" => false,
            "message" => "Método no permitido"
        ]);

        break;
    }

    $authController->logout();

    break;


    default:

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Endpoint no encontrado"
        ]);
}