const CACHE_NAME = "wp-seblang-v1";
const URLS_TO_CACHE = [
    "/",
    "/login",
    "/dashboard",
    "/admin",
    "/kategori",
    "/karyawan",
    "/css/app.css",
    "/js/app.js",
];

// Install: simpan file ke cache
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(URLS_TO_CACHE)),
    );
});

// Fetch: ambil dari cache dulu, kalau nggak ada baru ke network
self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        }),
    );
});
