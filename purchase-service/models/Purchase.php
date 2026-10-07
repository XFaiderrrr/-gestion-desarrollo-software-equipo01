<?php

class Purchase
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($data)
    {
        $sql = "INSERT INTO purchases
                (
                    auth_user_id,
                    game_id,
                    cantidad,
                    precio_unitario,
                    total,
                    estado
                )
                VALUES
                (
                    :auth_user_id,
                    :game_id,
                    :cantidad,
                    :precio_unitario,
                    :total,
                    :estado
                )";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(
            array(
                ':auth_user_id' => $data['auth_user_id'],
                ':game_id' => $data['game_id'],
                ':cantidad' => $data['cantidad'],
                ':precio_unitario' => $data['precio_unitario'],
                ':total' => $data['total'],
                ':estado' => $data['estado']
            )
        );

        return $this->db->lastInsertId();
    }

    public function findById($id)
    {
        $sql = "SELECT *
                FROM purchases
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(
            array(
                ':id' => $id
            )
        );

        return $stmt->fetch();
    }

    public function getAll()
    {
        $sql = "SELECT *
                FROM purchases
                ORDER BY created_at DESC";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }

    public function getByUser($authUserId)
    {
        $sql = "SELECT *
                FROM purchases
                WHERE auth_user_id = :auth_user_id
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(
            array(
                ':auth_user_id' => $authUserId
            )
        );

        return $stmt->fetchAll();
    }
}