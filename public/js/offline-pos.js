// =============================
// DATABASE
// =============================

let db;

const request = indexedDB.open("warungseblangPOS", 1);
request.onupgradeneeded = function (e) {
    db = e.target.result;

    const store = db.createObjectStore("transactions", {
        keyPath: "id",
        autoIncrement: true,
    });
};

request.onsuccess = function (e) {
    db = e.target.result;
    console.log("IndexedDB siap");
};

request.onerror = function () {
    console.log("IndexedDB error");
};

// =============================
// SIMPAN TRANSAKSI OFFLINE
// =============================

function saveOfflineTransaction(data) {
    const tx = db.transaction("transactions", "readwrite");
    const store = tx.objectStore("transactions");
    store.add(data);
    console.log("Transaksi disimpan offline");
}

// =============================
// AMBIL DATA OFFLINE
// =============================

function getOfflineTransactions() {
    return new Promise((resolve) => {
        const tx = db.transaction("transactions", "readonly");
        const store = tx.objectStore("transactions");
        const request = store.getAll();
        request.onsuccess = function () {
            resolve(request.result);
        };
    });
}

// =============================
// HAPUS DATA SETELAH SYNC
// =============================

function clearOfflineTransactions() {
    const tx = db.transaction("transactions", "readwrite");
    const store = tx.objectStore("transactions");
    store.clear();
}

// =============================
// SYNC KE SERVER
// =============================

async function syncTransactions() {
    const data = await getOfflineTransactions();

    if (data.length === 0) return;

    console.log("Sync transaksi offline");

    for (let trx of data) {
        await fetch("/kasir/penjualan/simpan", {
            method: "POST",

            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },

            body: JSON.stringify(trx),
        });
    }

    clearOfflineTransactions();
}

// =============================
// AUTO SYNC SAAT ONLINE
// =============================

window.addEventListener("online", () => {
    console.log("Internet kembali");

    syncTransactions();
});
