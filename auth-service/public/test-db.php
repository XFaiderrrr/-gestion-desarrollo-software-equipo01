<?php

require_once "../config/database.php";

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->connect();

echo json_encode([
    "success" => true,
    "message" => "Conexión con MySQL correcta",
    "database" => "tienda_auth"
]);