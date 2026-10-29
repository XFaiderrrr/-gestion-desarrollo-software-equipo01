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

        cargarCompras();

        cargarDashboard();

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

/* =====================================================
   COMPRAS
===================================================== */


async function cargarCompras() {
    const container = document.getElementById("purchasesAdminContainer");

    if (!container) return;

    container.innerHTML =
        '<div class="loading">Cargando compras...</div>';

    const token = localStorage.getItem("token");

    if (!token) {
        window.location.href = "login.html";
        return;
    }

    try {
        const headers = {
            "Authorization": "Bearer " + token
        };

        const [purchasesResponse, gamesResponse, usersResponse] =
            await Promise.all([
                fetch(
                    ADMIN_API + "?action=purchases&purchase_action=all",
                    { method: "GET", headers }
                ),
                fetch(
                    ADMIN_API + "?action=games&game_action=all"
                ),
                fetch(
                    ADMIN_API + "?action=users&user_action=all",
                    { method: "GET", headers }
                )
            ]);

        const [purchasesData, gamesData, usersData] =
            await Promise.all([
                purchasesResponse.json(),
                gamesResponse.json(),
                usersResponse.json()
            ]);

        if (!purchasesResponse.ok || !purchasesData.success) {
            throw new Error(
                purchasesData.message || "No se pudieron cargar las compras."
            );
        }

        if (!gamesResponse.ok || !gamesData.success) {
            throw new Error(
                gamesData.message || "No se pudieron cargar los videojuegos."
            );
        }

        if (!usersResponse.ok || !usersData.success) {
            throw new Error(
                usersData.message || "No se pudieron cargar los usuarios."
            );
        }

        const purchases = purchasesData.purchases || [];
        const games = gamesData.games || gamesData.data || [];
        const users = usersData.users || usersData.data || [];

        mostrarCompras(purchases, games, users);

    } catch (error) {
        console.error("Error al cargar compras:", error);

        container.innerHTML =
            '<div class="empty-store">' +
            escapeHTML(
                error.message || "No se pudieron cargar los datos de las compras."
            ) +
            '</div>';
    }
}



/* =====================================================
   MOSTRAR COMPRAS
===================================================== */


function mostrarCompras(purchases, games, users) {
    const container = document.getElementById("purchasesAdminContainer");

    if (!container) return;

    if (!purchases || purchases.length === 0) {
        container.innerHTML =
            '<div class="empty-store">Todavía no hay compras registradas.</div>';
        return;
    }

    games = games || [];
    users = users || [];

    let html = `
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID compra</th>
                    <th>Comprador</th>
                    <th>Correo</th>
                    <th>Videojuego</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
    `;

    purchases.forEach(function (purchase) {
        const user = users.find(function (item) {
            return Number(item.auth_user_id) ===
                Number(purchase.auth_user_id);
        });

        const game = games.find(function (item) {
            return Number(item.id) === Number(purchase.game_id);
        });

        const comprador = user
            ? user.nombre
            : "Usuario #" + purchase.auth_user_id;

        const correo = user
            ? user.email
            : "No disponible";

        const videojuego = game
            ? game.nombre
            : "Videojuego #" + purchase.game_id;

        const total = Number(purchase.total || 0);
        const precio = Number(purchase.precio_unitario || 0);
        const cantidad = Number(purchase.cantidad || 0);
        const estado = purchase.estado || "Sin estado";

        const fecha = purchase.created_at
            ? new Date(
                String(purchase.created_at).replace(" ", "T")
            ).toLocaleString("es-MX")
            : "No disponible";

        html += `
            <tr>
                <td>${escapeHTML(purchase.id)}</td>
                <td><strong>${escapeHTML(comprador)}</strong></td>
                <td>${escapeHTML(correo)}</td>
                <td>${escapeHTML(videojuego)}</td>
                <td>${cantidad}</td>
                <td>$${precio.toFixed(2)}</td>
                <td><strong>$${total.toFixed(2)}</strong></td>
                <td>${escapeHTML(estado)}</td>
                <td>${escapeHTML(fecha)}</td>
            </tr>
        `;
    });

    html += `
            </tbody>
        </table>
    `;

    container.innerHTML = html;
}


/* =====================================================
   DASHBOARD ADMINISTRATIVO
===================================================== */

async function cargarDashboard() {
    const token = localStorage.getItem("token");

    const statsGames = document.getElementById("statsGames");
    const statsUsers = document.getElementById("statsUsers");
    const statsPurchases = document.getElementById("statsPurchases");
    const statsSales = document.getElementById("statsSales");
    const chartMessage = document.getElementById("salesChartMessage");

    if (!statsGames || !statsUsers || !statsPurchases || !statsSales) {
        return;
    }

    try {
        const headers = token
            ? { "Authorization": "Bearer " + token }
            : {};

        const [gamesResponse, usersResponse, purchasesResponse] =
            await Promise.all([
                fetch(ADMIN_API + "?action=games&game_action=all"),
                fetch(ADMIN_API + "?action=users&user_action=all", {
                    method: "GET",
                    headers: headers
                }),
                fetch(ADMIN_API + "?action=purchases&purchase_action=all", {
                    method: "GET",
                    headers: headers
                })
            ]);

        if (!gamesResponse.ok || !usersResponse.ok || !purchasesResponse.ok) {
            throw new Error("No se pudieron consultar todas las estadísticas.");
        }

        const [gamesData, usersData, purchasesData] = await Promise.all([
            gamesResponse.json(),
            usersResponse.json(),
            purchasesResponse.json()
        ]);

        if (gamesData.success === false ||
            usersData.success === false ||
            purchasesData.success === false) {
            throw new Error("La API devolvió un error al consultar las estadísticas.");
        }

        const games = gamesData.games || gamesData.data || [];
        const users = usersData.users || usersData.data || [];
        const purchases = purchasesData.purchases || purchasesData.data || [];

        const totalSales = purchases.reduce(function (sum, purchase) {
            return sum + Number(purchase.total || 0);
        }, 0);

        statsGames.textContent = games.length.toLocaleString("es-MX");
        statsUsers.textContent = users.length.toLocaleString("es-MX");
        statsPurchases.textContent = purchases.length.toLocaleString("es-MX");

        statsSales.textContent = totalSales.toLocaleString("es-MX", {
            style: "currency",
            currency: "MXN"
        });

        dibujarGraficaVentas(purchases);

        if (chartMessage) {
            chartMessage.textContent = purchases.length
                ? "Importes agrupados por fecha de compra."
                : "Todavía no hay compras para mostrar en la gráfica.";
        }
    } catch (error) {
        console.error("Error al cargar el dashboard:", error);

        statsGames.textContent = "—";
        statsUsers.textContent = "—";
        statsPurchases.textContent = "—";
        statsSales.textContent = "—";

        if (chartMessage) {
            chartMessage.textContent =
                "No fue posible cargar las estadísticas. Revisa la conexión y los permisos.";
        }
    }
}


