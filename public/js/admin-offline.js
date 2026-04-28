function simpanDataOfflineAdmin() {
  localStorage.setItem("admin_offline_data", JSON.stringify({
    waktu: new Date().toISOString(),
    pesan: "Data admin disimpan saat offline"
  }));

  alert("Data admin disimpan (offline)");
}
window.addEventListener("online", () => {
  const data = localStorage.getItem("admin_offline_data");

  if (data) {
    fetch("/api/sync-admin", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: data
    })
    .then(() => {
      localStorage.removeItem("admin_offline_data");
      alert("Data admin berhasil disinkronkan");
    });
  }
});
