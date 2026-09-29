<?php

class GameController
{
    private $gameModel;

    public function __construct($db)
    {
        $this->gameModel = new Game($db);
    }

    /*
    |--------------------------------------------------------------------------
    | LISTAR VIDEOJUEGOS
    |--------------------------------------------------------------------------
    */

    public function getAll()
    {
        try {

            $games = $this->gameModel->getAll();

            $this->response(
                200,
                array(
                    "success" => true,
                    "games" => $games
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER VIDEOJUEGO POR ID
    |--------------------------------------------------------------------------
    */

    public function getById($id)
    {
        try {

            if (!is_numeric($id)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "ID de videojuego inválido"
                    )
                );

                return;
            }

            $game = $this->gameModel->findById(
                (int) $id
            );

            if (!$game) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" => "Videojuego no encontrado"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "game" => $game
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR VIDEOJUEGO
    |--------------------------------------------------------------------------
    */

    public function create($data)
    {
        try {

            $nombre = isset($data["nombre"])
                ? trim($data["nombre"])
                : "";

            $descripcion = isset($data["descripcion"])
                ? trim($data["descripcion"])
                : "";

            $precio = isset($data["precio"])
                ? $data["precio"]
                : "";

            $genero = isset($data["genero"])
                ? trim($data["genero"])
                : "";

            $plataforma = isset($data["plataforma"])
                ? trim($data["plataforma"])
                : "";

            $stock = isset($data["stock"])
                ? $data["stock"]
                : "";

            $imagen = isset($data["imagen"])
                ? trim($data["imagen"])
                : null;

            /*
             * Validaciones
             */

            if (
                $nombre === "" ||
                $descripcion === "" ||
                $precio === "" ||
                $genero === "" ||
                $plataforma === "" ||
                $stock === ""
            ) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "nombre, descripcion, precio, genero, plataforma y stock son obligatorios"
                    )
                );

                return;
            }

            if (!is_numeric($precio)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El precio debe ser numérico"
                    )
                );

                return;
            }

            if ((float) $precio < 0) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El precio no puede ser negativo"
                    )
                );

                return;
            }

            if (!is_numeric($stock)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El stock debe ser numérico"
                    )
                );

                return;
            }

            if ((int) $stock < 0) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El stock no puede ser negativo"
                    )
                );

                return;
            }

            $createdId = $this->gameModel->create(
                $nombre,
                $descripcion,
                (float) $precio,
                $genero,
                $plataforma,
                (int) $stock,
                $imagen
            );

            if (!$createdId) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" =>
                            "No fue posible registrar el videojuego"
                    )
                );

                return;
            }

            $this->response(
                201,
                array(
                    "success" => true,
                    "message" =>
                        "Videojuego registrado correctamente",
                    "game_id" => $createdId
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR VIDEOJUEGO
    |--------------------------------------------------------------------------
    */

    public function update($id, $data)
    {
        try {

            if (!is_numeric($id)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "ID de videojuego inválido"
                    )
                );

                return;
            }

            $game = $this->gameModel->findById(
                (int) $id
            );

            if (!$game) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" => "Videojuego no encontrado"
                    )
                );

                return;
            }

            $nombre = isset($data["nombre"])
                ? trim($data["nombre"])
                : "";

            $descripcion = isset($data["descripcion"])
                ? trim($data["descripcion"])
                : "";

            $precio = isset($data["precio"])
                ? $data["precio"]
                : "";

            $genero = isset($data["genero"])
                ? trim($data["genero"])
                : "";

            $plataforma = isset($data["plataforma"])
                ? trim($data["plataforma"])
                : "";

            $stock = isset($data["stock"])
                ? $data["stock"]
                : "";

            $imagen = isset($data["imagen"])
                ? trim($data["imagen"])
                : null;

            if (
                $nombre === "" ||
                $descripcion === "" ||
                $precio === "" ||
                $genero === "" ||
                $plataforma === "" ||
                $stock === ""
            ) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "Todos los campos del videojuego son obligatorios"
                    )
                );

                return;
            }

            if (!is_numeric($precio)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El precio debe ser numérico"
                    )
                );

                return;
            }

            if ((float) $precio < 0) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El precio no puede ser negativo"
                    )
                );

                return;
            }

            if (!is_numeric($stock)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El stock debe ser numérico"
                    )
                );

                return;
            }

            if ((int) $stock < 0) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "El stock no puede ser negativo"
                    )
                );

                return;
            }

            $updated = $this->gameModel->update(
                (int) $id,
                $nombre,
                $descripcion,
                (float) $precio,
                $genero,
                $plataforma,
                (int) $stock,
                $imagen
            );

            if (!$updated) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" =>
                            "No fue posible actualizar el videojuego"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" =>
                        "Videojuego actualizado correctamente"
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR VIDEOJUEGO
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        try {

            if (!is_numeric($id)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" => "ID de videojuego inválido"
                    )
                );

                return;
            }

            $game = $this->gameModel->findById(
                (int) $id
            );

            if (!$game) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" => "Videojuego no encontrado"
                    )
                );

                return;
            }

            $deleted = $this->gameModel->delete(
                (int) $id
            );

            if (!$deleted) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" =>
                            "No fue posible eliminar el videojuego"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" =>
                        "Videojuego eliminado correctamente"
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ERROR DE BASE DE DATOS
    |--------------------------------------------------------------------------
    */

    private function databaseError($e)
    {
        http_response_code(500);

        $response = array(
            "success" => false,
            "message" =>
                "Error al acceder a la base de datos"
        );

        if (getenv("DB_DEBUG") === "true") {

            $response["debug"] = $e->getMessage();
        }

        echo json_encode(
            $response,
            JSON_UNESCAPED_UNICODE
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESPUESTA
    |--------------------------------------------------------------------------
    */

    private function response($status, $data)
    {
        http_response_code($status);

        header(
            "Content-Type: application/json; charset=UTF-8"
        );

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );
    }
}