
let db;
const DB_NAME = "warungseblangPOS";

/* ================= OPEN DB ================= */
const request = indexedDB.open(DB_NAME, 1);

request.onupgradeneeded = function (e) {
    db = e.target.result;

    if (!db.objectStoreNames.contains("transactions")) {
        db.createObjectStore("transactions", {
            keyPath: "id",
            autoIncrement: true,
        });
    }
};

request.onsuccess = function (e) {
    db = e.target.result;
    console.log("IndexedDB siap");
};

/* ================= SAVE OFFLINE ================= */
function saveOfflineTransaction(data) {
    if (!db) {
        alert("Database belum siap");
        return;
    }

    data.csrf_token = document.querySelector('meta[name="csrf-token"]').content;
    data.created_at = new Date().toISOString();

    const tx = db.transaction("transactions", "readwrite");
    const store = tx.objectStore("transactions");

    store.add(data);

    tx.oncomplete = () => {
        alert("Internet offline, transaksi disimpan ke IndexedDB");
    };
}

/* ================= HANDLE SUBMIT FORM ================= */
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("form-transaksi");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        if (!navigator.onLine) {
            e.preventDefault();

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            saveOfflineTransaction(data);
            form.reset();
            return false;
        }
    });
});

/* ================= AUTO SYNC ================= */
window.addEventListener("online", () => {
    navigator.serviceWorker.ready.then((sw) => {
        if (sw.sync) {
            sw.sync.register("sync-transactions");
        }
    });
});
