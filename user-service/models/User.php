<?php

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "
            SELECT
                id,
                auth_user_id,
                nombre,
                email,
                rol,
                created_at,
                updated_at
            FROM users
            ORDER BY id ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "
            SELECT
                id,
                auth_user_id,
                nombre,
                email,
                rol,
                created_at,
                updated_at
            FROM users
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(array(
            ":id" => $id
        ));

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $user : null;
    }

    public function findByAuthUserId($authUserId)
    {
        $sql = "
            SELECT
                id,
                auth_user_id,
                nombre,
                email,
                rol,
                created_at,
                updated_at
            FROM users
            WHERE auth_user_id = :auth_user_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(array(
            ":auth_user_id" => $authUserId
        ));

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $user : null;
    }

    public function create(
        $authUserId,
        $nombre,
        $email,
        $rol = "usuario"
    ) {
        $sql = "
            INSERT INTO users
            (
                auth_user_id,
                nombre,
                email,
                rol
            )
            VALUES
            (
                :auth_user_id,
                :nombre,
                :email,
                :rol
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            ":auth_user_id" => $authUserId,
            ":nombre" => $nombre,
            ":email" => $email,
            ":rol" => $rol
        ));
    }

    public function update(
        $id,
        $nombre,
        $email,
        $rol
    ) {
        $sql = "
            UPDATE users
            SET
                nombre = :nombre,
                email = :email,
                rol = :rol
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            ":id" => $id,
            ":nombre" => $nombre,
            ":email" => $email,
            ":rol" => $rol
        ));
    }

    public function delete($id)
    {
        $sql = "
            DELETE FROM users
            WHERE id = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            ":id" => $id
        ));
    }
    public function syncFromAuth(
    $authUserId,
    $nombre,
    $email,
    $rol
) {
    $existingUser = $this->findByAuthUserId(
        $authUserId
    );

    if ($existingUser) {

        $sql = "
            UPDATE users
            SET
                nombre = :nombre,
                email = :email,
                rol = :rol
            WHERE auth_user_id = :auth_user_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            ":nombre" => $nombre,
            ":email" => $email,
            ":rol" => $rol,
            ":auth_user_id" => $authUserId
        ));
    }

    $sql = "
        INSERT INTO users
        (
            auth_user_id,
            nombre,
            email,
            rol
        )
        VALUES
        (
            :auth_user_id,
            :nombre,
            :email,
            :rol
        )
    ";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute(array(
        ":auth_user_id" => $authUserId,
        ":nombre" => $nombre,
        ":email" => $email,
        ":rol" => $rol
    ));
}

public function deleteByAuthUserId($authUserId)
{
    $sql = "
        DELETE FROM users
        WHERE auth_user_id = :auth_user_id
    ";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute(array(
        ":auth_user_id" => $authUserId
    ));
}
}