<?php

class Game
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER TODOS LOS VIDEOJUEGOS
    |--------------------------------------------------------------------------
    */

    public function getAll()
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                precio,
                genero,
                plataforma,
                stock,
                imagen,
                created_at,
                updated_at
            FROM games
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |--------------------------------------------------------------------------
    | OBTENER VIDEOJUEGO POR ID
    |--------------------------------------------------------------------------
    */

    public function findById($id)
    {
        $sql = "
            SELECT
                id,
                nombre,
                descripcion,
                precio,
                genero,
                plataforma,
                stock,
                imagen,
                created_at,
                updated_at
            FROM games
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(array(
            ":id" => $id
        ));

        $game = $stmt->fetch(PDO::FETCH_ASSOC);

        return $game ? $game : null;
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR VIDEOJUEGO
    |--------------------------------------------------------------------------
    */

    public function create(
        $nombre,
        $descripcion,
        $precio,
        $genero,
        $plataforma,
        $stock,
        $imagen
    ) {
        $sql = "
            INSERT INTO games
            (
                nombre,
                descripcion,
                precio,
                genero,
                plataforma,
                stock,
                imagen
            )
            VALUES
            (
                :nombre,
                :descripcion,
                :precio,
                :genero,
                :plataforma,
                :stock,
                :imagen
            )
        ";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute(array(
            ":nombre" => $nombre,
            ":descripcion" => $descripcion,
            ":precio" => $precio,
            ":genero" => $genero,
            ":plataforma" => $plataforma,
            ":stock" => $stock,
            ":imagen" => $imagen
        ));

        if (!$result) {
            return false;
        }

        return $this->db->lastInsertId();
    }

    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR VIDEOJUEGO
    |--------------------------------------------------------------------------
    */

    public function update(
        $id,
        $nombre,
        $descripcion,
        $precio,
        $genero,
        $plataforma,
        $stock,
        $imagen
    ) {
        $sql = "
            UPDATE games
            SET
                nombre = :nombre,
                descripcion = :descripcion,
                precio = :precio,
                genero = :genero,
                plataforma = :plataforma,
                stock = :stock,
                imagen = :imagen
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            ":id" => $id,
            ":nombre" => $nombre,
            ":descripcion" => $descripcion,
            ":precio" => $precio,
            ":genero" => $genero,
            ":plataforma" => $plataforma,
            ":stock" => $stock,
            ":imagen" => $imagen
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | ELIMINAR VIDEOJUEGO
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $sql = "
            DELETE FROM games
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            ":id" => $id
        ));
    }
}