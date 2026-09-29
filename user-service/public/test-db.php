<?php

require_once "../config/database.php";

header(
    "Content-Type: application/json; charset=UTF-8"
);

$database = new Database();

$db = $database->connect();

echo json_encode(
    array(
        "success" => true,
        "message" => "Conexión con tienda_users correcta",
        "database" => "tienda_users"
    ),
    JSON_UNESCAPED_UNICODE
);