const STORE_API =
    "http://localhost/-gestion-desarrollo-software-equipo01/api-gateway/public/";


document.addEventListener(
    "DOMContentLoaded",
    function () {

        cargarUsuario();

        cargarVideojuegos();

        configurarLogout();

    }
);


/* =====================================================
   USUARIO
===================================================== */

function cargarUsuario() {

    const userName =
        document.getElementById("userName");

    const userData =
        localStorage.getItem("user");

    if (!userData) {

        window.location.href =
            "login.html";

        return;
    }

    try {

        const user =
            JSON.parse(userData);

        userName.textContent =
            user.nombre || "Usuario";

    } catch (error) {

        console.error(error);

        localStorage.removeItem("user");

        localStorage.removeItem("token");

        window.location.href =
            "login.html";
    }
}


/* =====================================================
   CARGAR VIDEOJUEGOS
===================================================== */

async function cargarVideojuegos() {

    const container =
        document.getElementById(
            "gamesContainer"
        );

    try {

        const response =
            await fetch(
                STORE_API +
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
                'No se pudo cargar el catálogo.' +
                '</div>';

            return;
        }

        mostrarVideojuegos(
            data.games || data.data || []
        );

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
            "gamesContainer"
        );

    if (
        !games ||
        games.length === 0
    ) {

        container.innerHTML =
            '<div class="empty-store">' +
            'No hay videojuegos disponibles.' +
            '</div>';

        return;
    }

    container.innerHTML = "";

    games.forEach(function (game) {

        const card =
            document.createElement("article");

        card.className =
            "game-card";


        const stock =
            Number(game.stock);


        const price =
            Number(game.precio);


        const sinStock =
            stock <= 0;


        card.innerHTML = `

            <div class="game-image">
                🎮
            </div>

            <div class="game-content">

                <h2>
                    ${escapeHTML(game.nombre)}
                </h2>

                <p class="game-description">
                    ${escapeHTML(game.descripcion)}
                </p>

                <div class="game-meta">

                    ${escapeHTML(game.genero)}
                    ·
                    ${escapeHTML(game.plataforma)}

                </div>

                <div class="game-info">

                    <span class="game-price">
                        $${price.toFixed(2)}
                    </span>

                    <span class="game-stock">
                        Stock: ${stock}
                    </span>

                </div>

                <button
                    class="btn btn-primary buy-button"
                    data-game-id="${game.id}"
                    ${sinStock ? "disabled" : ""}
                >
                    ${sinStock ? "Agotado" : "Comprar"}
                </button>

            </div>

        `;


        const button =
            card.querySelector(
                ".buy-button"
            );


        if (button) {

            button.addEventListener(
                "click",
                function () {

                    comprarVideojuego(
                        game.id
                    );

                }
            );

        }


        container.appendChild(card);

    });
}


/* =====================================================
   COMPRA
===================================================== */

async function comprarVideojuego(gameId) {

    const token =
        localStorage.getItem("token");


    if (!token) {

        window.location.href =
            "login.html";

        return;
    }


    const confirmar =
        confirm(
            "¿Deseas comprar este videojuego?"
        );


    if (!confirmar) {

        return;
    }


    try {

        const response =
            await fetch(
                STORE_API +
                "?action=purchases&purchase_action=create",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json",

                        "Authorization":
                            "Bearer " + token
                    },

                    body: JSON.stringify({
                        game_id: gameId,
                        cantidad: 1
                    })
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
                "No se pudo realizar la compra."
            );

            return;
        }


        alert(
            "¡Compra realizada correctamente!"
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
   LOGOUT
===================================================== */

function configurarLogout() {

    const logoutButton =
        document.getElementById(
            "logoutButton"
        );


    if (!logoutButton) {

        return;
    }


    logoutButton.addEventListener(
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
   SEGURIDAD BÁSICA
===================================================== */

function escapeHTML(value) {

    if (value === null ||
        value === undefined) {

        return "";
    }

    return String(value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}