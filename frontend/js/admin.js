const ADMIN_API =
    "../api-gateway/public/";

/* =====================================================
   INICIO
===================================================== */

document.addEventListener(
    "DOMContentLoaded",
    function () {

        verificarAdministrador();

        cargarVideojuegos();

        cargarUsuarios();

        configurarFormulario();

        configurarLogout();

    }
);


/* =====================================================
   VERIFICAR ADMINISTRADOR
===================================================== */

function verificarAdministrador() {

    const token =
        localStorage.getItem("token");

    const userData =
        localStorage.getItem("user");


    if (!token || !userData) {

        window.location.href =
            "login.html";

        return;
    }


    try {

        const user =
            JSON.parse(userData);


        if (user.rol !== "admin") {

            alert(
                "No tienes permisos para acceder al panel."
            );

            window.location.href =
                "tienda.html";

            return;
        }


        const adminName =
            document.getElementById(
                "adminName"
            );


        if (adminName) {

            adminName.textContent =
                user.nombre ||
                "Administrador";
        }


    } catch (error) {

        console.error(error);


        localStorage.removeItem(
            "token"
        );

        localStorage.removeItem(
            "user"
        );


        window.location.href =
            "login.html";
    }
}


/* =====================================================
   VIDEOJUEGOS
===================================================== */


/* =====================================================
   CARGAR VIDEOJUEGOS
===================================================== */

async function cargarVideojuegos() {

    const container =
        document.getElementById(
            "gamesAdminContainer"
        );


    if (!container) {

        return;
    }


    container.innerHTML =
        '<div class="loading">' +
        'Cargando videojuegos...' +
        '</div>';


    try {

        const response =
            await fetch(
                ADMIN_API +
                "?action=games&game_action=all"
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            container.innerHTML =
                '<div class="empty-store">' +
                (
                    data.message ||
                    "No se pudo cargar el catálogo."
                ) +
                '</div>';

            return;
        }


        const games =
            data.games ||
            data.data ||
            [];


        mostrarVideojuegos(games);


    } catch (error) {

        console.error(error);


        container.innerHTML =
            '<div class="empty-store">' +
            'No se pudo conectar con el servidor.' +
            '</div>';
    }
}


/* =====================================================
   MOSTRAR VIDEOJUEGOS
===================================================== */

function mostrarVideojuegos(games) {

    const container =
        document.getElementById(
            "gamesAdminContainer"
        );


    if (
        !games ||
        games.length === 0
    ) {

        container.innerHTML =
            '<div class="empty-store">' +
            'No hay videojuegos registrados.' +
            '</div>';

        return;
    }


    let html = `

        <table class="admin-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Videojuego</th>

                    <th>Género</th>

                    <th>Plataforma</th>

                    <th>Precio</th>

                    <th>Stock</th>

                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

    `;


    games.forEach(function (game) {

        const stock =
            Number(game.stock);


        const price =
            Number(game.precio);


        const stockBadge =
            stock > 0

                ? `
                    <span class="badge badge-stock">
                        ${stock}
                    </span>
                  `

                : `
                    <span class="badge badge-empty">
                        Agotado
                    </span>
                  `;


        html += `

            <tr>

                <td>
                    ${game.id}
                </td>

                <td>

                    <strong>
                        ${escapeHTML(
                            game.nombre
                        )}
                    </strong>

                </td>

                <td>
                    ${escapeHTML(
                        game.genero
                    )}
                </td>

                <td>
                    ${escapeHTML(
                        game.plataforma
                    )}
                </td>

                <td>
                    $${price.toFixed(2)}
                </td>

                <td>
                    ${stockBadge}
                </td>

                <td>

                    <div class="admin-actions">

                        <button
                            class="btn-small btn-edit"
                            onclick="editarVideojuego(${game.id})"
                        >
                            Editar
                        </button>

                        <button
                            class="btn-small btn-delete"
                            onclick="eliminarVideojuego(${game.id})"
                        >
                            Eliminar
                        </button>

                    </div>

                </td>

            </tr>

        `;

    });


    html += `

            </tbody>

        </table>

    `;


    container.innerHTML =
        html;
}


