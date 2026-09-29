<?php

class SyncController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel =
            new User($db);
    }

    public function sync($data)
    {
        try {

            $authUserId =
                isset($data["auth_user_id"])
                    ? $data["auth_user_id"]
                    : null;

            $nombre =
                isset($data["nombre"])
                    ? trim($data["nombre"])
                    : "";

            $email =
                isset($data["email"])
                    ? trim($data["email"])
                    : "";

            $rol =
                isset($data["rol"])
                    ? trim($data["rol"])
                    : "";

            if (
                $authUserId === null ||
                $nombre === "" ||
                $email === "" ||
                $rol === ""
            ) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "auth_user_id, nombre, email y rol son obligatorios"
                    )
                );

                return;
            }

            if (!is_numeric($authUserId)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "auth_user_id inválido"
                    )
                );

                return;
            }

            if (!filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "Correo electrónico inválido"
                    )
                );

                return;
            }

            if (
                $rol !== "usuario" &&
                $rol !== "admin"
            ) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "Rol inválido"
                    )
                );

                return;
            }

            $synced =
                $this->userModel->syncFromAuth(
                    (int) $authUserId,
                    $nombre,
                    $email,
                    $rol
                );

            if (!$synced) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" =>
                            "No fue posible sincronizar el usuario"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" =>
                        "Usuario sincronizado correctamente"
                )
            );

        } catch (PDOException $e) {

            $this->response(
                500,
                array(
                    "success" => false,
                    "message" =>
                        "Error al sincronizar usuario"
                )
            );
        }
    }

    public function delete($data)
    {
        try {

            $authUserId =
                isset($data["auth_user_id"])
                    ? $data["auth_user_id"]
                    : null;

            if (
                $authUserId === null ||
                !is_numeric($authUserId)
            ) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "auth_user_id inválido"
                    )
                );

                return;
            }

            $deleted =
                $this->userModel
                    ->deleteByAuthUserId(
                        (int) $authUserId
                    );

            if (!$deleted) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" =>
                            "No fue posible eliminar el usuario sincronizado"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" =>
                        "Usuario eliminado de user-service"
                )
            );

        } catch (PDOException $e) {

            $this->response(
                500,
                array(
                    "success" => false,
                    "message" =>
                        "Error al eliminar usuario"
                )
            );
        }
    }

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