<?php

require_once "../config/database.php";
require_once "../models/Token.php";
require_once "../middleware/AuthMiddleware.php";

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->connect();

$authMiddleware = new AuthMiddleware($db);

$userToken = $authMiddleware->authenticate();

if (!$userToken) {
    exit;
}

echo json_encode([
    "success" => true,
    "message" => "Acceso autorizado",
    "user_id" => $userToken["user_id"]
], JSON_UNESCAPED_UNICODE);