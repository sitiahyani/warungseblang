const CACHE_NAME = "wp-seblang-v2";
const DB_NAME = "wp-seblang-db";

/* FILE YANG DI CACHE */
const ASSETS = [
    "/",
    "/kasir",
    "/offline.html",

    /* CDN */
    "https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css",
    "https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css",
    "https://cdn.jsdelivr.net/npm/bootstrap@4.6/dist/js/bootstrap.bundle.min.js",
    "https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js",
    "https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js",

    /* LOCAL JS */
    "/js/offline-pos.js",
];

/* ================= INSTALL ================= */
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS)),
    );

    self.skipWaiting();
});

/* ================= ACTIVATE ================= */
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
});

/* ================= FETCH ================= */
self.addEventListener("fetch", (event) => {
    const request = event.request;

    /* ================= SIMPAN TRANSAKSI OFFLINE ================= */

    if (request.method === "POST") {
        event.respondWith(
            fetch(request.clone()).catch(async () => {
                const body = await request.clone().json();

                await saveTransaction(body);

                return new Response(
                    JSON.stringify({
                        success: false,
                        offline: true,
                        message: "disimpan offline",
                    }),

                    { headers: { "Content-Type": "application/json" } },
                );
            }),
        );

        return;
    }

    /* ================= HTML PAGE CACHE ================= */

    if (request.destination === "document") {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const clone = response.clone();

                    caches
                        .open(CACHE_NAME)
                        .then((cache) => cache.put(request, clone));

                    return response;
                })

                .catch(() => {
                    return caches
                        .match(request)
                        .then((res) => res || caches.match("/offline.html"));
                }),
        );

        return;
    }

    /* ================= STATIC FILE CACHE ================= */

    if (
        request.destination === "style" ||
        request.destination === "script" ||
        request.destination === "image" ||
        request.destination === "font"
    ) {
        event.respondWith(
            caches.match(request).then((res) => {
                return (
                    res ||
                    fetch(request).then((fetchRes) => {
                        return caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, fetchRes.clone());

                            return fetchRes;
                        });
                    })
                );
            }),
        );

        return;
    }
});

/* ================= BACKGROUND SYNC ================= */

self.addEventListener("sync", (event) => {
    if (event.tag === "sync-transactions") {
        event.waitUntil(syncData());
    }
});

/* ================= INDEXEDDB ================= */

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);

        request.onupgradeneeded = (e) => {
            const db = e.target.result;

            if (!db.objectStoreNames.contains("transactions")) {
                db.createObjectStore("transactions", { autoIncrement: true });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/* ================= SIMPAN TRANSAKSI ================= */

async function saveTransaction(data) {
    const db = await openDB();

    const tx = db.transaction("transactions", "readwrite");

    const store = tx.objectStore("transactions");

    store.add(data);
}

/* ================= SYNC DATA ================= */

async function syncData() {
    const db = await openDB();

    const tx = db.transaction("transactions", "readwrite");

    const store = tx.objectStore("transactions");

    const allData = await new Promise((resolve) => {
        const req = store.getAll();

        req.onsuccess = () => resolve(req.result);
    });

    for (const data of allData) {
        await fetch("/api/sync-transaksi", {
            method: "POST",

            headers: {
                "Content-Type": "application/json",
            },

            body: JSON.stringify(data),
        });
    }

    store.clear();
}
