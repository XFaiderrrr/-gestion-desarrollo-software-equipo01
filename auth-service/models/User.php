<?php

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findByEmail($email)
    {
        $sql = "
            SELECT *
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(array(
            ":email" => $email
        ));

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ? $user : null;
    }

    public function findById($id)
    {
        $sql = "
            SELECT
                id,
                nombre,
                email,
                rol,
                created_at
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

    public function emailExists($email)
    {
        $sql = "
            SELECT id
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(array(
            ":email" => $email
        ));

        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function create($nombre, $email, $password)
    {
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "
            INSERT INTO users
            (
                nombre,
                email,
                password,
                rol
            )
            VALUES
            (
                :nombre,
                :email,
                :password,
                'usuario'
            )
        ";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute(array(
            ":nombre" => $nombre,
            ":email" => $email,
            ":password" => $hashedPassword
        ));

        if (!$result) {
            return false;
        }

        return $this->db->lastInsertId();
    }

    public function update($id, $nombre, $email, $rol)
    {
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
}