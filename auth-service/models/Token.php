<?php

class Token
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($userId)
    {
        $token = bin2hex(random_bytes(32));

        $expiresAt = date(
            "Y-m-d H:i:s",
            time() + (60 * 60 * 24)
        );

        $sql = "
            INSERT INTO tokens
            (
                user_id,
                token,
                expires_at
            )
            VALUES
            (
                :user_id,
                :token,
                :expires_at
            )
        ";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute(array(
            ":user_id" => $userId,
            ":token" => $token,
            ":expires_at" => $expiresAt
        ));

        if ($result) {
            return $token;
        }

        return false;
    }

    public function findValidToken($token)
    {
        $sql = "
            SELECT
                id,
                user_id,
                token,
                expires_at,
                created_at
            FROM tokens
            WHERE token = :token
            AND expires_at > NOW()
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute(array(
            ":token" => $token
        ));

        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $tokenData ? $tokenData : null;
    }

    public function delete($token)
    {
        $sql = "
            DELETE FROM tokens
            WHERE token = :token
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            ":token" => $token
        ));
    }
}