@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid px-10">
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-4">Daftar Lowongan Magang</h3>

                <!-- Filter Section -->
                <div class="filter-section mb-4">
                    <h5 class="mb-3"><i class="bi bi-funnel-fill me-2"></i>Filter Lowongan</h5>
                    <div class="row align-items-center">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label small text-muted">Perusahaan</label>
                            <select id="perusahaanFilter" class="form-select">
                                <option value="">Semua Perusahaan</option>
                                @foreach ($perusahaan as $p)
                                    <option value="{{ $p->perusahaan_id }}">{{ $p->nama_perusahaan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label small text-muted">Skill</label>
                            <select id="skillFilter" class="form-select">
                                <option value="">Semua Skill</option>
                                @foreach ($skills as $s)
                                    <option value="{{ $s->skill_id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="applyFilter">
                                <i class="bi bi-filter me-2"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Lowongan Cards Container -->
                <div class="row" id="lowonganContainer">
                    <!-- Initial Skeleton Loading Cards -->
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="col-md-4 mb-4 skeleton-card-wrapper" id="skeleton-card-{{ $i }}">
                            <div class="lowongan-skeleton-card">
                                <div class="skeleton-card-header">
                                    <div class="skeleton-company-section">
                                        <div class="skeleton-company-logo"></div>
                                        <div class="skeleton-company-text">
                                            <div class="skeleton-text skeleton-text-md mb-2"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="skeleton-card-divider"></div>

                                <div class="skeleton-card-body">
                                    <div class="skeleton-info-section mb-3">
                                        <div class="skeleton-info-item">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                        <div class="skeleton-info-item">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                    </div>

                                    <div class="skeleton-skills-section mb-3">
                                        <div class="skeleton-skill-tag"></div>
                                        <div class="skeleton-skill-tag"></div>
                                        <div class="skeleton-skill-tag-small"></div>
                                    </div>

                                    <div class="skeleton-card-footer">
                                        <div class="skeleton-capacity-badge"></div>
                                        <div class="skeleton-view-button"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor

                    <!-- Real content will replace skeleton cards -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Lowongan dengan Loading State -->
    <div class="modal fade" id="lowonganDetailModal" tabindex="-1" aria-labelledby="lowonganDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="lowonganDetailModalLabel">Detail Lowongan</h5>
                    <button type="button" class="btn-close btn-close-white position-absolute"
                        style="right: 20px; top: 20px; z-index: 10;" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="lowonganDetailContent">
                    <!-- Modal Skeleton Loading -->
                    <div class="modal-skeleton-loading" id="modal-skeleton">
                        <div class="modal-hero-skeleton">
                            <div class="skeleton-hero-badge"></div>
                            <div class="skeleton-hero-title"></div>
                            <div class="skeleton-hero-company"></div>
                            <div class="skeleton-hero-meta">
                                <div class="skeleton-meta-item"></div>
                                <div class="skeleton-meta-item"></div>
                            </div>
                        </div>

                        <div class="modal-content-skeleton">
                            <div class="skeleton-highlights-section">
                                <div class="skeleton-highlight-card">
                                    <div class="skeleton-highlight-icon"></div>
                                    <div class="skeleton-highlight-text">
                                        <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                                <div class="skeleton-highlight-card">
                                    <div class="skeleton-highlight-icon"></div>
                                    <div class="skeleton-highlight-text">
                                        <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                                <div class="skeleton-highlight-card">
                                    <div class="skeleton-highlight-icon"></div>
                                    <div class="skeleton-highlight-text">
                                        <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="skeleton-section">
                                <div class="skeleton-section-title"></div>
                                <div class="skeleton-description-block">
                                    <div class="skeleton-text skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text skeleton-text-md mb-2"></div>
                                    <div class="skeleton-text skeleton-text-sm"></div>
                                </div>
                            </div>

                            <div class="skeleton-section">
                                <div class="skeleton-section-title"></div>
                                <div class="skeleton-skills-row">
                                    <div class="skeleton-skill-pill"></div>
                                    <div class="skeleton-skill-pill"></div>
                                    <div class="skeleton-skill-pill"></div>
                                    <div class="skeleton-skill-pill-small"></div>
                                </div>
                            </div>

                            <div class="skeleton-section">
                                <div class="skeleton-section-title"></div>
                                <div class="skeleton-company-details">
                                    <div class="skeleton-text skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text skeleton-text-md mb-3"></div>
                                    <div class="skeleton-company-meta">
                                        <div class="skeleton-meta-row">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                        <div class="skeleton-meta-row">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                        <div class="skeleton-meta-row">
                                            <div class="skeleton-icon-small"></div>
                                            <div class="skeleton-text skeleton-text-sm"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Real modal content will be injected here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i> Tutup
                    </button>
                    <button type="button" class="btn btn-apply" id="btnApplyLowongan">
                        <i class="bi bi-send-fill me-2"></i>Ajukan Lamaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ TAMBAH setelah modal detail lowongan --}}

    <!-- Modal Upload Dokumen untuk Lamaran -->
    <div class="modal fade" id="uploadDocumentModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title">Detail Lamaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Berikut adalah CV yang akan dilampirkan pada lamaran Anda:
                    </div>

                    <div id="documentsCheckboxList">
                        <!-- CV akan ditampilkan di sini -->
                    </div>

                    <div class="alert alert-danger d-none" id="noCvAlert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Anda belum mengupload CV. Silakan upload CV terlebih dahulu di halaman profil.
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="submitApplicationBtn" class="btn btn-primary"
                            onclick="submitApplication(currentLowonganId)">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Lamaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/lowongan.css') }}">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ✅ DEBUG: Add console monitoring
        console.log('🚀 MhsLowongan page initialized');
        console.log('📍 Current URL:', window.location.href);

        // Perbaiki konfigurasi Axios
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

        // ✅ DEBUG: Monitor API calls
        api.interceptors.request.use(request => {
            console.log('📤 API Request:', request.method?.toUpperCase(), request.url);
            return request;
        });

        api.interceptors.response.use(
            response => {
                console.log('📥 API Response:', response.config.url, response.status, 'Success:', response.data
                    ?.success);
                return response;
            },
            error => {
                console.error('❌ API Error:', error.config?.url, error.response?.status, error.response?.data);
                return Promise.reject(error);
            }
        );

        // Variabel global untuk menyimpan data aplikasi user dan status magang aktif
        let userApplications = [];
        let hasActiveMagang = false;
        let activeMagangLowonganId = null;
        let isInitialLoad = true;

        // Fungsi untuk memeriksa apakah user telah melamar
        function hasApplied(lowonganId) {
            return userApplications.some(app => app.id_lowongan == lowonganId);
        }

        // Fungsi untuk mendapatkan status lamaran
        function getApplicationStatus(lowonganId) {
            const app = userApplications.find(app => app.id_lowongan == lowonganId);
            return app ? app.status : null;
        }

        // ✅ FIXED: Generate company description
        function generateCompanyDescription(perusahaan) {
            if (perusahaan.deskripsi && perusahaan.deskripsi !== '') {
                return perusahaan.deskripsi;
            }

            return `${perusahaan.nama_perusahaan || 'Perusahaan ini'} adalah perusahaan yang berlokasi di ${perusahaan.nama_kota || 'berbagai lokasi'} dan membuka kesempatan magang untuk mahasiswa. Untuk informasi lebih detail, silakan hubungi kontak yang tersedia.`;
        }

        // ✅ FIXED: Fungsi untuk memuat status magang aktif
        async function loadActiveMagangStatus() {
            try {
                console.log('🔍 Checking active internship status...');
                const response = await api.get('/mahasiswa/active-internship');

                if (response.data.success) {
                    hasActiveMagang = response.data.has_active_internship;
                    activeMagangLowonganId = response.data.active_internship ?
                        response.data.active_internship.id_lowongan : null;

                    console.log('✅ Active internship status loaded:', {
                        hasActiveMagang,
                        activeMagangLowonganId
                    });

                    if (hasActiveMagang) {
                        console.log('📢 User has active internship');
                    }
                }
            } catch (error) {
                console.error('❌ Error checking active internship status:', error);
            }
        }

        // ✅ FIXED: Fungsi untuk memuat data aplikasi user
        async function loadUserApplications() {
            try {
                console.log('📋 Loading user applications...');
                // Ganti endpoint yang salah dengan endpoint yang benar
                const response = await api.get('/mahasiswa/applications/user');

                if (response.data.success) {
                    userApplications = response.data.data || [];
                    console.log('✅ User applications loaded:', userApplications);
                }
            } catch (error) {
                console.error('❌ Error loading user applications:', error);
                userApplications = [];
            }
        }

        // ✅ FIXED: Enhanced function dengan loading animation
        async function loadLowongan(filters = {}) {
            console.log('🔄 Loading lowongan with filters:', filters);

            const container = document.getElementById('lowonganContainer');

            // Jika bukan initial load, tampilkan skeleton loading
            if (!isInitialLoad) {
                showSkeletonCards();
            }

            try {
                // Load data
                const response = await api.get('/mahasiswa/lowongan', {
                    params: filters
                });

                // Delay untuk menunjukkan skeleton loading effect
                const delay = isInitialLoad ? 2000 : 1500;

                setTimeout(() => {
                    if (response.data.success) {
                        console.log('✅ Lowongan data loaded:', response.data.data.length, 'items');
                        renderLowonganCards(response.data.data);
                    } else {
                        console.error('❌ API returned error:', response.data.message);
                        showErrorState(response.data.message || 'Terjadi kesalahan saat memuat data');
                    }
                    isInitialLoad = false;
                }, delay);

            } catch (error) {
                console.error('❌ Error loading lowongan:', error);
                setTimeout(() => {
                    showErrorState('Terjadi kesalahan pada server. Coba lagi nanti.');
                    isInitialLoad = false;
                }, isInitialLoad ? 2000 : 1500);
            }
        }

        function showSkeletonCards() {
            const container = document.getElementById('lowonganContainer');
            container.innerHTML = '';

            for (let i = 1; i <= 6; i++) {
                const skeletonCard = createSkeletonCard(i);
                container.appendChild(skeletonCard);
            }

            console.log('💀 Skeleton cards displayed');
        }

        function createSkeletonCard(index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'col-md-4 mb-4 skeleton-card-wrapper';
            wrapper.style.animationDelay = `${index * 0.1}s`;

            wrapper.innerHTML = `
                        <div class="lowongan-skeleton-card">
                            <div class="skeleton-card-header">
                                <div class="skeleton-company-section">
                                    <div class="skeleton-company-logo"></div>
                                    <div class="skeleton-company-text">
                                        <div class="skeleton-text skeleton-text-md mb-2"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="skeleton-card-divider"></div>

                            <div class="skeleton-card-body">
                                <div class="skeleton-info-section mb-3">
                                    <div class="skeleton-info-item">
                                        <div class="skeleton-icon-small"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                    <div class="skeleton-info-item">
                                        <div class="skeleton-icon-small"></div>
                                        <div class="skeleton-text skeleton-text-sm"></div>
                                    </div>
                                </div>

                                <div class="skeleton-skills-section mb-3">
                                    <div class="skeleton-skill-tag"></div>
                                    <div class="skeleton-skill-tag"></div>
                                    <div class="skeleton-skill-tag-small"></div>
                                </div>

                                <div class="skeleton-card-footer">
                                    <div class="skeleton-capacity-badge"></div>
                                    <div class="skeleton-view-button"></div>
                                </div>
                            </div>
                        </div>
                    `;

            return wrapper;
        }

        function createLowonganCard(lowongan, index) {
            // ...existing code...

            // Dapatkan status aplikasi langsung dari card data jika ada
            // Ini jika API mengembalikan status aplikasi bersama data lowongan
            const applicationStatus = lowongan.application_status || null;
            let applied = hasApplied(lowongan.id_lowongan);
            let status = getApplicationStatus(lowongan.id_lowongan);

            // Prioritaskan data dari API jika ada
            if (applicationStatus) {
                applied = true;
                status = applicationStatus.status;
            }

            let statusBadge = '';

            if (applied) {
                if (status === 'diterima') {
                    statusBadge = `<span class="badge-status badge-accepted">
                        <i class="bi bi-check-circle-fill me-1"></i> Diterima
                    </span>`;
                } else if (status === 'ditolak') {
                    statusBadge = `<span class="badge-status badge-rejected">
                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                    </span>`;
                } else {
                    statusBadge = `<span class="badge-status badge-waiting">
                        <i class="bi bi-hourglass-split me-1"></i> Menunggu
                    </span>`;
                }
            } else if (hasActiveMagang && lowongan.id_lowongan == activeMagangLowonganId) {
                statusBadge = `<span class="badge-status badge-active-magang">
                    <i class="bi bi-briefcase-fill me-1"></i> Magang Aktif
                </span>`;
            }

            // ...rest of the function...
        }

        function renderLowonganCards(lowonganData) {
            console.log('🎨 Rendering lowongan cards:', lowonganData.length, 'items');
            const container = document.getElementById('lowonganContainer');

            // Fade out skeleton cards
            const skeletonCards = container.querySelectorAll('.skeleton-card-wrapper');
            skeletonCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(-20px)';
                }, index * 50);
            });

            // Clear container after fade out
            setTimeout(() => {
                container.innerHTML = '';

                if (lowonganData.length === 0) {
                    console.log('📭 No lowongan data, showing empty state');
                    showEmptyState();
                    return;
                }

                // Render real cards with staggered animation
                lowonganData.forEach((lowongan, index) => {
                    setTimeout(() => {
                        const card = createLowonganCard(lowongan, index);
                        container.appendChild(card);

                        // Animate card in
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0) scale(1)';
                        }, 50);
                    }, index * 100);
                });

                console.log('✅ All lowongan cards rendered successfully');
            }, 300);
        }

        // ✅ FIXED: Function createLowonganCard dengan logo yang benar
        function createLowonganCard(lowongan, index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'col-md-4 mb-4 lowongan-card-wrapper';
            wrapper.style.opacity = '0';
            wrapper.style.transform = 'translateY(30px) scale(0.95)';
            wrapper.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';

            const perusahaan = lowongan.perusahaan || {};
            const skills = lowongan.skills || [];

            // Cek status aplikasi
            const applied = hasApplied(lowongan.id_lowongan);
            const status = getApplicationStatus(lowongan.id_lowongan);
            let statusBadge = '';

            if (applied) {
                if (status === 'diterima') {
                    statusBadge = `<span class="badge-status badge-accepted">
                                <i class="bi bi-check-circle-fill me-1"></i> Diterima
                            </span>`;
                } else if (status === 'ditolak') {
                    statusBadge = `<span class="badge-status badge-rejected">
                                <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                            </span>`;
                } else {
                    statusBadge = `<span class="badge-status badge-waiting">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu
                            </span>`;
                }
            } else if (hasActiveMagang && lowongan.id_lowongan == activeMagangLowonganId) {
                statusBadge = `<span class="badge-status badge-active-magang">
                            <i class="bi bi-briefcase-fill me-1"></i> Magang Aktif
                        </span>`;
            }

            // Generate skills HTML
            let skillsHTML = '';
            if (skills && skills.length > 0) {
                const visibleSkills = skills.slice(0, 3);
                skillsHTML = visibleSkills.map(skill =>
                    `<span class="badge-skill">${skill?.nama_skill || 'Skill'}</span>`
                ).join('');

                if (skills.length > 3) {
                    skillsHTML += `<span class="badge-more">+${skills.length - 3}</span>`;
                }
            } else {
                skillsHTML = '<span class="text-muted small">Tidak ada skill yang disebutkan</span>';
            }

            // ✅ FIXED: Logo handling yang benar
            let logoHTML;
            if (perusahaan.logo_url && perusahaan.logo_url !== null && perusahaan.logo_url !== '') {
                logoHTML =
                    `<img src="${perusahaan.logo_url}" 
                                   alt="Logo ${perusahaan.nama_perusahaan || 'Perusahaan'}"
                                   class="company-logo"
                                   onerror="handleCompanyLogoError(this, '${perusahaan.nama_perusahaan || 'Perusahaan'}')">`;
            } else {
                logoHTML = `<div class="company-logo-placeholder">
                                   <i class="bi bi-building" style="font-size: 1.5rem; color: #6c757d;"></i>
                               </div>`;
            }

            // ✅ FIXED: Min IPK badge jika ada
            let ipkBadge = '';
            if (lowongan.min_ipk && lowongan.min_ipk > 0) {
                ipkBadge = `<div class="d-flex align-items-center mb-2">
                                   <i class="bi bi-star-fill text-warning me-2"></i>
                                   <span class="text-muted small">Min. IPK ${parseFloat(lowongan.min_ipk).toFixed(2)}</span>
                               </div>`;
            }

            wrapper.innerHTML = `
                        <div class="card h-100 lowongan-card border-0 position-relative" onclick="showDetail(${lowongan.id_lowongan})">
                            ${statusBadge}
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="company-logo-wrapper me-3">
                                        ${logoHTML}
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold">${lowongan.judul_lowongan || 'Tidak ada judul'}</h5>
                                        <p class="text-primary mb-0 company-name">${perusahaan.nama_perusahaan || 'Tidak disebutkan'}</p>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div class="mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                        <span class="text-muted">${perusahaan.nama_kota || 'Lokasi tidak disebutkan'}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-calendar-fill text-success me-2"></i>
                                        <span class="text-muted">${lowongan.periode?.waktu || 'Periode tidak tersedia'}</span>
                                    </div>
                                    ${ipkBadge}
                                </div>

                                <div class="d-flex flex-wrap gap-1 mt-3">
                                    ${skillsHTML}
                                </div>

                                <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                                    <span class="capacity-badge">
                                        <i class="bi bi-people-fill me-1"></i> ${lowongan.kapasitas || 0} kuota
                                    </span>
                                    <button class="btn btn-sm btn-view">Lihat Detail <i class="bi bi-arrow-right ms-1"></i></button>
                                </div>
                            </div>
                        </div>
                    `;

            return wrapper;
        }

        // ✅ FIXED: Handle error logo perusahaan
        function handleCompanyLogoError(img, companyName) {
            console.warn('Company logo failed to load:', img.src);

            const placeholder = document.createElement('div');
            placeholder.className = 'company-logo-placeholder';
            placeholder.innerHTML =
                `<i class="bi bi-building" style="font-size: 1.5rem; color: #dc3545;" title="Logo ${companyName} tidak dapat dimuat"></i>`;

            img.parentNode.replaceChild(placeholder, img);
        }

        function showEmptyState() {
            const container = document.getElementById('lowonganContainer');
            container.innerHTML = `
                        <div class="col-12 text-center py-5 empty-state">
                            <div class="empty-icon mb-3">
                                <i class="bi bi-search" style="font-size: 3rem, color: #6c757d;"></i>
                            </div>
                            <h6 class="mb-2">Tidak ada lowongan yang sesuai</h6>
                            <p class="text-muted">Coba ubah filter pencarian Anda</p>
                        </div>
                    `;
        }

        function showErrorState(message) {
            const container = document.getElementById('lowonganContainer');
            container.innerHTML = `
                        <div class="col-12 text-center py-5 error-state">
                            <div class="error-icon mb-3">
                                <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #dc3545;"></i>
                            </div>
                            <h6 class="mb-2">Gagal memuat data</h6>
                            <p class="text-muted">${message}</p>
                            <button class="btn btn-primary btn-sm" onclick="initializePage()">
                                <i class="bi bi-arrow-clockwise me-1"></i>Coba Lagi
                            </button>
                        </div>
                    `;
        }

        // ✅ FIXED: Show detail function
        function showDetail(id) {
            console.log('👁️ Showing detail for lowongan:', id);

            // Show modal with skeleton loading
            const modal = new bootstrap.Modal(document.getElementById('lowonganDetailModal'));
            modal.show();

            // Show modal skeleton
            showModalSkeleton();

            // Fetch detail data
            api.get(`/mahasiswa/lowongan/${id}`)
                .then(response => {
                    setTimeout(() => {
                        if (response.data.success) {
                            hideModalSkeleton();
                            renderDetailLowongan(response.data.data, id);

                            // Gunakan application_status langsung dari response
                            const applicationStatus = response.data.data.application_status;
                            setupApplyButton(id, applicationStatus);
                        } else {
                            showModalError(response.data.message || 'Terjadi kesalahan');
                        }
                    }, 1200);
                })
                .catch(error => {
                    setTimeout(() => {
                        showModalError('Terjadi kesalahan pada server');
                    }, 1200);
                });
        }

        function showModalSkeleton() {
            const content = document.getElementById('lowonganDetailContent');
            const modalSkeleton = content.querySelector('#modal-skeleton');

            if (modalSkeleton) {
                modalSkeleton.style.display = 'block';
                modalSkeleton.style.opacity = '1';
            }
        }

        function hideModalSkeleton() {
            const content = document.getElementById('lowonganDetailContent');
            const modalSkeleton = content.querySelector('#modal-skeleton');

            if (modalSkeleton) {
                modalSkeleton.style.transition = 'opacity 0.4s ease';
                modalSkeleton.style.opacity = '0';

                setTimeout(() => {
                    modalSkeleton.style.display = 'none';
                }, 400);
            }
        }

        function showModalError(message) {
            const content = document.getElementById('lowonganDetailContent');
            content.innerHTML = `
                        <div class="text-center py-5">
                            <div class="error-icon mb-3">
                                <i class="bi bi-exclamation-circle" style="font-size: 3rem; color: #dc3545;"></i>
                            </div>
                            <h6 class="mb-2">Gagal memuat detail</h6>
                            <p class="text-muted">${message}</p>
                        </div>
                    `;
        }

        // ✅ FIXED: Render detail lowongan function
        function renderDetailLowongan(lowongan, id) {
            const perusahaan = lowongan.perusahaan || {};
            const skills = Array.isArray(lowongan.skills) ? lowongan.skills : [];
            const periode = lowongan.periode || {};

            const content = document.getElementById('lowonganDetailContent');

            // Generate skills HTML untuk modal
            let skillsHTML = '';
            if (skills && skills.length > 0) {
                skillsHTML = skills.map(skill =>
                    `<span class="skill-pill">${skill?.nama_skill || 'Skill'}</span>`
                ).join('');
            } else {
                skillsHTML = '<p class="text-muted mb-0">Tidak ada keahlian khusus yang disebutkan.</p>';
            }

            // Logo handling untuk modal
            let modalLogoHTML;
            if (perusahaan.logo_url && perusahaan.logo_url !== null && perusahaan.logo_url !== '') {
                modalLogoHTML =
                    `<img src="${perusahaan.logo_url}" 
                                       alt="${perusahaan.nama_perusahaan || 'Perusahaan'}" 
                                       class="company-hero-logo"
                                       onerror="handleModalLogoError(this, '${perusahaan.nama_perusahaan || 'Perusahaan'}')">`;
            } else {
                modalLogoHTML = `<div class="company-hero-logo-placeholder">
                                       <i class="bi bi-building"></i>
                                    </div>`;
            }

            // Min IPK info jika ada
            let ipkInfo = '';
            if (lowongan.min_ipk && lowongan.min_ipk > 0) {
                ipkInfo = `
                        <div class="highlight-card">
                            <div class="highlight-icon">
                                <i class="bi bi-star-fill"></i>
                            </div>
                            <div class="highlight-content">
                                <div class="highlight-title">Min. IPK</div>
                                <div class="highlight-value">${parseFloat(lowongan.min_ipk).toFixed(2)}</div>
                            </div>
                        </div>
                    `;
            }

            content.innerHTML = `
                    <div class="modal-hero">
                        <div class="modal-hero-overlay"></div>
                        <div class="modal-hero-content">
                            <div class="company-badge">
                                ${modalLogoHTML}
                            </div>
                            <h2 class="modal-hero-title">${lowongan.judul_lowongan || 'Tidak ada judul'}</h2>
                            <div class="modal-hero-company">${perusahaan.nama_perusahaan || 'Tidak disebutkan'}</div>
                            <div class="modal-hero-meta">
                                <span><i class="bi bi-geo-alt-fill"></i> ${perusahaan.nama_kota || 'Lokasi tidak disebutkan'}</span>
                                <span class="meta-divider">•</span>
                                <span><i class="bi bi-calendar-event-fill"></i> ${periode?.waktu || 'Periode tidak diketahui'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="detail-container">
                        <div class="detail-highlights">
                            <div class="highlight-card">
                                <div class="highlight-icon">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="highlight-content">
                                    <div class="highlight-title">Kuota</div>
                                    <div class="highlight-value">${lowongan.kapasitas || 0} orang</div>
                                </div>
                            </div>

                            <div class="highlight-card">
                                <div class="highlight-icon">
                                    <i class="bi bi-calendar-check-fill"></i>
                                </div>
                                <div class="highlight-content">
                                    <div class="highlight-title">Periode Magang</div>
                                    <div class="highlight-value">${periode?.waktu || 'Tidak diketahui'}</div>
                                </div>
                            </div>

                            ${ipkInfo}

                            <div class="highlight-card">
                                <div class="highlight-icon">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div class="highlight-content">
                                    <div class="highlight-title">Status</div>
                                    <div class="highlight-value">Aktif</div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="bi bi-file-text-fill me-2"></i>
                                Deskripsi
                            </h4>
                            <div class="section-content description-text">
                                ${lowongan.deskripsi || 'Tidak ada deskripsi tersedia.'}
                            </div>
                        </div>

                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="bi bi-award-fill me-2"></i>
                                Keahlian yang Dibutuhkan
                            </h4>
                            <div class="section-content">
                                <div class="skills-container">
                                    ${skillsHTML}
                                </div>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h4 class="section-title">
                                <i class="bi bi-building-fill me-2"></i>
                                Tentang Perusahaan
                            </h4>
                            <div class="section-content">
                                <p class="company-description">
                                    ${generateCompanyDescription(perusahaan)}
                                </p>
                                <div class="company-meta">
                                    <div class="company-meta-item">
                                        <i class="bi bi-link-45deg"></i>
                                        <span>${perusahaan.website || 'Website tidak tersedia'}</span>
                                    </div>
                                    <div class="company-meta-item">
                                        <i class="bi bi-envelope-fill"></i>
                                        <span>${perusahaan.email || 'Email tidak tersedia'}</span>
                                    </div>
                                    <div class="company-meta-item">
                                        <i class="bi bi-telephone-fill"></i>
                                        <span>${perusahaan.contact_person || 'No. Telepon tidak tersedia'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="detail-section apply-section">
                            <div class="apply-info">
                                <i class="bi bi-info-circle-fill"></i>
                                <span>Pastikan kualifikasi Anda sesuai dengan kebutuhan perusahaan sebelum mengajukan lamaran.</span>
                            </div>
                        </div>
                    </div>
                    `;
        }

        // Handle modal logo error
        function handleModalLogoError(img, companyName) {
            console.warn('Modal logo failed to load:', img.src);

            const placeholder = document.createElement('div');
            placeholder.className = 'company-hero-logo-placeholder';
            placeholder.innerHTML = `<i class="bi bi-building" title="Logo ${companyName} tidak dapat dimuat"></i>`;

            img.parentNode.replaceChild(placeholder, img);
        }

        function setupApplyButton(id, applicationStatus = null) {
            const btnApply = document.getElementById('btnApplyLowongan');

            // Default check dari global data jika applicationStatus tidak disediakan
            let isUserApplied = hasApplied(id);
            let applicationCurrentStatus = getApplicationStatus(id);

            // Jika applicationStatus disediakan dari response, gunakan itu
            if (applicationStatus) {
                isUserApplied = true;
                applicationCurrentStatus = applicationStatus.status;
                console.log('🔍 Using direct application status:', applicationCurrentStatus);
            }

            if (hasActiveMagang) {
                btnApply.disabled = true;
                btnApply.classList.remove('btn-primary', 'btn-apply');
                btnApply.classList.add('btn-outline-secondary');
                btnApply.innerHTML = '<i class="bi bi-info-circle me-2"></i>Anda memiliki magang aktif';
            } else if (isUserApplied) { // Gunakan isUserApplied, bukan hasApplied
                btnApply.disabled = true;
                btnApply.classList.remove('btn-primary', 'btn-apply');
                btnApply.classList.add('btn-outline-secondary');

                if (applicationCurrentStatus === 'diterima') {
                    btnApply.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Diterima';
                } else if (applicationCurrentStatus === 'ditolak') {
                    btnApply.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Ditolak';
                } else {
                    btnApply.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Menunggu Konfirmasi';
                }
            } else {
                // Jika belum melamar, aktifkan tombol
                btnApply.disabled = false;
                btnApply.classList.add('btn-primary', 'btn-apply');
                btnApply.classList.remove('btn-outline-secondary');
                btnApply.innerHTML = '<i class="bi bi-send-fill me-2"></i>Ajukan Lamaran';
                btnApply.onclick = () => showUploadDocumentModal(id);
            }

            // Debug log untuk membantu troubleshooting
            console.log('🔧 Button setup for lowongan:', id, {
                isUserApplied,
                applicationCurrentStatus,
                hasActiveMagang,
                buttonDisabled: btnApply.disabled,
                buttonText: btnApply.innerHTML
            });
        }

        // ✅ NEW: Filter functionality
        function setupFilters() {
            const applyFilterBtn = document.getElementById('applyFilter');
            const perusahaanFilter = document.getElementById('perusahaanFilter');
            const skillFilter = document.getElementById('skillFilter');

            if (applyFilterBtn) {
                applyFilterBtn.addEventListener('click', function() {
                    const filters = {
                        perusahaan_id: perusahaanFilter.value,
                        skill_id: skillFilter.value
                    };

                    console.log('🔍 Applying filters:', filters);
                    isInitialLoad = false; // Reset flag
                    loadLowongan(filters);
                });
            }
        }

        // ✅ MAIN INITIALIZATION FUNCTION
        async function initializePage() {
            console.log('🚀 Initializing MhsLowongan page...');

            try {
                // Load dependencies in parallel
                await Promise.all([
                    loadActiveMagangStatus(),
                    loadUserApplications() // ✅ Pastikan ini dipanggil
                ]);

                // Load lowongan data
                await loadLowongan();

                // Setup filters
                setupFilters();

                console.log('✅ Page initialization completed successfully');

            } catch (error) {
                console.error('❌ Error during page initialization:', error);
                showErrorState('Gagal menginisialisasi halaman. Silakan refresh.');
            }
        }

        // ✅ DOCUMENT UPLOAD FUNCTIONS (keeping existing ones but simplified for now)
        let currentLowonganId = null;
        let uploadedDocuments = [];
        let documentCounter = 0;

        async function confirmApplicationStatus(lowonganId) {
            try {
                console.log('🔎 Verifying application status before proceeding...');
                const response = await api.get(`/mahasiswa/lowongan/${lowonganId}/application-status`);

                if (response.data.success && response.data.has_applied) {
                    // User sudah melamar, update UI
                    Swal.fire({
                        title: 'Sudah Melamar',
                        text: 'Anda sudah melamar untuk lowongan ini',
                        icon: 'info'
                    });

                    // Reload data lamaran dan update UI
                    await loadUserApplications();
                    setupApplyButton(lowonganId, response.data.application_data);
                    return false;
                }

                // User belum melamar, lanjutkan
                return true;
            } catch (error) {
                console.error('❌ Error verifying application status:', error);
                return true; // Lanjutkan untuk berjaga-jaga
            }
        }

        async function showUploadDocumentModal(lowonganId) {
            console.log('🔄 Opening upload document modal for lowongan:', lowonganId);

            currentLowonganId = lowonganId;

            // Show modal
            const uploadModal = new bootstrap.Modal(document.getElementById('uploadDocumentModal'));
            uploadModal.show();

            // Load available documents
            loadAvailableDocuments();

            // Close detail modal if needed
            const detailModal = bootstrap.Modal.getInstance(document.getElementById('lowonganDetailModal'));
            if (detailModal) detailModal.hide();

            console.log('✅ Upload document modal opened successfully');
        }

        // Move loadAvailableDocuments outside to be a standalone function
        async function loadAvailableDocuments() {
            try {
                const container = document.getElementById('documentsCheckboxList');
                const noCvAlert = document.getElementById('noCvAlert');
                const submitBtn = document.getElementById('submitApplicationBtn');

                container.innerHTML =
                    '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary me-2"></div><span>Memuat dokumen...</span></div>';

                const response = await api.get('/mahasiswa/profile/documents');
                console.log('Documents response:', response.data);

                if (!response.data.success) {
                    container.innerHTML = '<div class="alert alert-danger">Gagal memuat dokumen.</div>';
                    return;
                }

                if (!response.data.has_cv) {
                    noCvAlert.classList.remove('d-none');
                    submitBtn.disabled = true;
                    container.innerHTML = `
                        <div class="alert alert-warning">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-circle fs-4 me-2"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">CV Belum Tersedia</h6>
                                    <p class="mb-0">Anda belum mengupload CV. Silakan upload CV terlebih dahulu di halaman profil.</p>
                                    <a href="/mahasiswa/profile" class="btn btn-sm btn-warning mt-2">
                                        <i class="fas fa-upload me-1"></i>Upload CV di Profil
                                    </a>
                                </div>
                            </div>
                        </div>`;
                    return;
                }

                noCvAlert.classList.add('d-none');
                submitBtn.disabled = false;

                const cv = response.data.documents[0];
                container.innerHTML = `
            <div class="card border">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                Curriculum Vitae (CV)
                            </h6>
                            ${cv.uploaded_at ? `
                                        <small class="text-muted">
                                            Diupload: ${new Date(cv.uploaded_at).toLocaleDateString()}
                                        </small>
                                    ` : ''}
                        </div>
                        <a href="${cv.url}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Lihat CV
                        </a>
                    </div>
                </div>
            </div>`;

            } catch (error) {
                console.error('Error loading documents:', error);
                document.getElementById('documentsCheckboxList').innerHTML =
                    '<div class="alert alert-danger">Terjadi kesalahan saat memuat dokumen.</div>';
            }
        }

        // Move updateSubmitButton outside to be a standalone function
        function updateSubmitButton() {
            const checkedDocs = document.querySelectorAll('.document-checkbox:checked');
            const submitBtn = document.getElementById('submitApplicationBtn');
            submitBtn.disabled = checkedDocs.length === 0;
        }

        // Only have one DOMContentLoaded event listener at the top level
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM Content Loaded, starting initialization...');
            initializePage();

            // Add event listener for upload modal
            const uploadModal = document.getElementById('uploadDocumentModal');
            if (uploadModal) {
                uploadModal.addEventListener('shown.bs.modal', function() {
                    loadAvailableDocuments();
                });
            }
        });

        // Function to submit application without using async/await
        function submitApplication(lowonganId) {
            // Show loading state
            Swal.fire({
                title: 'Mengirim Lamaran...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            console.log('📤 Submitting application for lowongan:', lowonganId);

            // Submit lamaran using promise syntax
            api.post(`/mahasiswa/apply-with-documents`, {
                    lowongan_id: lowonganId
                })
                .then(function(response) {
                    console.log('📥 Application response:', response.data);

                    if (response.data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal'));
                        modal.hide();

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Lamaran Anda telah berhasil dikirim',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reload lowongan data
                        setTimeout(function() {
                            loadLowongan();
                        }, 2000);
                    } else {
                        throw new Error(response.data.message || 'Gagal mengirim lamaran');
                    }
                })
                .catch(function(error) {
                    console.error('❌ Error submitting application:', error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengirim Lamaran',
                        text: error.response?.data?.message || 'Terjadi kesalahan saat mengirim lamaran',
                        confirmButtonText: 'Tutup'
                    });
                });
        }
    </script>
@endpush
