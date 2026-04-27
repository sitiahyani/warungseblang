let db;

const request = indexedDB.open("warungOfflineDB", 1);

request.onupgradeneeded = function (event) {
    db = event.target.result;

    const stores = [
        "barang",
        "pelanggan",
        "transactions",
        "stok_opname",
        "sync_queue"
    ];

    stores.forEach(storeName => {
        if (!db.objectStoreNames.contains(storeName)) {
            db.createObjectStore(storeName, {
                keyPath: "id",
                autoIncrement: true
            });
        }
    });
};

request.onsuccess = function (event) {
    db = event.target.result;
};

function saveToStore(storeName, data) {
    const tx = db.transaction(storeName, "readwrite");
    tx.objectStore(storeName).add(data);
}

function getAllFromStore(storeName) {
    return new Promise((resolve) => {
        const tx = db.transaction(storeName, "readonly");
        const req = tx.objectStore(storeName).getAll();
        req.onsuccess = () => resolve(req.result);
    });
}