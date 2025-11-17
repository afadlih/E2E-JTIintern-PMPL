@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid px-10">
        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Evaluasi</h6>
                    <button id="reset-filters" class="btn btn-link text-secondary ms-auto mb-0">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dosen</label>
                        <select class="form-select" id="filter-dosen">
                            <option value="">Semua Dosen</option>
                            <!-- Dosen options will be loaded here -->
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Perusahaan</label>
                        <select class="form-select" id="filter-perusahaan">
                            <option value="">Semua Perusahaan</option>
                            <!-- Perusahaan options will be loaded here -->
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Skeleton Loading Container -->
        <div id="skeleton-loading-container" class="skeleton-loading">
            <div class="row" id="skeleton-cards">
                @for ($i = 1; $i <= 6; $i++)
                    <div class="col-lg-6 mb-4 skeleton-card-wrapper" data-index="{{ $i }}">
                        <div class="evaluasi-skeleton-card">
                            <div class="skeleton-card-body">
                                <div class="skeleton-header-section">
                                    <div class="skeleton-avatar-section">
                                        <div class="skeleton-avatar"></div>
                                        <div class="skeleton-avatar-text">
                                            <div class="skeleton-text skeleton-text-md mb-1"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                    </div>
                                    <div class="skeleton-score-badge"></div>
                                </div>

                                <div class="skeleton-meta-section">
                                    <div class="skeleton-badge"></div>
                                    <div class="skeleton-date"></div>
                                </div>

                                <div class="skeleton-comment-section">
                                    <div class="skeleton-text skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text skeleton-text-md mb-1"></div>
                                    <div class="skeleton-text skeleton-text-sm"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Legacy Loading Spinner (Hidden) -->
        <div id="loading-container" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data evaluasi...</p>
        </div>

        <!-- Empty State Container -->
        <div id="empty-container" class="d-none">
            <div class="card empty-state-card">
                <div class="card-body">
                    <div class="text-center py-5">
                        <div class="empty-icon mb-3">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h5 class="mb-2">Tidak ada evaluasi</h5>
                        <p class="text-muted mb-0" id="empty-message">
                            Belum ada evaluasi yang tercatat
                        </p>
                        <button class="btn btn-outline-primary btn-sm mt-3" onclick="loadEvaluations()">
                            <i class="fas fa-refresh me-1"></i>Muat Ulang
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real Evaluasi Cards Container -->
        <div id="evaluasi-container" class="d-none">
            <div class="row" id="evaluasi-cards">
                <!-- Evaluation cards will be dynamically loaded here -->
            </div>
        </div>

        <!-- Filter Loading Overlay -->
        <div id="filter-loading-overlay" class="filter-loading-overlay d-none">
            <div class="filter-loading-content">
                <div class="loading-spinner"></div>
                <p>Memfilter evaluasi...</p>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/evaluasi.css') }}">
@endpush

