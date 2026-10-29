const STORE_API =
    "../api-gateway/public/";

document.addEventListener("DOMContentLoaded", function () {
    cargarUsuario();
    cargarVideojuegos();
    cargarMisCompras();
    configurarLogout();

    const reloadButton = document.getElementById("reloadPurchasesButton");

    if (reloadButton) {
        reloadButton.addEventListener("click", cargarMisCompras);
    }
});

/* =====================================================
USUARIO
===================================================== */

function cargarUsuario() {
    const userName = document.getElementById("userName");
    const profileName = document.getElementById("profileName");
    const profileEmail = document.getElementById("profileEmail");
    const userData = localStorage.getItem("user");

    if (!userData) {
        window.location.href = "login.html";
        return;
    }

    try {
        const user = JSON.parse(userData);

        const nombre = user.nombre || "Usuario";
        const email = user.email || user.correo || "Correo no disponible";

        if (userName) {
            userName.textContent = nombre;
        }

        if (profileName) {
            profileName.textContent = nombre;
        }

        if (profileEmail) {
            profileEmail.textContent = email;
        }
    } catch (error) {
        console.error("Error al cargar el usuario:", error);

        localStorage.removeItem("user");
        localStorage.removeItem("token");

        window.location.href = "login.html";
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
            'No se pudo cargar el catÃ¡logo.' +
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


    const imagen =
        game.imagen
            ? "images/games/" + escapeHTML(game.imagen)
            : "";


    card.innerHTML = `

        <div class="game-image">

            ${
                imagen
                    ? `
                        <img
                            src="${imagen}"
                            alt="${escapeHTML(game.nombre)}"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <div class="game-image-fallback">
                            ðŸŽ®
                        </div>
                      `
                    : `
                        <div class="game-image-fallback">
                            ðŸŽ®
                        </div>
                      `
            }

        </div>


        <div class="game-content">

            <div class="game-title-row">

                <h2>
                    ${escapeHTML(game.nombre)}
                </h2>

                ${
                    sinStock
                        ? `
                            <span class="stock-badge stock-empty">
                                Agotado
                            </span>
                          `
                        : `
                            <span class="stock-badge">
                                Disponible
                            </span>
                          `
                }

            </div>


            <p class="game-description">

                ${escapeHTML(
                    game.descripcion ||
                    "Sin descripciÃ³n disponible."
                )}

            </p>


            <div class="game-meta">

                <span>
                    ðŸŽ® ${escapeHTML(game.genero || "Sin gÃ©nero")}
                </span>

                <span>
                    ðŸ’» ${escapeHTML(game.plataforma || "Sin plataforma")}
                </span>

            </div>


            <div class="game-info">

                <span class="game-price">
                    $${price.toFixed(2)}
                </span>

                <span class="game-stock">
                    ${
                        sinStock
                            ? "Sin existencias"
                            : "Stock: " + stock
                    }
                </span>

            </div>


            <button
                class="btn btn-primary buy-button"
                data-game-id="${game.id}"
                ${sinStock ? "disabled" : ""}
            >
                ${
                    sinStock
                        ? "Agotado"
                        : "Comprar ahora"
                }
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
        "Â¿Deseas comprar este videojuego?"
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
        "Â¡Compra realizada correctamente!"
    );


    cargarVideojuegos();
cargarMisCompras();

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
SEGURIDAD BÃSICA
===================================================== */

function escapeHTML(value) {

if (
    value === null ||
    value === undefined
) {

    return "";
}

return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");

}

/* =====================================================
   MIS COMPRAS
===================================================== */

async function cargarMisCompras() {
    const container = document.getElementById("purchasesContainer");
    const token = localStorage.getItem("token");

    if (!container) {
        return;
    }

    if (!token) {
        container.innerHTML =
            '<p class="empty-store">Inicia sesiÃ³n para consultar tus compras.</p>';
        return;
    }

    container.innerHTML =
        '<p class="loading">Cargando historial de compras...</p>';

    try {
        const response = await fetch(
            STORE_API + "?action=purchases&purchase_action=my-purchases",
            {
                method: "GET",
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer " + token
                }
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            container.innerHTML =
                '<p class="empty-store">' +
                escapeHTML(data.message || "No se pudo cargar tu historial.") +
                '</p>';
            return;
        }

const purchases = data.purchases || [];
mostrarMisCompras(purchases);
actualizarEstadisticasPerfil(purchases);
    } catch (error) {
        console.error("Error al cargar las compras:", error);

        container.innerHTML =
            '<p class="empty-store">No se pudo conectar con el servidor.</p>';
    }
}

function mostrarMisCompras(purchases) {
    const container = document.getElementById("purchasesContainer");

    if (!container) {
        return;
    }

    if (!Array.isArray(purchases) || purchases.length === 0) {
        container.innerHTML =
            '<p class="empty-store">TodavÃ­a no tienes compras registradas.</p>';
        return;
    }

    const rows = purchases.map(function (purchase) {
        const cantidad = Number(purchase.cantidad) || 0;
        const precio = Number(purchase.precio_unitario) || 0;
        const total = Number(purchase.total) || 0;

        const nombre = purchase.game_nombre || "Videojuego no disponible";

        const fecha = purchase.created_at
            ? escapeHTML(purchase.created_at)
            : "No disponible";

        let imagenHTML = `
            <div class="purchase-game-image-fallback">
                Sin imagen
            </div>
        `;

        if (purchase.game_imagen) {
            const imagen = "images/games/" + escapeHTML(purchase.game_imagen);

            imagenHTML = `
                <img
                    class="purchase-game-image"
                    src="${imagen}"
                    alt="Portada de ${escapeHTML(nombre)}"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >
                <div class="purchase-game-image-fallback" style="display:none;">
                    Sin imagen
                </div>
            `;
        }

        return `
            <tr>
                <td>
                    <div class="purchase-game-cell">
                        ${imagenHTML}
                        <div class="purchase-game-details">
                            <strong>${escapeHTML(nombre)}</strong>
                            <span>ID: ${escapeHTML(purchase.game_id)}</span>
                            ${
                                purchase.game_plataforma
                                    ? `<span>${escapeHTML(purchase.game_plataforma)}</span>`
                                    : ""
                            }
                        </div>
                    </div>
                </td>
                <td>${cantidad}</td>
                <td>$${precio.toFixed(2)}</td>
                <td><strong>$${total.toFixed(2)}</strong></td>
                <td>
                    <span class="purchase-status">
                        ${escapeHTML(purchase.estado || "Sin estado")}
                    </span>
                </td>
                <td>${fecha}</td>
            </tr>
        `;
    }).join("");

    container.innerHTML = `
        <div class="purchases-table-wrapper">
            <table class="purchases-table">
                <thead>
                    <tr>
                        <th>Videojuego</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        </div>
    `;
}

function actualizarEstadisticasPerfil(purchases) {
    const purchasesElement = document.getElementById("profilePurchases");
    const unitsElement = document.getElementById("profileUnits");
    const spentElement = document.getElementById("profileSpent");
    const lastPurchaseElement = document.getElementById("profileLastPurchase");

    if (!Array.isArray(purchases)) {
        return;
    }

    // NÃºmero de registros de compra
    const totalCompras = purchases.length;

    // Unidades adquiridas
    const totalUnidades = purchases.reduce(function (sum, purchase) {
        return sum + (Number(purchase.cantidad) || 0);
    }, 0);

    // Importe acumulado de las compras
    const totalGastado = purchases.reduce(function (sum, purchase) {
        return sum + (Number(purchase.total) || 0);
    }, 0);

    // Fecha de la compra mÃ¡s reciente
    const comprasConFecha = purchases.filter(function (purchase) {
        return purchase.created_at &&
            !Number.isNaN(Date.parse(purchase.created_at));
    });

    comprasConFecha.sort(function (a, b) {
        return Date.parse(b.created_at) - Date.parse(a.created_at);
    });

    if (purchasesElement) {
        purchasesElement.textContent = totalCompras;
    }

    if (unitsElement) {
        unitsElement.textContent = totalUnidades;
    }

    if (spentElement) {
        spentElement.textContent = totalGastado.toLocaleString("es-MX", {
            style: "currency",
            currency: "MXN"
        });
    }

    if (lastPurchaseElement) {
        if (comprasConFecha.length > 0) {
            lastPurchaseElement.textContent =
                new Date(comprasConFecha[0].created_at).toLocaleDateString("es-MX");
        } else {
            lastPurchaseElement.textContent = "Sin compras";
        }
    }
}