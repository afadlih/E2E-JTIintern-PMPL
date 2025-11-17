const api = axios.create({
    baseURL: "/api",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
    },
    withCredentials: true,
});

function loadDosenData(page = 1) {
    api.get("/dosen", { params: { page } })
        .then(function (response) {
            const tableBody = document.getElementById("dosen-table-body");
            tableBody.innerHTML = "";

            if (response.data.success && response.data.data.length > 0) {
                response.data.data.forEach((dosen) => {
                    tableBody.innerHTML += `
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-0 text-sm">${
                                            dosen.nama_dosen
                                        }</h6>
                                        <span class="text-secondary text-xs">${
                                            dosen.nip
                                        }</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-sm ${
                                        dosen.perusahaan
                                            ? "text-success"
                                            : "text-muted"
                                    }">
                                        ${
                                            dosen.perusahaan?.nama_perusahaan ||
                                            "Belum ditugaskan"
                                        }
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary" onclick="detailDosen(${
                                        dosen.id_dosen
                                    })">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        `;
                });
            } else {
                tableBody.innerHTML = `
                        <tr>
                            <td colspan="4">
                                <div class="text-center py-4">
                                    <div class="empty-state-icon mb-3">
                                        <i class="bi bi-person-x" style="font-size: 3rem; color: #8898aa;"></i>
                                    </div>
                                    <h6 class="text-muted">Tidak ada data dosen</h6>
                                </div>
                            </td>
                        </tr>
                    `;
            }
        })
        .catch(function (error) {
            console.error("Error:", error);
            const tableBody = document.getElementById("dosen-table-body");
            tableBody.innerHTML = `
                    <tr>
                        <td colspan="4">
                            <div class="text-center py-4">
                                <div class="empty-state-icon mb-3">
                                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #5e72e4;"></i>
                                </div>
                                <h6 class="text-danger">Gagal memuat data dosen</h6>
                            </div>
                        </td>
                    </tr>
                `;
        });
}
// Load data when page loads
document.addEventListener("DOMContentLoaded", function () {
    loadDosenData();
});
