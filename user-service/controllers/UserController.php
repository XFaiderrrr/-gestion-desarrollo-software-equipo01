<?php

class UserController
{
    private $userModel;
    private $authServiceClient;

    public function __construct($db)
    {
        $this->userModel = new User($db);

        $this->authServiceClient =
            new AuthServiceClient();
    }

    public function getAll()
    {
        try {

            $users = $this->userModel->getAll();

            $this->response(
                200,
                array(
                    "success" => true,
                    "users" => $users
                )
            );

        } catch (PDOException $e) {

            $this->response(
                500,
                array(
                    "success" => false,
                    "message" =>
                        "Error al consultar usuarios"
                )
            );
        }
    }

    public function getById($id)
    {
        try {

            if (!is_numeric($id)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "ID de usuario inválido"
                    )
                );

                return;
            }

            $user =
                $this->userModel->findById(
                    (int) $id
                );

            if (!$user) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" =>
                            "Usuario no encontrado"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "user" => $user
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    public function update($id, $data)
    {
        try {

            if (!is_numeric($id)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "ID de usuario inválido"
                    )
                );

                return;
            }

            $user =
                $this->userModel->findById(
                    (int) $id
                );

            if (!$user) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" =>
                            "Usuario no encontrado"
                    )
                );

                return;
            }

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
                $nombre === "" ||
                $email === "" ||
                $rol === ""
            ) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "nombre, email y rol son obligatorios"
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

            /*
             * El cambio real lo hace auth-service.
             */

            $updated =
                $this->authServiceClient->updateUser(
                    $user["auth_user_id"],
                    $nombre,
                    $email,
                    $rol
                );

            if (!$updated) {

                $this->response(
                    503,
                    array(
                        "success" => false,
                        "message" =>
                            "No fue posible actualizar el usuario en auth-service"
                    )
                );

                return;
            }

            /*
             * auth-service sincroniza
             * automáticamente la copia local.
             */

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" =>
                        "Usuario actualizado y sincronizado correctamente"
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

    public function delete($id)
    {
        try {

            if (!is_numeric($id)) {

                $this->response(
                    400,
                    array(
                        "success" => false,
                        "message" =>
                            "ID de usuario inválido"
                    )
                );

                return;
            }

            $user =
                $this->userModel->findById(
                    (int) $id
                );

            if (!$user) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" =>
                            "Usuario no encontrado"
                    )
                );

                return;
            }

            /*
             * El borrado real lo hace auth-service.
             */

            $deleted =
                $this->authServiceClient->deleteUser(
                    $user["auth_user_id"]
                );

            if (!$deleted) {

                $this->response(
                    503,
                    array(
                        "success" => false,
                        "message" =>
                            "No fue posible eliminar el usuario en auth-service"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" =>
                        "Usuario eliminado y sincronizado correctamente"
                )
            );

        } catch (PDOException $e) {

            $this->databaseError($e);
        }
    }

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