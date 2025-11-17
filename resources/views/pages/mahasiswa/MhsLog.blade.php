@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid px-10">
        @if (isset($hasActiveMagang) && $hasActiveMagang)
            <!-- ✅ CONTENT DENGAN SKELETON LOADING UNTUK MAGANG AKTIF -->

            <!-- Header Section dengan Skeleton -->
            <div class="row mb-4">
                <div class="col-12">
                    <!-- Skeleton Loader untuk Header -->
                    <div class="header-skeleton" id="header-skeleton">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="skeleton-text skeleton-text-xl mb-0" style="width: 280px;"></div>
                            <div class="skeleton-status-card">
                                <div class="skeleton-text skeleton-text-sm mb-1" style="width: 120px;"></div>
                                <div class="skeleton-badge" style="width: 80px;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Real Header Content (Hidden Initially) -->
                    <div class="real-header d-none" id="real-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">Logbook Aktivitas Magang</h4>

                            <!-- Status Magang Card -->
                            <div class="status-magang d-flex align-items-center">
                                @if (isset($magangInfo) && $magangInfo)
                                    <div class="status-info">
                                        <span class="status-label">Periode Magang:</span>
                                        @if (isset($magangInfo['data']->tgl_mulai) && isset($magangInfo['data']->tgl_selesai))
                                            <span class="status-value">
                                                {{ \Carbon\Carbon::parse($magangInfo['data']->tgl_mulai)->format('d M Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($magangInfo['data']->tgl_selesai)->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="status-value text-muted">Jadwal belum ditentukan</span>
                                        @endif
                                    </div>
                                    <div class="status-badge active">
                                        <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Row -->
            <div class="row">
                <!-- Logbook Form Column -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm mb-4">
                        <!-- Form Skeleton Loader -->
                        <div class="form-skeleton" id="form-skeleton">
                            <div class="card-header bg-white pt-4 pb-3 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="skeleton-icon-box me-3"></div>
                                    <div>
                                        <div class="skeleton-text skeleton-text-lg mb-2" style="width: 180px;"></div>
                                        <div class="skeleton-text skeleton-text-sm" style="width: 250px;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <!-- Date Input Skeleton -->
                                <div class="mb-4">
                                    <div class="skeleton-text skeleton-text-sm mb-2" style="width: 120px;"></div>
                                    <div class="skeleton-input"></div>
                                </div>

                                <!-- Textarea Skeleton -->
                                <div class="mb-4">
                                    <div class="skeleton-text skeleton-text-sm mb-2" style="width: 140px;"></div>
                                    <div class="skeleton-textarea"></div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <div class="skeleton-text skeleton-text-xs" style="width: 100px;"></div>
                                        <div class="skeleton-text skeleton-text-xs" style="width: 80px;"></div>
                                    </div>
                                </div>

                                <!-- File Upload Skeleton -->
                                <div class="mb-4">
                                    <div class="skeleton-text skeleton-text-sm mb-2" style="width: 160px;"></div>
                                    <div class="skeleton-upload-area"></div>
                                </div>

                                <!-- Button Skeleton -->
                                <div class="d-grid">
                                    <div class="skeleton-button"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Form Content (Hidden Initially) -->
                        <div class="real-form d-none" id="real-form">
                            <div class="card-header bg-white pt-4 pb-3 border-0">
                                <div class="d-flex align-items-center">
                                    <div class="icon-box me-3">
                                        <i class="bi bi-journal-plus"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">Tambah Aktivitas Baru</h5>
                                        <p class="text-muted mb-0 small">Dokumentasikan kegiatan magang harian Anda</p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <form id="logbookForm" enctype="multipart/form-data" class="needs-validation" novalidate>
                                    <div class="mb-4">
                                        <label for="tanggal" class="form-label">Tanggal Aktivitas</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-transparent border-end-0">
                                                <i class="bi bi-calendar-event"></i>
                                            </span>
                                            <input type="date" class="form-control ps-0 border-start-0" id="tanggal"
                                                name="tanggal" required max="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="invalid-feedback">Tanggal aktivitas tidak boleh kosong dan tidak boleh
                                            di masa depan</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="deskripsi" class="form-label">Deskripsi Kegiatan</label>
                                        <div class="input-group">
                                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                                placeholder="Jelaskan aktivitas yang Anda lakukan hari ini" required minlength="10"></textarea>
                                        </div>
                                        <div class="invalid-feedback">Deskripsi kegiatan tidak boleh kosong dan minimal 10
                                            karakter</div>
                                        <div class="form-text text-end"><span id="charCount">0</span>/500 karakter</div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="foto" class="form-label">Foto Aktivitas <span
                                                class="text-muted">(Opsional)</span></label>
                                        <div class="upload-container">
                                            <input type="file" class="file-input" id="foto" name="foto"
                                                accept="image/*" onchange="previewImage(this)">
                                            <div class="upload-area" id="uploadArea">
                                                <i class="bi bi-image"></i>
                                                <p>Klik untuk upload atau drag & drop</p>
                                                <span class="text-muted">JPG, PNG, JPEG max 2MB</span>
                                            </div>
                                            <div id="imagePreview" class="image-preview d-none">
                                                <button type="button" class="btn-remove-image" onclick="removeImage()">
                                                    <i class="bi bi-x-circle-fill"></i>
                                                </button>
                                                <img src="" alt="Preview" class="img-preview">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary py-2" id="simpanAktivitas">
                                            <span id="btnText"><i class="bi bi-journal-check me-2"></i>Simpan
                                                Aktivitas</span>
                                            <span id="btnLoading" class="d-none">
                                                <span class="spinner-border spinner-border-sm me-2" role="status"
                                                    aria-hidden="true"></span>
                                                Menyimpan...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity History Column -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm mb-4">
                        <!-- Timeline Skeleton Loader -->
                        <div class="timeline-skeleton" id="timeline-skeleton">
                            <div class="card-header bg-white pt-4 pb-3 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="skeleton-icon-box me-3"></div>
                                        <div>
                                            <div class="skeleton-text skeleton-text-lg mb-2" style="width: 150px;"></div>
                                            <div class="skeleton-text skeleton-text-sm" style="width: 200px;"></div>
                                        </div>
                                    </div>
                                    <div class="skeleton-search-box"></div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="timeline-skeleton-container p-4">
                                    <!-- Skeleton Timeline Items -->
                                    <div class="skeleton-timeline-month mb-4">
                                        <div class="skeleton-text skeleton-text-md mb-3" style="width: 120px;"></div>
                                        <div class="skeleton-timeline-entries">
                                            <div class="skeleton-timeline-item mb-3">
                                                <div class="skeleton-timeline-dot"></div>
                                                <div class="skeleton-timeline-content">
                                                    <div class="skeleton-text skeleton-text-sm mb-2"
                                                        style="width: 140px;"></div>
                                                    <div class="skeleton-text skeleton-text-xs mb-1" style="width: 100%;">
                                                    </div>
                                                    <div class="skeleton-text skeleton-text-xs mb-1" style="width: 80%;">
                                                    </div>
                                                    <div class="skeleton-text skeleton-text-xs mb-2" style="width: 60%;">
                                                    </div>
                                                    <div class="skeleton-image mb-2"></div>
                                                    <div class="skeleton-button-sm"></div>
                                                </div>
                                            </div>
                                            <div class="skeleton-timeline-item mb-3">
                                                <div class="skeleton-timeline-dot"></div>
                                                <div class="skeleton-timeline-content">
                                                    <div class="skeleton-text skeleton-text-sm mb-2"
                                                        style="width: 160px;"></div>
                                                    <div class="skeleton-text skeleton-text-xs mb-1" style="width: 100%;">
                                                    </div>
                                                    <div class="skeleton-text skeleton-text-xs mb-1" style="width: 90%;">
                                                    </div>
                                                    <div class="skeleton-text skeleton-text-xs mb-2" style="width: 70%;">
                                                    </div>
                                                    <div class="skeleton-button-sm"></div>
                                                </div>
                                            </div>
                                            <div class="skeleton-timeline-item mb-3">
                                                <div class="skeleton-timeline-dot"></div>
                                                <div class="skeleton-timeline-content">
                                                    <div class="skeleton-text skeleton-text-sm mb-2"
                                                        style="width: 130px;"></div>
                                                    <div class="skeleton-text skeleton-text-xs mb-1" style="width: 100%;">
                                                    </div>
                                                    <div class="skeleton-text skeleton-text-xs mb-1" style="width: 85%;">
                                                    </div>
                                                    <div class="skeleton-button-sm"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Timeline Content (Hidden Initially) -->
                        <div class="real-timeline d-none" id="real-timeline">
                            <div class="card-header bg-white pt-4 pb-3 border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box me-3">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">Riwayat Aktivitas</h5>
                                            <p class="text-muted mb-0 small">Catatan aktivitas magang Anda</p>
                                        </div>
                                    </div>
                                    <div class="search-container">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="searchActivities"
                                                placeholder="Cari aktivitas...">
                                            <span class="input-group-text bg-transparent">
                                                <i class="bi bi-search"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="timelineContainer" class="timeline-container">
                                    <!-- Timeline content will be loaded here -->
                                    <div id="timelinePlaceholder" class="p-4 text-center d-none">
                                        <div class="empty-state">
                                            <div class="empty-state-icon">
                                                <i class="bi bi-journal-text"></i>
                                            </div>
                                            <h6>Belum ada aktivitas</h6>
                                            <p class="text-muted">Mulai catat aktivitas magang harian Anda dengan mengisi
                                                form di samping</p>
                                        </div>
                                    </div>

                                    <div id="timelineLoader" class="p-4 text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Memuat aktivitas...</p>
                                    </div>

                                    <div id="timeline" class="timeline">
                                        <!-- Timeline items will be generated dynamically by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- PLACEHOLDER CONTENT JIKA BELUM MAGANG -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <div class="access-denied-state">
                                <div class="access-denied-icon mb-4">
                                    <i class="bi bi-shield-lock"></i>
                                </div>
                                <h5 class="mb-3">Memverifikasi Status Magang</h5>
                                <p class="text-muted mb-4 mx-auto" style="max-width: 500px;">
                                    Memeriksa kelengkapan data untuk mengakses halaman ini...
                                </p>
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2">Mohon tunggu sebentar...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Activity Detail Modal -->
    <div class="modal fade" id="activityDetailModal" tabindex="-1" aria-labelledby="activityDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="activityDetailModalLabel">Detail Aktivitas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="activityModalContent">
                    <!-- Activity detail content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/logaktivitas.css') }}">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📋 MhsLog page loaded, checking magang status...');

            // Check if user has active magang
            const hasActiveMagang = @json($hasActiveMagang ?? false);
            const userData = @json($userData ?? null);

            console.log('📊 Has Active Magang:', hasActiveMagang);
            console.log('👤 User Data:', userData);

            if (!hasActiveMagang) {
                // Show access denied alert
                setTimeout(() => {
                    showAccessDeniedAlert();
                }, 1500);
                return;
            }

            // Start skeleton loading simulation
            simulateLogbookLoading();
        });

        // ✅ PERBAIKAN: Update submitLogbook function untuk menggunakan API yang benar
        function submitLogbook() {
            const form = document.getElementById('logbookForm');
            if (!form) return;

            const formData = new FormData(form);
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');

            // Show loading state
            if (btnText) btnText.classList.add('d-none');
            if (btnLoading) btnLoading.classList.remove('d-none');

            // ✅ GUNAKAN: API endpoint yang benar
            fetch('/api/mahasiswa/logbook', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📝 Logbook submit response:', data);

                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Aktivitas berhasil disimpan',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reset form
                        form.reset();
                        form.classList.remove('was-validated');
                        removeImage();
                        const charCount = document.getElementById('charCount');
                        if (charCount) charCount.textContent = '0';

                        // Reload timeline
                        loadLogbookEntries();

                    } else {
                        // Show error message
                        let errorMessage = data.message || 'Gagal menyimpan aktivitas';

                        // Handle specific error codes
                        if (data.code === 'DUPLICATE_DATE') {
                            errorMessage = 'Aktivitas untuk tanggal tersebut sudah ada. Silakan pilih tanggal lain.';
                        } else if (data.code === 'DATE_OUT_OF_RANGE') {
                            errorMessage = data.message;
                        } else if (data.errors) {
                            // Validation errors
                            const errorMessages = Object.values(data.errors).flat();
                            errorMessage = errorMessages.join('\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: errorMessage,
                            confirmButtonText: 'Ok'
                        });
                    }
                })
                .catch(error => {
                    console.error('❌ Error submitting logbook:', error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan aktivitas',
                        confirmButtonText: 'Ok'
                    });
                })
                .finally(() => {
                    // Reset button state
                    if (btnText) btnText.classList.remove('d-none');
                    if (btnLoading) btnLoading.classList.add('d-none');
                });
        }

        // ✅ PERBAIKAN: Update loadLogbookEntries untuk menggunakan API yang benar
        function loadLogbookEntries() {
            const timeline = document.getElementById('timeline');
            const timelineLoader = document.getElementById('timelineLoader');
            const timelinePlaceholder = document.getElementById('timelinePlaceholder');

            if (!timeline) return;

            timeline.innerHTML = '';
            if (timelineLoader) timelineLoader.classList.remove('d-none');
            if (timelinePlaceholder) timelinePlaceholder.classList.add('d-none');

            // ✅ FETCH: Data dari API endpoint yang benar
            fetch('/api/mahasiswa/logbook', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Logbook entries loaded:', data);

                    if (timelineLoader) timelineLoader.classList.add('d-none');

                    if (data.success && data.data && data.data.length > 0) {
                        // Render timeline entries
                        renderTimelineEntries(data.data);
                    } else {
                        // Show placeholder
                        if (timelinePlaceholder) timelinePlaceholder.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading logbook entries:', error);

                    if (timelineLoader) timelineLoader.classList.add('d-none');
                    if (timelinePlaceholder) timelinePlaceholder.classList.remove('d-none');

                    // Update placeholder to show error
                    if (timelinePlaceholder) {
                        timelinePlaceholder.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                    </div>
                    <h6>Gagal Memuat Data</h6>
                    <p class="text-muted">Terjadi kesalahan saat memuat aktivitas logbook</p>
                    <button class="btn btn-primary btn-sm" onclick="loadLogbookEntries()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                    </button>
                </div>
            `;
                    }
                });
        }

        // ✅ TAMBAHAN: Function untuk render timeline entries
        function renderTimelineEntries(groupedData) {
            const timeline = document.getElementById('timeline');
            if (!timeline) return;

            let timelineHTML = '';

            groupedData.forEach(monthGroup => {
                timelineHTML += `
            <div class="timeline-month">
                <h6 class="month-label">${monthGroup.month}</h6>
                <div class="timeline-entries">
        `;

                monthGroup.entries.forEach(entry => {
                    const photoHTML = entry.has_foto ? `
                <div class="timeline-photo">
                    <img src="${entry.foto}" alt="Foto aktivitas" class="img-fluid rounded" 
                         onclick="showPhotoModal('${entry.foto}', '${entry.tanggal_formatted}')">
                </div>
            ` : '';

                    timelineHTML += `
                <div class="timeline-item" data-entry-id="${entry.id}">
                    <div class="timeline-dot"></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <span class="timeline-date">${entry.tanggal_formatted} - ${entry.tanggal_hari}</span>
                            <small class="text-muted">${entry.time_ago}</small>
                        </div>
                        <div class="timeline-description">
                            ${entry.deskripsi}
                        </div>
                        ${photoHTML}
                        <div class="timeline-actions">
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteLogbookEntry(${entry.id}, '${entry.tanggal_formatted}')">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
            `;
                });

                timelineHTML += `
                </div>
            </div>
        `;
            });

            timeline.innerHTML = timelineHTML;
        }

        // ✅ TAMBAHAN: Function untuk delete logbook entry
        function deleteLogbookEntry(id, tanggal) {
            Swal.fire({
                title: 'Hapus Aktivitas?',
                text: `Apakah Anda yakin ingin menghapus aktivitas tanggal ${tanggal}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/mahasiswa/logbook/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Aktivitas berhasil dihapus',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload timeline
                                loadLogbookEntries();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message || 'Gagal menghapus aktivitas',
                                    confirmButtonText: 'Ok'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting logbook:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus aktivitas',
                                confirmButtonText: 'Ok'
                            });
                        });
                }
            });
        }

        // ✅ TAMBAHAN: Function untuk show photo modal
        function showPhotoModal(photoUrl, tanggal) {
            Swal.fire({
                title: `Foto Aktivitas - ${tanggal}`,
                imageUrl: photoUrl,
                imageWidth: 400,
                imageHeight: 300,
                imageAlt: 'Foto aktivitas',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    image: 'img-fluid rounded'
                }
            });
        }

        // ✅ TAMBAHAN: Missing functions yang diperlukan
        function showAccessDeniedAlert() {
            Swal.fire({
                icon: 'warning',
                title: 'Akses Ditolak',
                text: 'Anda belum memiliki magang aktif. Silakan lamar magang terlebih dahulu.',
                confirmButtonText: 'Mengerti',
                allowOutsideClick: false
            }).then(() => {
                // Redirect ke halaman lowongan
                window.location.href = '/mahasiswa/lowongan';
            });
        }

        function simulateLogbookLoading() {
            console.log('🎬 Starting logbook loading simulation...');

            // Simulate loading with progressive content reveal
            setTimeout(() => loadHeaderContent(), 800);
            setTimeout(() => loadFormContent(), 1200);
            setTimeout(() => loadTimelineContent(), 1600);
            setTimeout(() => initializeLogbook(), 2000);
        }

        function loadHeaderContent() {
            console.log('📊 Loading header content...');
            const skeleton = document.getElementById('header-skeleton');
            const realContent = document.getElementById('real-header');

            if (skeleton && realContent) {
                skeleton.style.transition = 'opacity 0.4s ease';
                skeleton.style.opacity = '0';

                setTimeout(() => {
                    skeleton.classList.add('d-none');
                    realContent.classList.remove('d-none');
                    realContent.style.opacity = '0';
                    realContent.style.transform = 'translateY(-20px)';
                    realContent.style.transition = 'all 0.6s ease';

                    setTimeout(() => {
                        realContent.style.opacity = '1';
                        realContent.style.transform = 'translateY(0)';
                    }, 50);
                }, 400);
            }
        }

        function loadFormContent() {
            console.log('📝 Loading form content...');
            const skeleton = document.getElementById('form-skeleton');
            const realContent = document.getElementById('real-form');

            if (skeleton && realContent) {
                skeleton.style.transition = 'opacity 0.4s ease';
                skeleton.style.opacity = '0';

                setTimeout(() => {
                    skeleton.classList.add('d-none');
                    realContent.classList.remove('d-none');
                    realContent.style.opacity = '0';
                    realContent.style.transform = 'translateX(-30px)';
                    realContent.style.transition = 'all 0.6s ease';

                    setTimeout(() => {
                        realContent.style.opacity = '1';
                        realContent.style.transform = 'translateX(0)';
                    }, 50);
                }, 400);
            }
        }

        function loadTimelineContent() {
            console.log('📋 Loading timeline content...');
            const skeleton = document.getElementById('timeline-skeleton');
            const realContent = document.getElementById('real-timeline');

            if (skeleton && realContent) {
                skeleton.style.transition = 'opacity 0.4s ease';
                skeleton.style.opacity = '0';

                setTimeout(() => {
                    skeleton.classList.add('d-none');
                    realContent.classList.remove('d-none');
                    realContent.style.opacity = '0';
                    realContent.style.transform = 'translateX(30px)';
                    realContent.style.transition = 'all 0.6s ease';

                    setTimeout(() => {
                        realContent.style.opacity = '1';
                        realContent.style.transform = 'translateX(0)';

                        // Load actual timeline entries
                        loadLogbookEntries();
                    }, 50);
                }, 400);
            }
        }

        function initializeLogbook() {
            console.log('🚀 Initializing logbook functionality...');
            initFormValidation();
            initDragAndDrop();
            initSearchFunctionality();
            console.log('✅ Logbook initialized successfully');
        }

        function initFormValidation() {
            const form = document.getElementById('logbookForm');
            if (!form) return;

            // Form submission handler
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (!form.checkValidity()) {
                    form.classList.add('was-validated');
                    return;
                }

                submitLogbook();
            });

            // Character counter for textarea
            const textarea = document.getElementById('deskripsi');
            const charCount = document.getElementById('charCount');

            if (textarea && charCount) {
                textarea.addEventListener('input', function() {
                    const currentLength = this.value.length;
                    charCount.textContent = currentLength;

                    // Change color based on character count
                    if (currentLength > 500) {
                        charCount.style.color = '#dc3545';
                        charCount.parentElement.classList.add('text-danger');
                    } else if (currentLength > 400) {
                        charCount.style.color = '#fd7e14';
                        charCount.parentElement.classList.remove('text-danger');
                    } else {
                        charCount.style.color = '#6c757d';
                        charCount.parentElement.classList.remove('text-danger');
                    }
                });
            }

            // Date validation
            const dateInput = document.getElementById('tanggal');
            if (dateInput) {
                dateInput.addEventListener('change', function() {
                    const selectedDate = new Date(this.value);
                    const today = new Date();
                    today.setHours(23, 59, 59, 999); // End of today

                    if (selectedDate > today) {
                        this.setCustomValidity('Tanggal tidak boleh di masa depan');
                        this.classList.add('is-invalid');
                    } else {
                        this.setCustomValidity('');
                        this.classList.remove('is-invalid');
                    }
                });
            }
        }

        function initDragAndDrop() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('foto');

            if (!uploadArea || !fileInput) return;

            // Click to upload
            uploadArea.addEventListener('click', () => fileInput.click());

            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            uploadArea.addEventListener('drop', handleDrop, false);

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight() {
                uploadArea.classList.add('drag-over');
            }

            function unhighlight() {
                uploadArea.classList.remove('drag-over');
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    previewImage(fileInput);
                }
            }
        }

        function initSearchFunctionality() {
            const searchInput = document.getElementById('searchActivities');
            if (!searchInput) return;

            let searchTimeout;

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.toLowerCase().trim();

                searchTimeout = setTimeout(() => {
                    filterTimelineEntries(searchTerm);
                }, 300);
            });
        }

        function filterTimelineEntries(searchTerm) {
            const timelineItems = document.querySelectorAll('.timeline-item');

            timelineItems.forEach(item => {
                const description = item.querySelector('.timeline-description')?.textContent.toLowerCase() || '';
                const date = item.querySelector('.timeline-date')?.textContent.toLowerCase() || '';

                if (searchTerm === '' || description.includes(searchTerm) || date.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.style.opacity = '1';
                } else {
                    item.style.display = 'none';
                    item.style.opacity = '0';
                }
            });

            // Show/hide month groups if all items are hidden
            const monthGroups = document.querySelectorAll('.timeline-month');
            monthGroups.forEach(group => {
                const visibleItems = group.querySelectorAll('.timeline-item[style*="block"]');
                if (visibleItems.length === 0 && searchTerm !== '') {
                    group.style.display = 'none';
                } else {
                    group.style.display = 'block';
                }
            });
        }

        function previewImage(input) {
            const file = input.files[0];
            if (!file) return;

            // Validate file type
            if (!file.type.startsWith('image/')) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Tidak Valid',
                    text: 'Silakan pilih file gambar (JPG, PNG, JPEG)',
                    confirmButtonText: 'Ok'
                });
                input.value = '';
                return;
            }

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB',
                    confirmButtonText: 'Ok'
                });
                input.value = '';
                return;
            }

            const uploadArea = document.getElementById('uploadArea');
            const imagePreview = document.getElementById('imagePreview');
            const imgPreview = imagePreview.querySelector('.img-preview');

            const reader = new FileReader();
            reader.onload = function(e) {
                imgPreview.src = e.target.result;
                uploadArea.classList.add('d-none');
                imagePreview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }

        function removeImage() {
            const uploadArea = document.getElementById('uploadArea');
            const imagePreview = document.getElementById('imagePreview');
            const fileInput = document.getElementById('foto');

            if (uploadArea && imagePreview && fileInput) {
                uploadArea.classList.remove('d-none');
                imagePreview.classList.add('d-none');
                fileInput.value = '';
            }
        }
    </script>
@endpush
