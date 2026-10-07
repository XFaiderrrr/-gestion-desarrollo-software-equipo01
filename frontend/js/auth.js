const API_GATEWAY =
    "http://localhost/-gestion-desarrollo-software-equipo01/api-gateway/public/";


/* =====================================================
   LOGIN
===================================================== */

const loginForm =
    document.getElementById("loginForm");

if (loginForm) {

    loginForm.addEventListener(
        "submit",
        async function (event) {

            event.preventDefault();

            const email =
                document.getElementById("email")
                    .value
                    .trim();

            const password =
                document.getElementById("password")
                    .value;

            const message =
                document.getElementById(
                    "loginMessage"
                );

            message.className = "message";
            message.textContent =
                "Iniciando sesión...";
            message.style.display = "block";

            try {

                const response = await fetch(
                    API_GATEWAY +
                    "?action=auth&auth_action=login",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        body: JSON.stringify({
                            email: email,
                            password: password
                        })
                    }
                );

                const data =
                    await response.json();

                if (
                    !response.ok ||
                    !data.success
                ) {

                    message.className =
                        "message error";

                    message.textContent =
                        data.message ||
                        "Correo o contraseña incorrectos.";

                    return;
                }

                localStorage.setItem(
                    "token",
                    data.token
                );

                localStorage.setItem(
                    "user",
                    JSON.stringify(data.user)
                );

                message.className =
                    "message success";

                message.textContent =
                    "¡Inicio de sesión correcto!";

                setTimeout(function () {

                    if (
                        data.user.rol === "admin"
                    ) {

                        window.location.href =
                            "admin.html";

                    } else {

                        window.location.href =
                            "tienda.html";

                    }

                }, 800);

            } catch (error) {

                console.error(error);

                message.className =
                    "message error";

                message.textContent =
                    "No se pudo conectar con el servidor.";
            }

        }
    );
}


/* =====================================================
   REGISTRO
===================================================== */

const registerForm =
    document.getElementById("registerForm");

if (registerForm) {

    registerForm.addEventListener(
        "submit",
        async function (event) {

            event.preventDefault();

            const nombre =
                document.getElementById("nombre")
                    .value
                    .trim();

            const email =
                document.getElementById("email")
                    .value
                    .trim();

            const password =
                document.getElementById("password")
                    .value;

            const passwordConfirm =
                document.getElementById(
                    "passwordConfirm"
                ).value;

            const message =
                document.getElementById(
                    "registerMessage"
                );


            /* -----------------------------
               VALIDAR CONTRASEÑAS
            ----------------------------- */

            if (
                password !== passwordConfirm
            ) {

                message.className =
                    "message error";

                message.textContent =
                    "Las contraseñas no coinciden.";

                message.style.display =
                    "block";

                return;
            }


            /* -----------------------------
               MOSTRAR PROCESANDO
            ----------------------------- */

            message.className =
                "message";

            message.textContent =
                "Creando cuenta...";

            message.style.display =
                "block";


            try {

                const response = await fetch(
                    API_GATEWAY +
                    "?action=auth&auth_action=register",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        body: JSON.stringify({
                            nombre: nombre,
                            email: email,
                            password: password
                        })
                    }
                );


                const data =
                    await response.json();


                /* -----------------------------
                   ERROR
                ----------------------------- */

                if (
                    !response.ok ||
                    !data.success
                ) {

                    message.className =
                        "message error";

                    message.textContent =
                        data.message ||
                        "No se pudo crear la cuenta.";

                    return;
                }


                /* -----------------------------
                   REGISTRO CORRECTO
                ----------------------------- */

                message.className =
                    "message success";

                message.textContent =
                    "¡Cuenta creada correctamente!";


                /*
                 * El usuario todavía no inicia sesión.
                 * Lo enviamos al login.
                 */

                setTimeout(function () {

                    window.location.href =
                        "login.html";

                }, 1200);

            } catch (error) {

                console.error(error);

                message.className =
                    "message error";

                message.textContent =
                    "No se pudo conectar con el servidor.";
            }

        }
    );
}