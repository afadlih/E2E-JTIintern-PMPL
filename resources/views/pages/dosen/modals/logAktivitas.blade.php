<div class="modal fade" id="logAktivitasModal" tabindex="-1" aria-labelledby="logAktivitasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <div class="d-flex align-items-center">
                    <div class="icon-box me-3"
                        style="background: linear-gradient(135deg, #5988FF, #4c7bef); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <span id="studentInitial" class="text-white">M</span> <!-- Tambahkan elemen ini -->
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="logAktivitasModalLabel">Log Aktivitas Mahasiswa</h5>
                        <p class="text-muted mb-0 small" id="mahasiswaInfo">Memuat informasi mahasiswa...</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <!-- Loading State -->
                <div id="logAktivitasLoading" class="p-5 text-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h6>Memuat Log Aktivitas</h6>
                    <p class="text-muted">Mengambil data aktivitas mahasiswa...</p>
                </div>

                <!-- Error State -->
                <div id="logAktivitasError" class="p-5 text-center d-none">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h6>Gagal Memuat Data</h6>
                    <p class="text-muted mb-3" id="errorMessage">Terjadi kesalahan saat memuat log aktivitas</p>
                    <button class="btn btn-primary" onclick="retryLoadLogAktivitas()">
                        <i class="fas fa-sync-alt me-2"></i>Coba Lagi
                    </button>
                </div>

                <!-- Empty State -->
                <div id="logAktivitasEmpty" class="p-5 text-center d-none">
                    <div class="mb-3">
                        <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                    </div>
                    <h6>Belum Ada Aktivitas</h6>
                    <p class="text-muted">Mahasiswa belum mencatat aktivitas magang</p>
                </div>

                <!-- Content -->
                <div id="logAktivitasContent" class="d-none">
                    <!-- Magang selector if multiple magang exist -->
                    <div id="magangSelector" class="px-4 pt-4 d-none">
                        <div class="alert alert-info mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle me-2"></i>
                                <div class="flex-grow-1">
                                    <p class="mb-0 small">Mahasiswa ini memiliki beberapa magang. Pilih magang untuk
                                        menampilkan log aktivitas tertentu:</p>
                                </div>
                                <select id="selectMagang" class="form-select form-select-sm ms-3"
                                    style="width: auto; min-width: 180px;">
                                    <option value="">Semua Magang</option>
                                    <!-- Options will be filled dynamically -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row g-3 p-4 pb-0">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="totalDays">0</h6>
                                    <small class="text-muted">Total Hari</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="totalActivities">0</h6>
                                    <small class="text-muted">Aktivitas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-images"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="totalPhotos">0</h6>
                                    <small class="text-muted">Foto</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-info">
                                    <h6 class="mb-0" id="lastActivity">-</h6>
                                    <small class="text-muted">Aktivitas Terakhir</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter and Search -->
                    <div class="px-4 py-3 border-bottom bg-light">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="searchLogAktivitas"
                                        placeholder="Cari aktivitas...">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterMonth">
                                    <option value="">Semua Bulan</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" id="filterHasPhoto">
                                    <option value="">Semua</option>
                                    <option value="with">Dengan Foto</option>
                                    <option value="without">Tanpa Foto</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Container -->
                    <div class="timeline-container-modal" style="max-height: 500px; overflow-y: auto;">
                        <div id="timelineLogAktivitas" class="timeline-dosen p-4">
                            <!-- Timeline items will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top">
                <div class="d-flex justify-content-between w-100 align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        Data diurutkan dari aktivitas terbaru
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Detail Modal -->
<div class="modal fade" id="photoDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoDetailTitle">Detail Foto Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" alt="Foto Aktivitas" class="img-fluid rounded" id="photoDetailImage">
                <div class="mt-3">
                    <p class="text-muted mb-0" id="photoDetailDescription">Deskripsi aktivitas</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Timeline styles for dosen view */
    .timeline-dosen {
        position: relative;
        padding: 0;
    }

    .timeline-month-dosen {
        margin-bottom: 30px;
    }

    .month-label-dosen {
        font-size: 0.9rem;
        text-transform: uppercase;
        color: #8898aa;
        letter-spacing: 0.5px;
        font-weight: 600;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
        margin-bottom: 15px;
    }

    .timeline-item-dosen {
        padding-bottom: 20px;
        transition: all 0.3s ease;
    }

    .timeline-item-dosen.filtered-out {
        display: none;
    }

    .timeline-item-dosen.filtered-in {
        display: block;
        animation: fadeIn 0.5s ease;
    }

    .timeline-card-dosen {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        padding: 15px;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }

    .timeline-card-dosen:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .timeline-header-dosen {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .timeline-date-dosen {
        font-weight: 600;
        font-size: 0.95rem;
        color: #344767;
    }

    .timeline-day-dosen {
        font-size: 0.8rem;
        color: #8898aa;
    }

    .timeline-time-dosen {
        font-size: 0.75rem;
        color: #8898aa;
    }

    .timeline-description-dosen {
        font-size: 0.9rem;
        color: #344767;
        white-space: pre-line;
        margin-bottom: 10px;
    }

    .timeline-photo-dosen {
        margin: 10px 0;
        text-align: center;
    }

    .timeline-photo-dosen img {
        max-height: 200px;
        border-radius: 5px;
        cursor: pointer;
        transition: transform 0.2s ease;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }

    .timeline-photo-dosen img:hover {
        transform: scale(1.02);
    }

    .timeline-actions-dosen {
        display: flex;
        justify-content: flex-end;
        margin-top: 8px;
    }

    .stat-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        height: 100%;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: rgba(89, 136, 255, 0.1);
        color: #5988FF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .stat-info {
        display: flex;
        flex-direction: column;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .timeline-month-dosen {
            margin-bottom: 20px;
        }

        .timeline-item-dosen {
            padding-bottom: 15px;
        }
    }
</style>

<script>
    // Helper function untuk mendapatkan warna status
    function getStatusColor(status) {
        if (!status) return 'secondary';

        switch (status.toLowerCase()) {
            case 'aktif':
                return 'success';
            case 'selesai':
                return 'warning';
            case 'menunggu':
                return 'info';
            case 'ditolak':
                return 'danger';
            case 'dibatalkan':
                return 'danger';
            case 'pending':
                return 'warning';
            default:
                return 'secondary';
        }
    }
</script>