/* =====================================================
   GRÁFICA DE VENTAS
===================================================== */

function dibujarGraficaVentas(purchases) {
    const canvas = document.getElementById("salesChart");

    if (!canvas) return;

    const container = canvas.parentElement;
    const ctx = canvas.getContext("2d");

    if (!ctx || !container) return;

    function dibujar() {
        const width = container.clientWidth;
        const height = container.clientHeight;

        if (!width || !height) return;

        const pixelRatio = window.devicePixelRatio || 1;

        canvas.width = Math.round(width * pixelRatio);
        canvas.height = Math.round(height * pixelRatio);

        ctx.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        ctx.clearRect(0, 0, width, height);

        const styles = getComputedStyle(document.body);
        const textColor = "#94a3b8";
        const gridColor = "#263449";
        const lineColor = "#60a5fa";

        const ventasPorFecha = {};

        purchases.forEach(function (purchase) {
            if (!purchase.created_at) return;

            const fechaTexto = String(purchase.created_at).slice(0, 10);

            if (!/^\d{4}-\d{2}-\d{2}$/.test(fechaTexto)) return;

            const total = Number(purchase.total || 0);

            if (!Number.isFinite(total)) return;

            ventasPorFecha[fechaTexto] =
                (ventasPorFecha[fechaTexto] || 0) + total;
        });

        const fechas = Object.keys(ventasPorFecha).sort();

        const margen = { top: 20, right: 20, bottom: 42, left: 65 };
        const chartWidth = width - margen.left - margen.right;
        const chartHeight = height - margen.top - margen.bottom;

        if (chartWidth <= 0 || chartHeight <= 0) return;

        ctx.font = "12px Arial";
        ctx.textAlign = "right";
        ctx.textBaseline = "middle";

        ctx.fillStyle = textColor;
        ctx.strokeStyle = gridColor;
        ctx.lineWidth = 1;

        if (!fechas.length) {
            ctx.textAlign = "center";
            ctx.fillText(
                "Sin datos de ventas por fecha",
                width / 2,
                height / 2
            );
            return;
        }

        const maxVenta = Math.max(
            1,
            ...fechas.map(function (fecha) {
                return ventasPorFecha[fecha];
            })
        );

        const maxEscala = maxVenta * 1.15;
        const divisiones = 4;

        for (let i = 0; i <= divisiones; i++) {
            const valor = maxEscala * (1 - i / divisiones);
            const y = margen.top + chartHeight * i / divisiones;

            ctx.beginPath();
            ctx.moveTo(margen.left, y);
            ctx.lineTo(width - margen.right, y);
            ctx.stroke();

            ctx.fillStyle = textColor;
            ctx.textAlign = "right";
            ctx.fillText(
                "$" + valor.toLocaleString("es-MX", {
                    maximumFractionDigits: 0
                }),
                margen.left - 10,
                y
            );
        }

        const puntos = fechas.map(function (fecha, index) {
            const x = fechas.length === 1
                ? margen.left + chartWidth / 2
                : margen.left + index * chartWidth / (fechas.length - 1);

            const y = margen.top +
                chartHeight *
                (1 - ventasPorFecha[fecha] / maxEscala);

            return { x: x, y: y, fecha: fecha };
        });

        ctx.beginPath();

        puntos.forEach(function (punto, index) {
            if (index === 0) {
                ctx.moveTo(punto.x, punto.y);
            } else {
                ctx.lineTo(punto.x, punto.y);
            }
        });

        ctx.strokeStyle = lineColor;
        ctx.lineWidth = 3;
        ctx.stroke();

        puntos.forEach(function (punto, index) {
            ctx.beginPath();
            ctx.arc(punto.x, punto.y, 4, 0, Math.PI * 2);
            ctx.fillStyle = lineColor;
            ctx.fill();

            if (
                fechas.length <= 10 ||
                index === 0 ||
                index === fechas.length - 1 ||
                index % Math.ceil(fechas.length / 6) === 0
            ) {
                const partes = punto.fecha.split("-");
                const etiqueta = partes[2] + "/" + partes[1];

                ctx.fillStyle = textColor;
                ctx.textAlign = "center";
                ctx.textBaseline = "top";
                ctx.fillText(
                    etiqueta,
                    punto.x,
                    height - margen.bottom + 12
                );
            }
        });
    }

    dibujar();

    if (canvas._dashboardResizeHandler) {
        window.removeEventListener(
            "resize",
            canvas._dashboardResizeHandler
        );
    }

    canvas._dashboardResizeHandler = dibujar;
    window.addEventListener("resize", dibujar);
}
