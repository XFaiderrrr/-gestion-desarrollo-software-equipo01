<?php

class UserController
{
    private $userModel;

    public function __construct($db)
    {
        $this->userModel = new User($db);
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
                    "message" => "Error al consultar usuarios",
                    "debug" => getenv("DB_DEBUG") === "true"
                        ? $e->getMessage()
                        : null
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
                        "message" => "ID de usuario inválido"
                    )
                );

                return;
            }

            $user = $this->userModel->findById(
                (int) $id
            );

            if (!$user) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" => "Usuario no encontrado"
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
                        "message" => "ID de usuario inválido"
                    )
                );

                return;
            }

            $nombre = isset($data["nombre"])
                ? trim($data["nombre"])
                : "";

            $email = isset($data["email"])
                ? trim($data["email"])
                : "";

            $rol = isset($data["rol"])
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
                        "message" => "nombre, email y rol son obligatorios"
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
                        "message" => "Correo electrónico inválido"
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
                        "message" => "Rol inválido"
                    )
                );

                return;
            }

            $user = $this->userModel->findById(
                (int) $id
            );

            if (!$user) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" => "Usuario no encontrado"
                    )
                );

                return;
            }

            $updated = $this->userModel->update(
                (int) $id,
                $nombre,
                $email,
                $rol
            );

            if (!$updated) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" => "No fue posible actualizar el usuario"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" => "Usuario actualizado correctamente"
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
                        "message" => "ID de usuario inválido"
                    )
                );

                return;
            }

            $user = $this->userModel->findById(
                (int) $id
            );

            if (!$user) {

                $this->response(
                    404,
                    array(
                        "success" => false,
                        "message" => "Usuario no encontrado"
                    )
                );

                return;
            }

            $deleted = $this->userModel->delete(
                (int) $id
            );

            if (!$deleted) {

                $this->response(
                    500,
                    array(
                        "success" => false,
                        "message" => "No fue posible eliminar el usuario"
                    )
                );

                return;
            }

            $this->response(
                200,
                array(
                    "success" => true,
                    "message" => "Usuario eliminado correctamente"
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
            "message" => "Error al acceder a la base de datos"
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