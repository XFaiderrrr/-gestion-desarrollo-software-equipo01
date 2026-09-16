<?php

require_once "../config/database.php";
require_once "../models/User.php";

header("Content-Type: application/json; charset=UTF-8");

$database = new Database();
$db = $database->connect();

$userModel = new User($db);

echo json_encode([
    "success" => true,
    "message" => "Modelo User cargado correctamente"
]);