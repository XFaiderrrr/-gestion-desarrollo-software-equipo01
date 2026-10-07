if ("serviceWorker" in navigator) {
    window.addEventListener("load", function () {
        navigator.serviceWorker.register("./service-worker.js")
            .then(function (registration) {
                console.log(
                    "Service Worker registrado correctamente:",
                    registration.scope
                );
            })
            .catch(function (error) {
                console.error(
                    "Error al registrar el Service Worker:",
                    error
                );
            });
    });
}