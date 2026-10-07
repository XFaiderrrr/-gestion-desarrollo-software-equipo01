<?php

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $database->connect();

    echo json_encode(
        array(
            'success' => true,
            'message' => 'Conexión a tienda_purchases correcta'
        )
    );

} catch (Exception $e) {

    http_response_code(500);

    echo json_encode(
        array(
            'success' => false,
            'message' => $e->getMessage()
        )
    );
}