/* =====================================================
   CONFIGURAR FORMULARIO VIDEOJUEGO
===================================================== */

function configurarFormulario() {

    const newGameButton =
        document.getElementById(
            "newGameButton"
        );


    if (!newGameButton) {

        return;
    }


    newGameButton.addEventListener(
        "click",
        function () {

            mostrarFormularioNuevoVideojuego();

        }
    );
}


/* =====================================================
   FORMULARIO NUEVO VIDEOJUEGO
===================================================== */

function mostrarFormularioNuevoVideojuego() {

    const formularioExistente =
        document.getElementById(
            "newGameFormContainer"
        );


    if (formularioExistente) {

        return;
    }


    const container =
        document.getElementById(
            "gamesAdminContainer"
        );


    const formContainer =
        document.createElement(
            "div"
        );


    formContainer.id =
        "newGameFormContainer";


    formContainer.className =
        "game-form active";


    formContainer.innerHTML = `

        <h3>
            Nuevo videojuego
        </h3>


        <form id="newGameForm">

            <div class="form-grid">


                <div class="form-group">

                    <label for="gameNombre">
                        Nombre
                    </label>

                    <input
                        type="text"
                        id="gameNombre"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="gameGenero">
                        Género
                    </label>

                    <input
                        type="text"
                        id="gameGenero"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="gamePlataforma">
                        Plataforma
                    </label>

                    <input
                        type="text"
                        id="gamePlataforma"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="gamePrecio">
                        Precio
                    </label>

                    <input
                        type="number"
                        id="gamePrecio"
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="gameStock">
                        Stock
                    </label>

                    <input
                        type="number"
                        id="gameStock"
                        min="0"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="gameImagen">
                        Imagen
                    </label>

                    <input
                        type="text"
                        id="gameImagen"
                        placeholder="URL opcional"
                    >

                </div>


                <div class="form-group full-width">

                    <label for="gameDescripcion">
                        Descripción
                    </label>

                    <textarea
                        id="gameDescripcion"
                        required
                    ></textarea>

                </div>


            </div>


            <div class="form-actions">


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Guardar videojuego
                </button>


                <button
                    type="button"
                    id="cancelGameButton"
                    class="btn btn-cancel"
                >
                    Cancelar
                </button>


            </div>


        </form>

    `;


    container.parentNode.insertBefore(
        formContainer,
        container
    );


    const newGameForm =
        document.getElementById(
            "newGameForm"
        );


    newGameForm.addEventListener(
        "submit",
        crearVideojuego
    );


    document
        .getElementById(
            "cancelGameButton"
        )
        .addEventListener(
            "click",
            function () {

                formContainer.remove();

            }
        );
}


/* =====================================================
   CREAR VIDEOJUEGO
===================================================== */

async function crearVideojuego(event) {

    event.preventDefault();


    const token =
        localStorage.getItem(
            "token"
        );


    if (!token) {

        window.location.href =
            "login.html";

        return;
    }


    const game = {

        nombre:
            document.getElementById(
                "gameNombre"
            ).value.trim(),

        descripcion:
            document.getElementById(
                "gameDescripcion"
            ).value.trim(),

        precio:
            Number(
                document.getElementById(
                    "gamePrecio"
                ).value
            ),

        genero:
            document.getElementById(
                "gameGenero"
            ).value.trim(),

        plataforma:
            document.getElementById(
                "gamePlataforma"
            ).value.trim(),

        stock:
            Number(
                document.getElementById(
                    "gameStock"
                ).value
            ),

        imagen:
            document.getElementById(
                "gameImagen"
            ).value.trim()

    };


    if (
        !game.nombre ||
        !game.descripcion ||
        !game.genero ||
        !game.plataforma
    ) {

        alert(
            "Completa todos los campos obligatorios."
        );

        return;
    }


    if (
        isNaN(game.precio) ||
        game.precio < 0
    ) {

        alert(
            "El precio no es válido."
        );

        return;
    }


    if (
        isNaN(game.stock) ||
        game.stock < 0
    ) {

        alert(
            "El stock no es válido."
        );

        return;
    }


    try {

        const response =
            await fetch(
                ADMIN_API +
                "?action=games&game_action=create",
                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/json",

                        "Authorization":
                            "Bearer " + token

                    },

                    body:
                        JSON.stringify(game)

                }
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            alert(
                data.message ||
                "No se pudo crear el videojuego."
            );

            return;
        }


        alert(
            "Videojuego creado correctamente."
        );


        const formContainer =
            document.getElementById(
                "newGameFormContainer"
            );


        if (formContainer) {

            formContainer.remove();
        }


        cargarVideojuegos();


    } catch (error) {

        console.error(error);


        alert(
            "No se pudo conectar con el servidor."
        );
    }
}


