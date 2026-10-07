const CACHE_NAME = "bugistore-v1";

const FILES_TO_CACHE = [
    "./",
    "./index.html",
    "./login.html",
    "./registro.html",
    "./tienda.html",
    "./admin.html",

    "./manifest.json",

    "./css/styles.css",

    "./js/auth.js",
    "./js/tienda.js",
    "./js/admin.js",
    "./js/pwa.js",

    "./images/icons/icon-192.png",
    "./images/icons/icon-512.png"
];

self.addEventListener("install", function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function (cache) {
                return cache.addAll(FILES_TO_CACHE);
            })
    );

    self.skipWaiting();
});

self.addEventListener("activate", function (event) {
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames
                    .filter(function (cacheName) {
                        return cacheName !== CACHE_NAME;
                    })
                    .map(function (cacheName) {
                        return caches.delete(cacheName);
                    })
            );
        }).then(function () {
            return self.clients.claim();
        })
    );
});

self.addEventListener("fetch", function (event) {
    const request = event.request;

    if (request.method !== "GET") {
        return;
    }

    const url = new URL(request.url);

    // No interceptar peticiones externas.
    if (url.origin !== self.location.origin) {
        return;
    }

    // No almacenar respuestas de los microservicios/API.
    if (url.pathname.includes("/api-gateway/")) {
        return;
    }

    // Solo manejamos recursos estáticos y navegación.
    const cacheableTypes = [
        "document",
        "style",
        "script",
        "image",
        "font"
    ];

    if (!cacheableTypes.includes(request.destination)) {
        return;
    }

    event.respondWith(
        caches.match(request)
            .then(function (cachedResponse) {

                if (cachedResponse) {
                    return cachedResponse;
                }

                return fetch(request)
                    .then(function (response) {

                        if (!response || !response.ok) {
                            return response;
                        }

                        const responseClone = response.clone();

                        caches.open(CACHE_NAME)
                            .then(function (cache) {
                                cache.put(request, responseClone);
                            });

                        return response;
                    })
                    .catch(function () {

                        // Si estamos navegando y no hay conexión,
                        // mostrar la página principal almacenada.
                        if (request.mode === "navigate") {
                            return caches.match("./index.html");
                        }

                        return Response.error();
                    });
            })
    );
});