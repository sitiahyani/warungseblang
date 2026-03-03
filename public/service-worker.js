const CACHE_NAME = "wp-seblang-v3";

self.addEventListener("install", (event) => {
    self.skipWaiting();
});

self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                }),
            );
        }),
    );
    self.clients.claim();
});

self.addEventListener("fetch", (event) => {
    if (event.request.method !== "GET") return;

    const requestURL = new URL(event.request.url);

    // Kalau file static (css, js, image)
    if (
        requestURL.pathname.endsWith(".css") ||
        requestURL.pathname.endsWith(".js") ||
        requestURL.pathname.endsWith(".png") ||
        requestURL.pathname.endsWith(".jpg") ||
        requestURL.pathname.endsWith(".jpeg") ||
        requestURL.pathname.endsWith(".svg")
    ) {
        event.respondWith(
            caches.match(event.request).then((response) => {
                return (
                    response ||
                    fetch(event.request).then((fetchRes) => {
                        return caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, fetchRes.clone());
                            return fetchRes;
                        });
                    })
                );
            }),
        );
        return;
    }

    // Untuk halaman HTML
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                const clone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, clone);
                });
                return response;
            })
            .catch(() => {
                return caches.match(event.request);
            }),
    );
});
