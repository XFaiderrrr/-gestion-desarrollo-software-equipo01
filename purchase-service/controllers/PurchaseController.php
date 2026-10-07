<?php

class PurchaseController
{
    private $purchaseModel;
    private $gameService;

    public function __construct($db, $gameService)
    {
        $this->purchaseModel = new Purchase($db);
        $this->gameService = $gameService;
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR COMPRA
    |--------------------------------------------------------------------------
    */

    public function create($user, $data)
    {
        try {

            /*
            |--------------------------------------------------------------------------
            | VALIDAR USUARIO
            |--------------------------------------------------------------------------
            */

            if (
                !isset($user['id']) &&
                !isset($user['auth_user_id'])
            ) {
                return array(
                    'success' => false,
                    'status' => 401,
                    'message' => 'Usuario no autenticado'
                );
            }

            $authUserId = isset($user['id'])
                ? (int) $user['id']
                : (int) $user['auth_user_id'];

            /*
            |--------------------------------------------------------------------------
            | VALIDAR DATOS
            |--------------------------------------------------------------------------
            */

            $gameId = isset($data['game_id'])
                ? $data['game_id']
                : '';

            $cantidad = isset($data['cantidad'])
                ? $data['cantidad']
                : '';

            if ($gameId === '' || $cantidad === '') {
                return array(
                    'success' => false,
                    'status' => 400,
                    'message' =>
                        'game_id y cantidad son obligatorios'
                );
            }

            if (
                !is_numeric($gameId) ||
                !is_numeric($cantidad)
            ) {
                return array(
                    'success' => false,
                    'status' => 400,
                    'message' =>
                        'game_id y cantidad deben ser numéricos'
                );
            }

            $gameId = (int) $gameId;
            $cantidad = (int) $cantidad;

            if ($gameId <= 0 || $cantidad <= 0) {
                return array(
                    'success' => false,
                    'status' => 400,
                    'message' =>
                        'game_id y cantidad deben ser mayores a 0'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CONSULTAR VIDEOJUEGO
            |--------------------------------------------------------------------------
            */

            $gameResponse =
                $this->gameService->getGame($gameId);

            if (!$gameResponse['success']) {
                return array(
                    'success' => false,
                    'status' => 404,
                    'message' =>
                        'No fue posible obtener el videojuego'
                );
            }

            $game = $gameResponse['game'];

            /*
            |--------------------------------------------------------------------------
            | VALIDAR STOCK
            |--------------------------------------------------------------------------
            */

            $stock = isset($game['stock'])
                ? (int) $game['stock']
                : 0;

            if ($stock < $cantidad) {
                return array(
                    'success' => false,
                    'status' => 400,
                    'message' => 'Stock insuficiente',
                    'stock_disponible' => $stock
                );
            }

            /*
            |--------------------------------------------------------------------------
            | OBTENER PRECIO
            |--------------------------------------------------------------------------
            */

            $precioUnitario = isset($game['precio'])
                ? (float) $game['precio']
                : 0;

            if ($precioUnitario <= 0) {
                return array(
                    'success' => false,
                    'status' => 400,
                    'message' =>
                        'El videojuego tiene un precio inválido'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CALCULAR TOTAL
            |--------------------------------------------------------------------------
            */

            $total = $precioUnitario * $cantidad;

            /*
            |--------------------------------------------------------------------------
            | DISMINUIR STOCK
            |--------------------------------------------------------------------------
            */

            $stockResponse =
                $this->gameService->decreaseStock(
                    $gameId,
                    $cantidad
                );

            if (!$stockResponse['success']) {
                return array(
                    'success' => false,
                    'status' => 400,
                    'message' =>
                        isset($stockResponse['message'])
                            ? $stockResponse['message']
                            : 'No fue posible actualizar el stock'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | REGISTRAR COMPRA
            |--------------------------------------------------------------------------
            */

            $purchaseId =
                $this->purchaseModel->create(
                    array(
                        'auth_user_id' => $authUserId,
                        'game_id' => $gameId,
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'total' => $total,
                        'estado' => 'completada'
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | RESPUESTA
            |--------------------------------------------------------------------------
            */

            return array(
                'success' => true,
                'status' => 201,
                'message' =>
                    'Compra realizada correctamente',
                'purchase' => array(
                    'id' => $purchaseId,
                    'auth_user_id' => $authUserId,
                    'game_id' => $gameId,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'total' => $total,
                    'estado' => 'completada'
                ),
                'game' => $stockResponse['game']
            );

        } catch (PDOException $e) {

            return array(
                'success' => false,
                'status' => 500,
                'message' =>
                    'Error al registrar la compra'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER COMPRA POR ID
    |--------------------------------------------------------------------------
    */

    public function getById($id)
    {
        try {

            if (!is_numeric($id)) {
                return array(
                    'success' => false,
                    'status' => 400,
                    'message' => 'ID de compra inválido'
                );
            }

            $purchase =
                $this->purchaseModel->findById(
                    (int) $id
                );

            if (!$purchase) {
                return array(
                    'success' => false,
                    'status' => 404,
                    'message' => 'Compra no encontrada'
                );
            }

            return array(
                'success' => true,
                'status' => 200,
                'purchase' => $purchase
            );

        } catch (PDOException $e) {

            return array(
                'success' => false,
                'status' => 500,
                'message' =>
                    'Error al consultar la compra'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER COMPRAS DE UN USUARIO
    |--------------------------------------------------------------------------
    */

    public function getByUser($user)
    {
        try {

            if (
                !isset($user['id']) &&
                !isset($user['auth_user_id'])
            ) {
                return array(
                    'success' => false,
                    'status' => 401,
                    'message' => 'Usuario no autenticado'
                );
            }

            $authUserId = isset($user['id'])
                ? (int) $user['id']
                : (int) $user['auth_user_id'];

            $purchases =
                $this->purchaseModel->getByUser(
                    $authUserId
                );

            return array(
                'success' => true,
                'status' => 200,
                'purchases' => $purchases
            );

        } catch (PDOException $e) {

            return array(
                'success' => false,
                'status' => 500,
                'message' =>
                    'Error al consultar las compras'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER TODAS LAS COMPRAS
    |--------------------------------------------------------------------------
    */

    public function getAll()
    {
        try {

            $purchases =
                $this->purchaseModel->getAll();

            return array(
                'success' => true,
                'status' => 200,
                'purchases' => $purchases
            );

        } catch (PDOException $e) {

            return array(
                'success' => false,
                'status' => 500,
                'message' =>
                    'Error al consultar las compras'
            );
        }
    }
}