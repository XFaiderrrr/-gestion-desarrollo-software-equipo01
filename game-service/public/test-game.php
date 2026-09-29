<?php

require_once "../config/database.php";
require_once "../models/Game.php";

header(
    "Content-Type: application/json; charset=UTF-8"
);

$database = new Database();

$db = $database->connect();

$gameModel = new Game($db);

$games = $gameModel->getAll();

echo json_encode(
    array(
        "success" => true,
        "message" => "Modelo Game cargado correctamente",
        "games" => $games
    ),
    JSON_UNESCAPED_UNICODE
);