/* =====================================================
   EDITAR VIDEOJUEGO
===================================================== */

async function editarVideojuego(gameId) {

    const token =
        localStorage.getItem(
            "token"
        );


    if (!token) {

        window.location.href =
            "login.html";

        return;
    }


    try {

        const response =
            await fetch(
                ADMIN_API +
                "?action=games&game_action=one&id=" +
                gameId
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            alert(
                data.message ||
                "No se pudo obtener el videojuego."
            );

            return;
        }


        const game =
            data.game ||
            data.data;


        if (!game) {

            alert(
                "No se encontró el videojuego."
            );

            return;
        }


        const nombre =
            prompt(
                "Nombre:",
                game.nombre
            );


        if (nombre === null) {

            return;
        }


        const descripcion =
            prompt(
                "Descripción:",
                game.descripcion
            );


        if (descripcion === null) {

            return;
        }


        const precio =
            prompt(
                "Precio:",
                game.precio
            );


        if (precio === null) {

            return;
        }


        const genero =
            prompt(
                "Género:",
                game.genero
            );


        if (genero === null) {

            return;
        }


        const plataforma =
            prompt(
                "Plataforma:",
                game.plataforma
            );


        if (plataforma === null) {

            return;
        }


        const stock =
            prompt(
                "Stock:",
                game.stock
            );


        if (stock === null) {

            return;
        }


        const imagen =
            prompt(
                "Imagen:",
                game.imagen || ""
            );


        if (imagen === null) {

            return;
        }


        const updatedGame = {

            nombre:
                nombre.trim(),

            descripcion:
                descripcion.trim(),

            precio:
                Number(precio),

            genero:
                genero.trim(),

            plataforma:
                plataforma.trim(),

            stock:
                Number(stock),

            imagen:
                imagen.trim()

        };


        if (
            !updatedGame.nombre ||
            !updatedGame.descripcion ||
            !updatedGame.genero ||
            !updatedGame.plataforma
        ) {

            alert(
                "Los campos obligatorios no pueden estar vacíos."
            );

            return;
        }


        if (
            isNaN(updatedGame.precio) ||
            updatedGame.precio < 0
        ) {

            alert(
                "El precio no es válido."
            );

            return;
        }


        if (
            isNaN(updatedGame.stock) ||
            updatedGame.stock < 0
        ) {

            alert(
                "El stock no es válido."
            );

            return;
        }


        const updateResponse =
            await fetch(
                ADMIN_API +
                "?action=games&game_action=update&id=" +
                gameId,
                {

                    method: "PUT",

                    headers: {

                        "Content-Type":
                            "application/json",

                        "Authorization":
                            "Bearer " + token

                    },

                    body:
                        JSON.stringify(
                            updatedGame
                        )

                }
            );


        const updateData =
            await updateResponse.json();


        if (
            !updateResponse.ok ||
            !updateData.success
        ) {

            alert(
                updateData.message ||
                "No se pudo actualizar el videojuego."
            );

            return;
        }


        alert(
            "Videojuego actualizado correctamente."
        );


        cargarVideojuegos();


    } catch (error) {

        console.error(error);


        alert(
            "No se pudo conectar con el servidor."
        );
    }
}


/* =====================================================
   ELIMINAR VIDEOJUEGO
===================================================== */

