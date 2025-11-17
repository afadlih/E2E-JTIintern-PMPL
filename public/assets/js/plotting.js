let currentPage = 1;
const perPage = 10;

// Initialize when document is ready
document.addEventListener("DOMContentLoaded", function () {
    loadDosenData();
    initializeSearchListeners();
});

function initializeSearchListeners() {
    let dosenTimeout = null;
    let perusahaanTimeout = null;

    document
        .getElementById("searchDosen")
        .addEventListener("input", function (e) {
            clearTimeout(dosenTimeout);
            dosenTimeout = setTimeout(() => {
                loadDosenData(1, { search: e.target.value });
            }, 300);
        });

    document
        .getElementById("searchPerusahaan")
        .addEventListener("input", function (e) {
            clearTimeout(perusahaanTimeout);
            perusahaanTimeout = setTimeout(() => {
                loadDosenData(1, { perusahaan: e.target.value });
            }, 300);
        });
}

function loadDosenData(page = 1, filters = {}) {
    currentPage = page;
    const params = {
        page,
        per_page: perPage,
        ...filters,
    };

    api.get("/dosen", { params })
        .then(function (response) {
            if (response.data.success) {
                updateTable(response.data.data);
                updatePagination(response.data.meta);
            }
        })
        .catch(function (error) {
            console.error("Error:", error);
            showError();
        });
}

function updateTable(data) {
    const tableBody = document.getElementById("plotting-table-body");
    tableBody.innerHTML = "";

    if (data.length > 0) {
        data.forEach((dosen) => {
            tableBody.innerHTML += `
                <tr>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="${
                                dosen.id_dosen
                            }">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <h6 class="mb-0 text-sm">${dosen.nama_dosen}</h6>
                            <span class="text-secondary text-xs">${
                                dosen.nip
                            }</span>
                        </div>
                    </td>
                    <td>
                        <span class="text-sm ${
                            dosen.perusahaan
                                ? "text-assigned"
                                : "text-unassigned"
                        }">
                            ${
                                dosen.perusahaan?.nama_perusahaan ||
                                "Belum ditugaskan"
                            }
                        </span>
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-primary btn-action" onclick="assignDosen(${
                            dosen.id_dosen
                        })">
                            Tugaskan
                        </button>
                    </td>
                </tr>
            `;
        });
    } else {
        showEmptyState();
    }
}

function updatePagination(meta) {
    document.getElementById(
        "showingCount"
    ).textContent = `${meta.from}-${meta.to}`;
    document.getElementById("totalCount").textContent = meta.total;

    const pagination = document.getElementById("pagination");
    pagination.innerHTML = generatePaginationHTML(meta);
}

function generatePaginationHTML(meta) {
    // Implement pagination HTML generation
}

function showEmptyState() {
    const tableBody = document.getElementById("plotting-table-body");
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

function showError() {
    const tableBody = document.getElementById("plotting-table-body");
    tableBody.innerHTML = `
        <tr>
            <td colspan="4">
                <div class="text-center py-4">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #dc3545;"></i>
                    </div>
                    <h6 class="text-danger">Gagal memuat data dosen</h6>
                </div>
            </td>
        </tr>
    `;
}