@push('js')
    <script>
        // Global variables
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });

        let evaluasiData = [];
        let isInitialLoad = true;

        // Generate random color for avatar backgrounds
        function getRandomColor(name) {
            const colors = [
                'primary', 'secondary', 'success', 'danger',
                'warning', 'info', 'dark'
            ];

            // Use the name to determine a consistent color
            const hashCode = name.split('').reduce((a, b) => {
                a = ((a << 5) - a) + b.charCodeAt(0);
                return a & a;
            }, 0);

            return colors[Math.abs(hashCode) % colors.length];
        }

        // Format date to Indonesian format
        function formatDate(dateString) {
            const date = new Date(dateString);
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            return date.toLocaleDateString('id-ID', options);
        }

        // Enhanced load evaluations with skeleton loading
        function loadEvaluations(filters = {}) {
            console.log('🔄 Loading evaluations with filters:', filters);

            const skeletonContainer = document.getElementById('skeleton-loading-container');
            const loadingContainer = document.getElementById('loading-container');
            const emptyContainer = document.getElementById('empty-container');
            const evaluasiContainer = document.getElementById('evaluasi-container');
            const filterOverlay = document.getElementById('filter-loading-overlay');

            // Show appropriate loading state
            if (isInitialLoad) {
                // Show skeleton for initial load
                skeletonContainer.classList.remove('d-none');
                animateSkeletonCards();
            } else {
                // Show filter overlay for subsequent loads
                filterOverlay.classList.remove('d-none');
            }

            emptyContainer.classList.add('d-none');
            evaluasiContainer.classList.add('d-none');
            loadingContainer.classList.add('d-none');

            // Build query parameters
            const params = new URLSearchParams();
            if (filters.dosen) params.append('dosen', filters.dosen);
            if (filters.perusahaan) params.append('perusahaan', filters.perusahaan);

            // Get evaluations from API
            api.get(`/mahasiswa/evaluasi?${params.toString()}`)
                .then(response => {
                    // Add delay to show skeleton/loading effect
                    const delay = isInitialLoad ? 2500 : 1200;

                    setTimeout(() => {
                        if (response.data.success) {
                            evaluasiData = response.data.data;

                            // Hide loading states
                            hideAllLoadingStates();

                            if (evaluasiData.length === 0) {
                                showEmptyState(filters);
                            } else {
                                showEvaluations(evaluasiData);
                            }
                        } else {
                            hideAllLoadingStates();
                            showError('Gagal memuat data evaluasi');
                        }

                        isInitialLoad = false;
                    }, delay);
                })
                .catch(error => {
                    console.error('Error:', error);
                    const delay = isInitialLoad ? 2500 : 1200;

                    setTimeout(() => {
                        hideAllLoadingStates();
                        showError('Terjadi kesalahan saat memuat data');
                        isInitialLoad = false;
                    }, delay);
                });
        }

        function animateSkeletonCards() {
            const skeletonCards = document.querySelectorAll('.skeleton-card-wrapper');

            skeletonCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';

                setTimeout(() => {
                    card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });
        }

        function hideAllLoadingStates() {
            const skeletonContainer = document.getElementById('skeleton-loading-container');
            const loadingContainer = document.getElementById('loading-container');
            const filterOverlay = document.getElementById('filter-loading-overlay');

            // Hide skeleton with fade out
            if (!skeletonContainer.classList.contains('d-none')) {
                skeletonContainer.style.transition = 'opacity 0.5s ease';
                skeletonContainer.style.opacity = '0';

                setTimeout(() => {
                    skeletonContainer.classList.add('d-none');
                    skeletonContainer.style.opacity = '1';
                }, 500);
            }

            loadingContainer.classList.add('d-none');
            filterOverlay.classList.add('d-none');
        }

        function showEmptyState(filters) {
            const emptyContainer = document.getElementById('empty-container');
            const emptyMessage = document.getElementById('empty-message');

            if (Object.keys(filters).length > 0) {
                emptyMessage.textContent = 'Tidak ada evaluasi yang sesuai dengan filter yang dipilih';
            } else {
                emptyMessage.textContent = 'Belum ada evaluasi yang tercatat';
            }

            emptyContainer.classList.remove('d-none');

            // Animate empty state
            emptyContainer.style.opacity = '0';
            emptyContainer.style.transform = 'translateY(20px)';
            emptyContainer.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                emptyContainer.style.opacity = '1';
                emptyContainer.style.transform = 'translateY(0)';
            }, 50);
        }

        function showEvaluations(data) {
            const evaluasiContainer = document.getElementById('evaluasi-container');

            evaluasiContainer.classList.remove('d-none');

            // Animate container in
            evaluasiContainer.style.opacity = '0';
            evaluasiContainer.style.transform = 'translateY(20px)';
            evaluasiContainer.style.transition = 'all 0.6s ease';

            setTimeout(() => {
                evaluasiContainer.style.opacity = '1';
                evaluasiContainer.style.transform = 'translateY(0)';
                renderEvaluations(data);
            }, 50);
        }

        // Enhanced render evaluations with staggered animation
        function renderEvaluations(data) {
            const evaluasiCards = document.getElementById('evaluasi-cards');
            evaluasiCards.innerHTML = '';

            data.forEach((item, index) => {
                const colorClass = getRandomColor(item.dosen.nama);
                const formattedDate = formatDate(item.tanggal);

                const cardWrapper = document.createElement('div');
                cardWrapper.className = 'col-lg-6 mb-4 evaluation-card-wrapper';
                cardWrapper.style.opacity = '0';
                cardWrapper.style.transform = 'translateY(30px) scale(0.95)';

                cardWrapper.innerHTML = `
                    <div class="card card-evaluation">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-gradient-${colorClass} me-3">${item.dosen.avatar_text}</div>
                                <div>
                                    <h6 class="mb-0">${item.dosen.nama}</h6>
                                    <p class="text-sm mb-0">Dosen Pembimbing</p>
                                </div>
                                <div class="ms-auto">
                                    <div class="text-end">
                                        <span class="score-badge">
                                            <i class="fas fa-star text-warning me-1"></i>${item.score}
                                        </span>
                                        ${item.grade && item.grade !== '-' ? `<div class="text-sm text-muted mt-1">Grade: ${item.grade}</div>` : ''}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge bg-gradient-info">${item.perusahaan.nama}</span>
                                <span class="text-sm ms-2">${formattedDate}</span>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-sm font-weight-bold mb-1">${item.judul_lowongan}</h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Nilai Perusahaan:</small>
                                        <div class="font-weight-bold">${item.nilai_perusahaan || '-'}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Nilai Dosen:</small>
                                        <div class="font-weight-bold">${item.nilai_dosen || '-'}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="mb-3 text-sm">${item.komentar}</p>
                            
                            <!-- Tombol Log Aktivitas -->
                            <button onclick="showLogAktivitas('${item.id_mahasiswa}', '${item.id_magang}')" 
                                    class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-clipboard-list me-2"></i>Lihat Log Aktivitas
                            </button>
                        </div>
                    </div>
                `;

                evaluasiCards.appendChild(cardWrapper);

                // Animate card in with staggered delay
                setTimeout(() => {
                    cardWrapper.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    cardWrapper.style.opacity = '1';
                    cardWrapper.style.transform = 'translateY(0) scale(1)';
                }, index * 150);
            });
        }

        // Show error message
        function showError(message) {
            hideAllLoadingStates();

            const emptyContainer = document.getElementById('empty-container');
            const emptyMessage = document.getElementById('empty-message');

            emptyContainer.classList.remove('d-none');
            emptyMessage.textContent = message;

            // Change icon to error
            const emptyIcon = emptyContainer.querySelector('.empty-icon i');
            emptyIcon.className = 'fas fa-exclamation-triangle';
            emptyIcon.style.color = '#f5365c';

            // Animate error state
            emptyContainer.style.opacity = '0';
            emptyContainer.style.transform = 'translateY(20px)';
            emptyContainer.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                emptyContainer.style.opacity = '1';
                emptyContainer.style.transform = 'translateY(0)';
            }, 50);
        }

        // Load filter options
        function loadFilterOptions() {
            api.get('/mahasiswa/evaluasi/filter-options')
                .then(response => {
                    if (response.data.success) {
                        const {
                            dosen,
                            perusahaan
                        } = response.data.data;

                        // Populate dosen options
                        const dosenSelect = document.getElementById('filter-dosen');
                        dosen.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.id_dosen;
                            option.textContent = item.nama_dosen;
                            dosenSelect.appendChild(option);
                        });

                        // Populate perusahaan options
                        const perusahaanSelect = document.getElementById('filter-perusahaan');
                        perusahaan.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.perusahaan_id;
                            option.textContent = item.nama_perusahaan;
                            perusahaanSelect.appendChild(option);
                        });
                    } else {
                        console.error('Failed to load filter options:', response.data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading filter options:', error);
                });
        }

        // Initialize filters
        function initFilters() {
            const dosenSelect = document.getElementById('filter-dosen');
            const perusahaanSelect = document.getElementById('filter-perusahaan');
            const resetButton = document.getElementById('reset-filters');

            // Dosen filter change event
            dosenSelect.addEventListener('change', function() {
                applyFilters();
            });

            // Perusahaan filter change event
            perusahaanSelect.addEventListener('change', function() {
                applyFilters();
            });

            // Reset filters button click event
            resetButton.addEventListener('click', function() {
                dosenSelect.value = '';
                perusahaanSelect.value = '';
                applyFilters();

                // Show reset feedback
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: 'Filter direset',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        }

        // Apply filters with enhanced UX
        function applyFilters() {
            const dosenValue = document.getElementById('filter-dosen').value;
            const perusahaanValue = document.getElementById('filter-perusahaan').value;

            const filters = {};
            if (dosenValue) filters.dosen = dosenValue;
            if (perusahaanValue) filters.perusahaan = perusahaanValue;

            // Show filter applied feedback
            if (Object.keys(filters).length > 0) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Filter diterapkan',
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            loadEvaluations(filters);
        }

        // Document ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 Initializing MhsEvaluasi page...');

            // Load filter options first
            loadFilterOptions();

            // Initialize filters
            initFilters();

            // Start initial loading with skeleton
            setTimeout(() => {
                loadEvaluations();
            }, 300);
        });

        // Add these functions after renderEvaluations function
        function showLogAktivitas(id_mahasiswa, id_magang) {
            // Show loading first
            Swal.fire({
                title: 'Memuat Log Aktivitas',
                text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('Fetching logs for mahasiswa:', id_mahasiswa, 'magang:', id_magang);

            if (!id_magang) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID Magang tidak ditemukan'
                });
                return;
            }

            // Make API call to get logbook data
            api.get(`/mahasiswa/${id_mahasiswa}/logbook`, {
                    params: {
                        id_magang: id_magang
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        // Get the modal element
                        const modal = new bootstrap.Modal(document.getElementById('logAktivitasModal'));
                        const modalBody = document.querySelector('#logAktivitasModal .modal-body');
                        const modalTitle = document.querySelector('#logAktivitasModal .modal-title');

                        // Update modal title
                        modalTitle.textContent = `Log Aktivitas`;

                        // Generate content based on the data
                        if (response.data.data.length === 0) {
                            modalBody.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0">Belum ada log aktivitas yang tercatat</p>
                            </div>
                        `;
                        } else {
                            // Sort and group logs by month
                            const groupedLogs = response.data.data;
                            let logsHTML = '';

                            groupedLogs.forEach(group => {
                                logsHTML += `
                                <div class="month-group mb-4">
                                    <h6 class="text-primary mb-3">${group.month}</h6>
                                    <div class="timeline">
                                        ${group.entries.map(log => `
                                                    <div class="timeline-item mb-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <div>
                                                                <span class="badge bg-info">${log.tanggal_hari}</span>
                                                                <span class="text-muted ms-2">${log.tanggal_formatted}</span>
                                                            </div>
                                                            <small class="text-muted">${log.time_ago}</small>
                                                        </div>
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <p class="mb-0">${log.deskripsi}</p>
                                                                ${log.has_foto ? `
                                                            <div class="mt-2">
                                                                <img src="${log.foto}" class="img-fluid rounded" 
                                                                     style="max-height: 200px; cursor: pointer"
                                                                     onclick="showFullImage('${log.foto}')"
                                                                     alt="Dokumentasi aktivitas">
                                                            </div>
                                                        ` : ''}
                                                            </div>
                                                        </div>
                                                    </div>
                                                `).join('')}
                                    </div>
                                </div>
                            `;
                            });

                            modalBody.innerHTML = logsHTML;
                        }

                        // Hide loading and show modal
                        Swal.close();
                        modal.show();
                    } else {
                        throw new Error(response.data.message || 'Gagal memuat log aktivitas');
                    }
                })
                .catch(error => {
                    console.error('Error fetching log aktivitas:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Terjadi kesalahan saat memuat log aktivitas'
                    });
                });
        }

        // Helper function to show full image
        function showFullImage(imageUrl) {
            Swal.fire({
                imageUrl: imageUrl,
                imageAlt: 'Dokumentasi aktivitas',
                width: '80%',
                showConfirmButton: false,
                showCloseButton: true
            });
        }
    </script>
@endpush

<!-- Log Aktivitas Modal -->
<div class="modal fade" id="logAktivitasModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be inserted here by JavaScript -->
            </div>
        </div>
    </div>
</div>