async function eliminarVideojuego(gameId) {

    const confirmar =
        confirm(
            "¿Estás seguro de eliminar este videojuego?"
        );


    if (!confirmar) {

        return;
    }


    const token =
        localStorage.getItem(
            "token"
        );


    if (!token) {

        window.location.href =
            "login.html";

        return;
    }


    try {

        const response =
            await fetch(
                ADMIN_API +
                "?action=games&game_action=delete&id=" +
                gameId,
                {

                    method: "DELETE",

                    headers: {

                        "Authorization":
                            "Bearer " + token

                    }

                }
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            alert(
                data.message ||
                "No se pudo eliminar el videojuego."
            );

            return;
        }


        alert(
            "Videojuego eliminado correctamente."
        );


        cargarVideojuegos();


    } catch (error) {

        console.error(error);


        alert(
            "No se pudo conectar con el servidor."
        );
    }
}


/* =====================================================
   USUARIOS
===================================================== */


/* =====================================================
   CARGAR USUARIOS
===================================================== */

async function cargarUsuarios() {

    const container =
        document.getElementById(
            "usersAdminContainer"
        );


    if (!container) {

        return;
    }


    container.innerHTML =
        '<div class="loading">' +
        'Cargando usuarios...' +
        '</div>';


    const token =
        localStorage.getItem(
            "token"
        );


    if (!token) {

        window.location.href =
            "login.html";

        return;
    }


    try {

        const response =
            await fetch(
                ADMIN_API +
                "?action=users&user_action=all",
                {

                    method: "GET",

                    headers: {

                        "Authorization":
                            "Bearer " + token

                    }

                }
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            container.innerHTML =
                '<div class="empty-store">' +
                (
                    data.message ||
                    "No se pudieron cargar los usuarios."
                ) +
                '</div>';

            return;
        }


        const users =
            data.users ||
            data.data ||
            [];


        mostrarUsuarios(users);


    } catch (error) {

        console.error(error);


        container.innerHTML =
            '<div class="empty-store">' +
            'No se pudo conectar con el servidor.' +
            '</div>';
    }
}


/* =====================================================
   MOSTRAR USUARIOS
===================================================== */

function mostrarUsuarios(users) {

    const container =
        document.getElementById(
            "usersAdminContainer"
        );


    if (
        !users ||
        users.length === 0
    ) {

        container.innerHTML =
            '<div class="empty-store">' +
            'No hay usuarios registrados.' +
            '</div>';

        return;
    }


    let html = `

        <table class="admin-table">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nombre</th>

                    <th>Email</th>

                    <th>Rol</th>

                    <th>Acciones</th>

                </tr>

            </thead>

            <tbody>

    `;


    users.forEach(function (user) {

        const rol =
            user.rol ||
            "usuario";


        const rolClass =
            rol === "admin"
                ? "badge-admin"
                : "badge-user";


        html += `

            <tr>

                <td>
                    ${user.id}
                </td>

                <td>

                    <strong>
                        ${escapeHTML(
                            user.nombre
                        )}
                    </strong>

                </td>

                <td>
                    ${escapeHTML(
                        user.email
                    )}
                </td>

                <td>

                    <span class="badge ${rolClass}">
                        ${escapeHTML(rol)}
                    </span>

                </td>

                <td>

                    <div class="admin-actions">

                        <button
                            class="btn-small btn-edit"
                            onclick="editarUsuario(${user.id})"
                        >
                            Editar
                        </button>

                        <button
                            class="btn-small btn-delete"
                            onclick="eliminarUsuario(${user.id})"
                        >
                            Eliminar
                        </button>

                    </div>

                </td>

            </tr>

        `;

    });


    html += `

            </tbody>

        </table>

    `;


    container.innerHTML =
        html;
}


/* =====================================================
   EDITAR USUARIO
===================================================== */

