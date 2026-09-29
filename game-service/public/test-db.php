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
        "message" => "Conexión con tienda_games correcta",
        "database" => "tienda_games"
    ),
    JSON_UNESCAPED_UNICODE
);