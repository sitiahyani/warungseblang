const CACHE_NAME = "warung-seblang-v1";
const DB_NAME = "warungseblangPOS";

/* FILE CACHE */
const ASSETS = ["/", "/kasir", "/offline.html", "/js/offline-pos.js"];

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
        caches.keys().then((keys) =>
            Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                }),
            ),
        ),
    );
    self.clients.claim();
});

/* ================= FETCH CACHE ================= */
self.addEventListener("fetch", (event) => {
    const request = event.request;

    if (request.method === "GET") {
        event.respondWith(
            caches.match(request).then((cached) => {
                return (
                    cached ||
                    fetch(request)
                        .then((response) => {
                            const clone = response.clone();
                            caches.open(CACHE_NAME).then((cache) => {
                                cache.put(request, clone);
                            });
                            return response;
                        })
                        .catch(() => caches.match("/offline.html"))
                );
            }),
        );
    }
});

/* ================= BACKGROUND SYNC ================= */
self.addEventListener("sync", (event) => {
    if (event.tag === "sync-transactions") {
        event.waitUntil(syncTransactions());
    }
});

/* ================= OPEN DB ================= */
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);

        request.onupgradeneeded = (e) => {
            const db = e.target.result;

            if (!db.objectStoreNames.contains("transactions")) {
                db.createObjectStore("transactions", {
                    keyPath: "id",
                    autoIncrement: true,
                });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/* ================= SYNC TRANSAKSI ================= */
async function syncTransactions() {
    const db = await openDB();
    const tx = db.transaction("transactions", "readwrite");
    const store = tx.objectStore("transactions");

    const allData = await new Promise((resolve) => {
        const req = store.getAll();
        req.onsuccess = () => resolve(req.result);
    });

    for (const trx of allData) {
        const response = await fetch("/kasir/penjualan/simpan", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": trx.csrf_token,
            },
            body: JSON.stringify(trx),
        });

        if (!response.ok) return;
    }

    store.clear();
}
