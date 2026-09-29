<?php

require_once "../config/database.php";
require_once "../models/User.php";
require_once "../controllers/SyncController.php";

header(
    "Content-Type: application/json; charset=UTF-8"
);

$database = new Database();
$db = $database->connect();

$syncController = new SyncController($db);

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

$input = json_decode(
    file_get_contents("php://input"),
    true
);

if (!is_array($input)) {
    $input = array();
}

$syncController->sync($input);