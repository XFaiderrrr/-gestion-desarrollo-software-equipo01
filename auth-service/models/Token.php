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

        $sql = "INSERT INTO tokens (user_id, token, expires_at)
                VALUES (:user_id, :token, :expires_at)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":token", $token);
        $stmt->bindParam(":expires_at", $expiresAt);

        if ($stmt->execute()) {
            return $token;
        }

        return false;
    }

    public function findValidToken($token)
    {
        $sql = "SELECT *
                FROM tokens
                WHERE token = :token
                AND expires_at > NOW()
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":token", $token);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function delete($token)
    {
        $sql = "DELETE FROM tokens
                WHERE token = :token";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(":token", $token);

        return $stmt->execute();
    }
}