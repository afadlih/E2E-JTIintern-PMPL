document.addEventListener("DOMContentLoaded", function () {
    // Load initial data with 'perusahaan' filter
    loadEvaluations("perusahaan");

    // Filter buttons functionality
    const filterButtons = document.querySelectorAll(".btn-filter");
    filterButtons.forEach((button) => {
        button.addEventListener("click", function () {
            // Remove active class from all buttons
            filterButtons.forEach((btn) => btn.classList.remove("active"));

            // Add active class to clicked button
            this.classList.add("active");

            // Get filter type
            const filterType = this.dataset.filter;

            // Load evaluations based on filter
            loadEvaluations(filterType);
        });
    });
});

function loadEvaluations(filterType) {
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

    api.get(`/evaluasi/${filterType}`)
        .then((response) => {
            if (response.data.success) {
                updateEvaluationCards(response.data.data);
            }
        })
        .catch((error) => {
            console.error("Error loading evaluations:", error);
            showErrorState();
        });
}

function updateEvaluationCards(evaluations) {
    const container = document.querySelector(".evaluation-cards");
    container.innerHTML = "";

    if (evaluations.length === 0) {
        showEmptyState();
        return;
    }

    evaluations.forEach((evaluation) => {
        container.innerHTML += `
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">${evaluation.nama_dosen}</h5>
                        <span class="text-muted small">${formatTimeAgo(
                            evaluation.created_at
                        )}</span>
                    </div>
                    <div class="evaluation-info mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-1">${evaluation.nama_mahasiswa}</p>
                                <p class="text-primary mb-0">${
                                    evaluation.nama_perusahaan
                                }</p>
                            </div>
                            <div class="score-badge">
                                Nilai : ${evaluation.nilai}
                            </div>
                        </div>
                    </div>
                    <div class="evaluation-text">
                        <h6 class="text-muted mb-2">Evaluasi</h6>
                        <p class="text-secondary">${evaluation.evaluasi}</p>
                    </div>
                </div>
            </div>
        `;
    });
}

function showEmptyState() {
    const container = document.querySelector(".evaluation-cards");
    container.innerHTML = `
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="empty-state-icon mb-3">
                    <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #8898aa;"></i>
                </div>
                <h5 class="text-muted">Tidak ada data evaluasi</h5>
                <p class="text-secondary mb-0">Belum ada evaluasi yang ditambahkan</p>
            </div>
        </div>
    `;
}

function showErrorState() {
    const container = document.querySelector(".evaluation-cards");
    container.innerHTML = `
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="empty-state-icon mb-3">
                    <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: #dc3545;"></i>
                </div>
                <h5 class="text-danger">Gagal memuat data</h5>
                <p class="text-secondary mb-0">Terjadi kesalahan saat memuat data evaluasi</p>
            </div>
        </div>
    `;
}

function formatTimeAgo(datetime) {
    const date = new Date(datetime);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // difference in seconds

    if (diff < 60) return "Baru saja";
    if (diff < 3600) return `${Math.floor(diff / 60)} menit yang lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam yang lalu`;
    if (diff < 2592000) return `${Math.floor(diff / 86400)} hari yang lalu`;
    if (diff < 31536000) return `${Math.floor(diff / 2592000)} bulan yang lalu`;
    return `${Math.floor(diff / 31536000)} tahun yang lalu`;
}