async function editarUsuario(userId) {

    const token =
        localStorage.getItem(
            "token"
        );


    if (!token) {

        window.location.href =
            "login.html";

        return;
    }


    try {

        const response =
            await fetch(
                ADMIN_API +
                "?action=users&user_action=one&id=" +
                userId,
                {

                    method: "GET",

                    headers: {

                        "Authorization":
                            "Bearer " + token

                    }

                }
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            alert(
                data.message ||
                "No se pudo obtener el usuario."
            );

            return;
        }


        const user =
            data.user ||
            data.data;


        if (!user) {

            alert(
                "No se encontró el usuario."
            );

            return;
        }


        const nombre =
            prompt(
                "Nombre:",
                user.nombre
            );


        if (nombre === null) {

            return;
        }


        const email =
            prompt(
                "Email:",
                user.email
            );


        if (email === null) {

            return;
        }


        const rol =
            prompt(
                "Rol (usuario/admin):",
                user.rol
            );


        if (rol === null) {

            return;
        }


        const nuevoNombre =
            nombre.trim();


        const nuevoEmail =
            email.trim();


        const nuevoRol =
            rol.trim().toLowerCase();


        if (
            !nuevoNombre ||
            !nuevoEmail
        ) {

            alert(
                "El nombre y el email son obligatorios."
            );

            return;
        }


        if (
            nuevoRol !== "usuario" &&
            nuevoRol !== "admin"
        ) {

            alert(
                "El rol debe ser usuario o admin."
            );

            return;
        }


        const updatedUser = {

            nombre:
                nuevoNombre,

            email:
                nuevoEmail,

            rol:
                nuevoRol

        };


        const updateResponse =
            await fetch(
                ADMIN_API +
                "?action=users&user_action=update&id=" +
                userId,
                {

                    method: "PUT",

                    headers: {

                        "Content-Type":
                            "application/json",

                        "Authorization":
                            "Bearer " + token

                    },

                    body:
                        JSON.stringify(
                            updatedUser
                        )

                }
            );


        const updateData =
            await updateResponse.json();


        if (
            !updateResponse.ok ||
            !updateData.success
        ) {

            alert(
                updateData.message ||
                "No se pudo actualizar el usuario."
            );

            return;
        }


        alert(
            "Usuario actualizado correctamente."
        );


        cargarUsuarios();


    } catch (error) {

        console.error(error);


        alert(
            "No se pudo conectar con el servidor."
        );
    }
}


/* =====================================================
   ELIMINAR USUARIO
===================================================== */

async function eliminarUsuario(userId) {

    const userData =
        localStorage.getItem(
            "user"
        );


    let currentUser = null;


    try {

        currentUser =
            JSON.parse(userData);

    } catch (error) {

        console.error(error);
    }


    /*
     * Evitamos que el administrador
     * elimine accidentalmente su propia cuenta.
     */

    if (
        currentUser &&
        Number(currentUser.id) ===
        Number(userId)
    ) {

        alert(
            "No puedes eliminar la cuenta de administrador con la que estás conectado."
        );

        return;
    }


    const confirmar =
        confirm(
            "¿Estás seguro de eliminar este usuario?"
        );


    if (!confirmar) {

        return;
    }


    const token =
        localStorage.getItem(
            "token"
        );


    if (!token) {

        window.location.href =
            "login.html";

        return;
    }


    try {

        const response =
            await fetch(
                ADMIN_API +
                "?action=users&user_action=delete&id=" +
                userId,
                {

                    method: "DELETE",

                    headers: {

                        "Authorization":
                            "Bearer " + token

                    }

                }
            );


        const data =
            await response.json();


        if (
            !response.ok ||
            !data.success
        ) {

            alert(
                data.message ||
                "No se pudo eliminar el usuario."
            );

            return;
        }


        alert(
            "Usuario eliminado correctamente."
        );


        cargarUsuarios();


    } catch (error) {

        console.error(error);


        alert(
            "No se pudo conectar con el servidor."
        );
    }
}


/* =====================================================
   LOGOUT
===================================================== */

function configurarLogout() {

    const logout =
        document.getElementById(
            "adminLogout"
        );


    if (!logout) {

        return;
    }


    logout.addEventListener(
        "click",
        function (event) {

            event.preventDefault();


            localStorage.removeItem(
                "token"
            );


            localStorage.removeItem(
                "user"
            );


            window.location.href =
                "login.html";

        }
    );
}


/* =====================================================
   ESCAPAR HTML
===================================================== */

function escapeHTML(value) {

    if (
        value === null ||
        value === undefined
    ) {

        return "";
    }


    return String(value)

        .replace(
            /&/g,
            "&amp;"
        )

        .replace(
            /</g,
            "&lt;"
        )

        .replace(
            />/g,
            "&gt;"
        )

        .replace(
            /"/g,
            "&quot;"
        )

        .replace(
            /'/g,
            "&#039;"
        );
}
