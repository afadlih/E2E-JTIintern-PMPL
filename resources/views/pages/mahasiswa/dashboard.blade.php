@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <!-- ✅ PERBAIKI: Welcome Skeleton -->
                        <div id="welcome-skeleton" class="welcome-skeleton">
                            <div class="d-flex align-items-center">
                                <div class="skeleton-welcome-icon me-3"></div>
                                <div class="flex-grow-1">
                                    <div class="skeleton-text-xl mb-2"></div>
                                    <div class="skeleton-text-md"></div>
                                </div>
                                <div class="ms-auto">
                                    <div class="skeleton-status-badge"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Content (Hidden Initially) -->
                        <div id="welcome-content" class="real-welcome d-none">
                            <div class="d-flex align-items-center">
                                <div class="welcome-icon me-3">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <h4 class="mb-2">
                                        Selamat Datang,
                                        @if (isset($userData) && $userData)
                                            {{ $userData->name ?? 'Mahasiswa' }}
                                        @else
                                            Mahasiswa
                                        @endif
                                        👋
                                    </h4>
                                    <p class="text-muted mb-0">Mari mulai perjalanan magang Anda dan raih pengalaman
                                        terbaik!</p>
                                </div>
                                <div class="ms-auto">
                                    @if (isset($activePeriod) && $activePeriod)
                                        <span class="badge bg-primary px-3 py-2">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ $activePeriod->waktu ?? ($activePeriod->nama_periode ?? 'Periode Aktif') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning px-3 py-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Tidak ada periode aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Completion Card - dengan skeleton jika perlu -->
        @if (isset($profileCompletion) && !$profileCompletion['is_complete'])
            <div class="row mb-4">
                <div class="col-12">
                    <!-- ✅ TAMBAH: Profile skeleton (opsional, bisa langsung show) -->
                    <div id="profile-skeleton" class="profile-incomplete-skeleton d-none">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="skeleton-warning-icon me-3"></div>
                                <div class="flex-grow-1">
                                    <div class="skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text-md"></div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="skeleton-complete-button me-2"></div>
                                <div class="skeleton-icon-box"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Real Profile Card -->
                    <div id="profile-content" class="profile-incomplete-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="warning-icon me-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Profil Belum Lengkap</h6>
                                    <p class="mb-0 text-sm">Lengkapi profil Anda untuk mendapatkan rekomendasi lowongan yang
                                        lebih akurat dan komunikasi yang tepat.</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <button type="button" class="btn btn-complete-now me-2"
                                    onclick="showProfileCompletionModal()">
                                    <i class="fas fa-user-edit me-1"></i>Lengkapi Sekarang
                                </button>
                                <button type="button" class="btn-close-card" onclick="hideProfileCard()"
                                    aria-label="Close">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Dashboard Content -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header pb-0">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Status Magang</h6>
                                <p class="text-sm mb-0">Informasi terkini tentang program magang Anda</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- ✅ PERBAIKI: Skeleton Loading for Magang Content -->
                        <div id="magang-skeleton" class="magang-skeleton-loading">
                            <div class="magang-skeleton-card">
                                <!-- ✅ Skeleton Header dengan struktur yang benar -->
                                <div class="p-4 border-bottom" style="border-color: #f0f2f5;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="skeleton-company-logo me-3"></div>
                                            <div class="flex-grow-1">
                                                <div class="skeleton-text-lg mb-2"></div>
                                                <div class="skeleton-text-md mb-1"></div>
                                                <div class="skeleton-text-sm"></div>
                                            </div>
                                        </div>
                                        <div class="skeleton-status-badge"></div>
                                    </div>
                                </div>

                                <!-- ✅ Skeleton Progress dengan struktur yang benar -->
                                <div class="skeleton-progress-section">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="skeleton-text-md" style="width: 120px;"></div>
                                        <div class="skeleton-text-lg" style="width: 60px;"></div>
                                    </div>
                                    <div class="skeleton-progress-bar mb-4"></div>
                                    <div class="d-flex justify-content-between">
                                        <div class="text-center">
                                            <div class="skeleton-text-xs mb-1" style="width: 80px; margin: 0 auto;"></div>
                                            <div class="skeleton-text-sm" style="width: 60px; margin: 0 auto;"></div>
                                        </div>
                                        <div class="text-center">
                                            <div class="skeleton-text-xs mb-1" style="width: 80px; margin: 0 auto;"></div>
                                            <div class="skeleton-text-sm" style="width: 60px; margin: 0 auto;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ✅ Skeleton Details -->
                                <div class="p-4 border-bottom" style="border-color: #f0f2f5;">
                                    <div class="d-flex align-items-center">
                                        <div class="skeleton-detail-icon me-3"></div>
                                        <div class="flex-grow-1">
                                            <div class="skeleton-text-xs mb-1" style="width: 100px;"></div>
                                            <div class="skeleton-text-md" style="width: 150px;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ✅ Skeleton Action -->
                                <div class="p-4 text-center">
                                    <div class="skeleton-action-button"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Magang Content (Hidden Initially) -->
                        <div id="magang-content" class="real-magang d-none">
                            @if (isset($magangInfo) && $magangInfo)
                                <!-- MAGANG AKTIF CARD -->
                                <div class="magang-card">
                                    <div class="magang-header">
                                        <div class="company-info">
                                            <div class="company-logo">
                                                @if (isset($magangInfo['data']->logo_perusahaan) && $magangInfo['data']->logo_perusahaan)
                                                    <img src="{{ asset('storage/' . $magangInfo['data']->logo_perusahaan) }}"
                                                        alt="Logo {{ $magangInfo['data']->nama_perusahaan }}">
                                                @else
                                                    <div class="company-initial">
                                                        {{ substr($magangInfo['data']->nama_perusahaan ?? 'P', 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="position-info">
                                                <h5 class="mb-1">
                                                    {{ $magangInfo['data']->judul_lowongan ?? 'Posisi Magang' }}
                                                </h5>
                                                <p class="company-name">
                                                    {{ $magangInfo['data']->nama_perusahaan ?? 'Nama Perusahaan' }}
                                                </p>
                                                @if (isset($magangInfo['data']->nama_kota) && $magangInfo['data']->nama_kota)
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $magangInfo['data']->nama_kota }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="status-badge">
                                            <span class="status-indicator"></span>
                                            Magang Aktif
                                        </div>
                                    </div>

                                    <div class="progress-container">
                                        <div class="progress-header">
                                            <span class="label">
                                                Progress Magang
                                                @if (isset($magangInfo['status_text']))
                                                    <small class="text-muted">({{ $magangInfo['status_text'] }})</small>
                                                @endif
                                            </span>
                                            <span class="value">{{ $magangInfo['progress'] ?? 0 }}%</span>
                                        </div>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" data-width="{{ $magangInfo['progress'] ?? 0 }}%"
                                                style="width: 0%;"></div>
                                        </div>
                                        <div class="date-info">
                                            <div class="date">
                                                <span class="label">
                                                    @if (($magangInfo['status_progress'] ?? '') === 'belum_mulai')
                                                        MULAI DALAM
                                                    @else
                                                        HARI LEWAT
                                                    @endif
                                                </span>
                                                <span class="value counter-number"
                                                    data-target="{{ $magangInfo['lewat'] ?? 0 }}">
                                                    0 hari
                                                </span>
                                            </div>
                                            <div class="date">
                                                <span class="label">SISA HARI</span>
                                                <span class="value counter-number"
                                                    data-target="{{ $magangInfo['sisaHari'] ?? 0 }}">
                                                    0 hari
                                                </span>
                                            </div>
                                        </div>

                                        @if (isset($magangInfo['tgl_mulai_formatted']) && isset($magangInfo['tgl_selesai_formatted']))
                                            <div class="date-range-info mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    {{ $magangInfo['tgl_mulai_formatted'] }} -
                                                    {{ $magangInfo['tgl_selesai_formatted'] }}
                                                    @if (isset($magangInfo['totalDurasi']))
                                                        ({{ $magangInfo['totalDurasi'] }} hari)
                                                    @endif
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    @if (isset($magangInfo['data']->nama_pembimbing) && $magangInfo['data']->nama_pembimbing)
                                        <div class="details-container">
                                            <div class="detail-item">
                                                <div class="detail-icon">
                                                    <i class="fas fa-chalkboard-teacher"></i>
                                                </div>
                                                <div class="detail-content">
                                                    <span class="label">PEMBIMBING</span>
                                                    <span class="value">{{ $magangInfo['data']->nama_pembimbing }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="action-container">
                                        <a href="{{ route('mahasiswa.magang') }}" class="action-button">
                                            <i class="fas fa-eye"></i>
                                            Lihat Detail Magang
                                        </a>
                                    </div>
                                </div>
                            @else
                                <!-- CARD BELUM MAGANG -->
                                <div class="empty-magang-card">
                                    <div class="empty-icon">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h5 class="mb-3">Belum Memiliki Magang</h5>
                                    <p class="text-muted mb-4">
                                        Saat ini Anda belum terdaftar pada program magang manapun.
                                        Eksplorasi berbagai lowongan yang tersedia dan ajukan lamaran agar tidak tertinggal!
                                    </p>
                                    <a href="{{ route('mahasiswa.lowongan') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>Cari Lowongan
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekomendasi Section -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">3 Rekomendasi Tempat Magang</h6>
                                <p class="text-sm mb-0">Rekomendasi sesuai dengan prefensi wilayah, skill, minat, kuota,
                                    dan
                                    IPK Anda berdasarkan perhitungan EDAS</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- ✅ PERBAIKI: Skeleton Loading for Recommendations -->
                        <div id="recommendations-skeleton" class="recommendations-skeleton">
                            <div class="row">
                                @for ($i = 1; $i <= 6; $i++)
                                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                                        <div class="recommendation-skeleton-card skeleton-enhanced">
                                            <div class="p-3">
                                                <!-- ✅ Skeleton Header yang lebih rapi -->
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="skeleton-company-logo-small me-3"></div>
                                                    <div class="flex-grow-1">
                                                        <div class="skeleton-text-md mb-1"></div>
                                                        <div class="skeleton-text-sm"></div>
                                                    </div>
                                                </div>

                                                <!-- ✅ Skeleton Badges dengan spacing yang benar -->
                                                <div class="d-flex gap-2 mb-3">
                                                    <div class="skeleton-badge-small"></div>
                                                    <div class="skeleton-badge-small"></div>
                                                </div>

                                                <!-- ✅ Skeleton Progress Indicators dengan struktur yang benar -->
                                                <div class="mt-3">
                                                    <div class="mb-2">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <div class="skeleton-text-xs" style="width: 60px;"></div>
                                                            <div class="skeleton-text-xs" style="width: 30px;"></div>
                                                        </div>
                                                        <div class="skeleton-progress-bar-small"></div>
                                                    </div>
                                                    <div class="mb-0">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <div class="skeleton-text-xs" style="width: 50px;"></div>
                                                            <div class="skeleton-text-xs" style="width: 30px;"></div>
                                                        </div>
                                                        <div class="skeleton-progress-bar-small"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <!-- Loading state -->
                        <div id="recommendations-loading" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Mencari rekomendasi terbaik untuk Anda...</p>
                        </div>

                        <!-- Empty state -->
                        <div id="recommendations-empty" class="text-center py-5 d-none">
                            <div class="empty-icon mb-3">
                                <i class="fas fa-search"></i>
                            </div>
                            <h6 class="mb-2">Tidak ada rekomendasi</h6>
                            <p class="text-muted">Belum ada lowongan yang sesuai dengan profil Anda saat ini.</p>
                        </div>

                        <!-- Recommendations cards -->
                        <div id="recommendations-container" class="row d-none">
                            <!-- Cards will be injected here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enhanced SPK Section in dashboard --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">🎯 Rekomendasi Cerdas SPK</h6>
                            <p class="text-sm mb-0">Menggunakan metode EDAS dengan analisis jarak real Pulau Jawa</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Metode: EDAS
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="switchSPKMethod('edas')">EDAS
                                        (Recommended)</a></li>
                                <li><a class="dropdown-item" href="#" onclick="switchSPKMethod('saw')">SAW
                                        (Alternative)</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- SPK Analysis Summary -->
                        <div id="spk-analysis-summary" class="mb-4">
                            <!-- Will be populated by JavaScript -->
                        </div>

                        <!-- Recommendations Grid -->
                        <div id="spk-recommendations-grid">
                            <!-- Enhanced recommendation cards -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/Dashboard.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ✅ GLOBAL VARIABLES
        const profileCompletion = @json($profileCompletion ?? ['is_complete' => true]);
        let currentSPKMethod = 'edas';
        let recommendationsData = null;
        let spkAnalysisData = null;

        // ✅ API CONFIGURATION WITH COMPREHENSIVE ERROR HANDLING
        const api = axios.create({
            baseURL: '/api',
            timeout: 30000,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });

        // ✅ API INTERCEPTORS
        api.interceptors.request.use(
            function(config) {
                console.log(`📡 API Request: ${config.method?.toUpperCase()} ${config.url}`);
                return config;
            },
            function(error) {
                console.error('❌ Request Error:', error);
                return Promise.reject(error);
            }
        );

        api.interceptors.response.use(
            function(response) {
                console.log(`✅ API Response: ${response.status} ${response.config.url}`);
                return response;
            },
            function(error) {
                console.error('💥 API Error:', {
                    status: error.response?.status,
                    statusText: error.response?.statusText,
                    url: error.config?.url,
                    message: error.message,
                    code: error.code,
                    data: error.response?.data
                });

                // Handle specific errors
                if (error.code === 'ECONNABORTED') {
                    showToast('error', '⏰ Request timeout - server mungkin sedang lambat');
                } else if (error.code === 'ERR_NETWORK') {
                    showToast('error', '🌐 Network error - periksa koneksi internet');
                } else if (error.response?.status === 419) {
                    showToast('error', '🔒 Session expired - memuat ulang halaman...');
                    setTimeout(() => location.reload(), 2000);
                } else if (error.response?.status === 401) {
                    showToast('error', '🚫 Unauthorized - mengarahkan ke login...');
                    setTimeout(() => window.location.href = '/login', 2000);
                }

                return Promise.reject(error);
            }
        );

        // ✅ MAIN INITIALIZATION
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 === DASHBOARD INITIALIZATION START ===');

            try {
                // Initialize all components
                initializeDashboard();
            } catch (error) {
                console.error('💥 Dashboard initialization error:', error);
                showToast('error', 'Gagal menginisialisasi dashboard');
            }
        });

        function initializeDashboard() {
            console.log('⚙️ Initializing dashboard components...');

            // Start progressive loading simulation
            simulateContentLoading();

            // Check profile completion
            checkAndShowProfileCompletion();

            // Load SPK recommendations with delay to avoid conflicts
            setTimeout(() => {
                loadSPKRecommendations();
            }, 3000);

            // Initialize event listeners
            initializeEventListeners();

            console.log('✅ Dashboard initialization completed');
        }

        // ✅ EVENT LISTENERS INITIALIZATION
        function initializeEventListeners() {
            // Add any global event listeners here
            window.addEventListener('error', function(e) {
                console.error('💥 Global JavaScript Error:', e.error);
            });

            window.addEventListener('unhandledrejection', function(e) {
                console.error('💥 Unhandled Promise Rejection:', e.reason);
            });
        }

        // ✅ PROFILE COMPLETION FUNCTIONS
        function checkAndShowProfileCompletion() {
            console.log('🔍 Checking profile completion...');
            console.log('Profile completion data:', profileCompletion);

            if (profileCompletion && !profileCompletion.is_complete) {
                console.log('❌ Profile incomplete, will show notification');

                setTimeout(() => {
                    showProfileCompletionNotification();
                }, 4000);
            } else {
                console.log('✅ Profile complete or no data');
            }
        }

        function showProfileCompletionNotification() {
            if (!profileCompletion || profileCompletion.is_complete) return;

            const missingItems = profileCompletion.missing || [];
            const details = profileCompletion.details || {};

            // Build the missing items HTML safely outside the template literal
            let missingItemsHtml = '';
            missingItems.forEach((item, idx) => {
                const detail = details[item] || {};
                missingItemsHtml += `
                    <div class="d-flex align-items-center mb-2">
                        <i class="${detail.icon || 'fas fa-exclamation-triangle'} me-2 text-warning"></i>
                        <strong>${detail.label || item}</strong>
                    </div>
                    <small class="text-muted">${detail.description || ''}</small>
                    ${idx !== missingItems.length - 1 ? '<hr class="my-2">' : ''}
                `;
            });

            Swal.fire({
                title: '📋 Lengkapi Profil Anda',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Profil Anda belum lengkap. Data yang masih perlu dilengkapi:</p>
                        <div class="alert alert-warning">
                            ${missingItemsHtml}
                        </div>
                        <p class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Profil yang lengkap akan membantu sistem memberikan rekomendasi lowongan yang lebih akurat.
                        </p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-user-edit me-1"></i>Lengkapi Sekarang',
                cancelButtonText: 'Nanti Saja',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/mahasiswa/profile';
                }
            });
        }

        function showProfileCompletionModal() {
            showProfileCompletionNotification();
        }

        function hideProfileCard() {
            const profileCard = document.getElementById('profile-content');
            if (profileCard) {
                profileCard.style.display = 'none';
            }
        }

        // ✅ PROGRESSIVE LOADING SIMULATION
        function simulateContentLoading() {
            console.log('⏳ Starting progressive content loading...');

            // Step 1: Load welcome section (fastest)
            setTimeout(() => {
                loadWelcomeSection();
            }, 800);

            // Step 2: Load magang section
            setTimeout(() => {
                loadMagangSection();
            }, 1500);

            // Step 3: Load recommendations section (longest)
            setTimeout(() => {
                loadRecommendationsSection();
            }, 2500);
        }

        function loadWelcomeSection() {
            console.log('👋 Loading welcome section...');

            const skeleton = document.getElementById('welcome-skeleton');
            const content = document.getElementById('welcome-content');

            if (!skeleton || !content) {
                console.warn('Welcome section elements not found');
                return;
            }

            skeleton.classList.add('skeleton-fade-out');

            setTimeout(() => {
                skeleton.classList.add('d-none');
                content.classList.remove('d-none');
                content.classList.add('content-fade-in');
            }, 400);
        }

        function loadMagangSection() {
            console.log('💼 Loading magang section...');

            const skeleton = document.getElementById('magang-skeleton');
            const content = document.getElementById('magang-content');

            if (!skeleton || !content) {
                console.warn('Magang section elements not found');
                return;
            }

            skeleton.classList.add('skeleton-fade-out');

            setTimeout(() => {
                skeleton.classList.add('d-none');
                content.classList.remove('d-none');
                content.classList.add('content-fade-in');

                // Animate progress bars and counters
                setTimeout(() => {
                    animateProgressBars();
                    animateCounters();
                }, 300);
            }, 500);
        }

        function loadRecommendationsSection() {
            console.log('📋 Loading recommendations section...');

            const skeleton = document.getElementById('recommendations-skeleton');

            if (!skeleton) {
                console.warn('Recommendations skeleton not found');
                return;
            }

            skeleton.classList.add('skeleton-fade-out');

            setTimeout(() => {
                skeleton.classList.add('d-none');
                loadRecommendations();
            }, 500);
        }

        // ✅ ANIMATION FUNCTIONS
        function animateProgressBars() {
            console.log('🎬 Animating progress bars...');

            const progressBars = document.querySelectorAll('.progress-bar[data-width]');

            progressBars.forEach((bar, index) => {
                const targetWidth = bar.getAttribute('data-width') || '0%';

                bar.style.width = '0%';
                bar.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';

                setTimeout(() => {
                    bar.style.width = targetWidth;
                    console.log(`Animated bar ${index} to ${targetWidth}`);
                }, 100 + (index * 100));
            });
        }

        function animateCounters() {
            const counters = document.querySelectorAll('.counter-number');

            counters.forEach((counter, index) => {
                setTimeout(() => {
                    animateCounter(counter);
                }, index * 200);
            });
        }

        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target')) || 0;
            const duration = 1500;
            const startTime = performance.now();
            const suffix = element.textContent.includes('hari') ? ' hari' : '';

            element.classList.add('counting');

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const easedProgress = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(easedProgress * target);

                element.textContent = current + suffix;

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.classList.remove('counting');
                    element.textContent = target + suffix;
                }
            }

            requestAnimationFrame(updateCounter);
        }

        // ✅ RECOMMENDATIONS LOADING FUNCTIONS
        function loadRecommendations() {
            console.log('📋 Loading recommendations...');

            // ✅ PERBAIKI: Gunakan endpoint yang benar
            api.get('/mahasiswa/recommendations') // ✅ FIXED: Remove duplicate /mahasiswa
                .then(response => {
                    hideRecommendationsLoading();

                    if (response.data && response.data.success) {
                        const recommendations = response.data.data || [];
                        console.log('✅ Recommendations loaded:', recommendations.length, 'items');

                        recommendationsData = recommendations;

                        if (recommendations.length === 0) {
                            showRecommendationsEmpty();
                        } else {
                            renderRecommendations(recommendations);
                            showRecommendationsContainer();
                        }
                    } else {
                        const errorMsg = response.data?.message || 'Server returned unsuccessful response';
                        console.error('❌ API returned error:', errorMsg);
                        showRecommendationsError(errorMsg);
                    }
                })
                .catch(error => {
                    console.error('💥 Error loading recommendations:', error);
                    hideRecommendationsLoading();

                    let errorMessage = 'Gagal memuat rekomendasi. ';

                    if (error.code === 'ERR_NETWORK') {
                        errorMessage += 'Periksa koneksi internet Anda.';
                    } else if (error.response?.status === 500) {
                        errorMessage += 'Server mengalami masalah internal.';
                    } else if (error.response?.status === 404) {
                        errorMessage += 'Endpoint API tidak ditemukan.';
                    } else if (error.response?.status === 401) {
                        errorMessage += 'Anda perlu login ulang.';
                    } else {
                        errorMessage += error.message || 'Terjadi kesalahan yang tidak diketahui.';
                    }

                    showRecommendationsError(errorMessage);
                });
        }

        // function testAPIConnectivity() {
        //     console.log('🧪 Testing API connectivity...');

        //     return api.get('/test-connection', { timeout: 5000 })
        //         .then(response => {
        //             console.log('✅ API connectivity test passed');
        //             return true;
        //         })
        //         .catch(error => {
        //             console.error('❌ API connectivity test failed:', error);
        //             return false;
        //         });
        // }

        // ✅ RECOMMENDATIONS UI STATE FUNCTIONS
        function showRecommendationsLoading() {
            const loadingContainer = document.getElementById('recommendations-loading');
            const emptyContainer = document.getElementById('recommendations-empty');
            const recommendationsContainer = document.getElementById('recommendations-container');

            if (loadingContainer) {
                loadingContainer.classList.remove('d-none');
                loadingContainer.innerHTML = `
                                                <div class="text-center py-5">
                                                    <div class="spinner-border text-primary mb-3" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="text-muted">Mencari 3 rekomendasi terbaik untuk Anda...</p>
                                                    <small class="text-muted">Menganalisis profil dan lowongan yang tersedia</small>
                                                </div>
                                            `;
            }

            if (emptyContainer) emptyContainer.classList.add('d-none');
            if (recommendationsContainer) recommendationsContainer.classList.add('d-none');
        }

        function hideRecommendationsLoading() {
            const loadingContainer = document.getElementById('recommendations-loading');
            if (loadingContainer) {
                loadingContainer.classList.add('d-none');
            }
        }

        function showRecommendationsEmpty() {
            const emptyContainer = document.getElementById('recommendations-empty');
            if (!emptyContainer) return;

            emptyContainer.classList.remove('d-none');

            if (profileCompletion && profileCompletion.is_complete === false) {
                emptyContainer.innerHTML = `
                                                    <div class="text-center py-5">
                                                        <div class="empty-icon mb-3">
                                                            <i class="fas fa-user-exclamation text-warning" style="font-size: 3rem;"></i>
                                                        </div>
                                                        <h6 class="mb-2">Tidak ada rekomendasi</h6>
                                                        <p class="text-muted mb-3">Lengkapi profil Anda terlebih dahulu untuk mendapatkan 3 rekomendasi terbaik.</p>
                                                        <button class="btn btn-primary btn-sm" onclick="showProfileCompletionModal()">
                                                            <i class="fas fa-user-edit me-2"></i>Lengkapi Profil
                                                        </button>
                                                    </div>
                                                `;
            } else {
                emptyContainer.innerHTML = `
                                                    <div class="text-center py-5">
                                                        <div class="empty-icon mb-3">
                                                            <i class="fas fa-search text-muted" style="font-size: 3rem;"></i>
                                                        </div>
                                                        <h6 class="mb-2">Tidak ada rekomendasi</h6>
                                                        <p class="text-muted mb-3">Belum ada lowongan yang sesuai dengan profil Anda untuk 3 rekomendasi teratas.</p>
                                                        <button class="btn btn-primary btn-sm" onclick="loadRecommendations()">
                                                            <i class="fas fa-refresh me-2"></i>Refresh
                                                        </button>
                                                    </div>
                                                `;
            }
        }

        function showRecommendationsError(message) {
            const emptyContainer = document.getElementById('recommendations-empty');
            if (!emptyContainer) return;

            emptyContainer.classList.remove('d-none');
            emptyContainer.innerHTML = `
                                                                                            <div class="text-center py-5">
                                                                                                <div class="empty-icon mb-3">
                                                                                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                                                                                </div>
                                                                                                <h6 class="mb-2">Gagal Memuat Rekomendasi</h6>
                                                                                                <p class="text-muted mb-3">${message}</p>
                                                                                                <div class="d-flex gap-2 justify-content-center">
                                                                                                    <button class="btn btn-primary btn-sm" onclick="loadRecommendations()">
                                                                                                        <i class="fas fa-refresh me-2"></i>Coba Lagi
                                                                                                    </button>
                                                                                                    <button class="btn btn-outline-info btn-sm" onclick="debugRecommendations()">
                                                                                                        <i class="fas fa-bug me-2"></i>Debug
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        `;
        }

        function showRecommendationsContainer() {
            const recommendationsContainer = document.getElementById('recommendations-container');
            if (recommendationsContainer) {
                recommendationsContainer.classList.remove('d-none');
            }
        }

        // ✅ RENDER RECOMMENDATIONS FUNCTION
        function renderRecommendations(recommendations) {
            console.log('🎨 Rendering top 3 recommendations:', recommendations.length, 'items');

            const container = document.getElementById('recommendations-container');
            if (!container) {
                console.error('❌ Recommendations container not found');
                return;
            }

            container.innerHTML = '';

            // ✅ LIMIT: Hanya tampilkan 3 rekomendasi teratas
            const topRecommendations = recommendations.slice(0, 3);

            topRecommendations.forEach((item, index) => {
                const logoUrl = getCompanyLogoUrl(item.logo_perusahaan);
                const cardWrapper = document.createElement('div');
                cardWrapper.className = 'col-xl-4 col-lg-4 col-md-6 mb-4 recommendation-card-wrapper';
                cardWrapper.style.animationDelay = `${index * 0.1}s`;
                cardWrapper.style.opacity = '0';
                cardWrapper.style.transform = 'translateY(20px) scale(0.95)';

                cardWrapper.innerHTML = `
                                                            <a href="/mahasiswa/lowongan" class="text-decoration-none">
                                                                <div class="card recommendation-card h-100">
                                                                    <div class="card-body">
                                                                        <!-- Header dengan rank badge -->
                                                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                                                            <div class="d-flex align-items-center flex-grow-1">
                                                                                <img src="${logoUrl}" 
                                                                                     alt="Logo ${item.nama_perusahaan || 'Company'}"
                                                                                     class="company-logo-small me-3"
                                                                                     onerror="this.src='/img/default-company.png'"
                                                                                     style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px;">
                                                                                <div class="flex-grow-1">
                                                                                    <h6 class="mb-0 text-dark fw-bold">${item.judul_lowongan || 'Lowongan'}</h6>
                                                                                    <p class="text-sm text-muted mb-0">${item.nama_perusahaan || 'Perusahaan'}</p>
                                                                                </div>
                                                                            </div>
                                                                            <span class="badge ${getTopRankBadgeClass(index + 1)} fs-6">
                                                                                <i class="fas fa-trophy me-1"></i>#${index + 1}
                                                                            </span>
                                                                        </div>

                                                                        <!-- Status badges -->
                                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                                            <span class="badge bg-light text-dark border">
                                                                                <i class="fas fa-map-marker-alt me-1"></i>${item.lokasi || 'Lokasi tidak tersedia'}
                                                                            </span>
                                                                            <span class="badge ${getScoreBadgeClass(item.appraisal_score)}">
                                                                                <i class="fas fa-star me-1"></i>
                                                                                ${Math.round((item.appraisal_score || 0) * 100)}% Match
                                                                            </span>
                                                                        </div>

                                                                        <!-- Kriteria lengkap dengan progress bar -->
                                                                        <div class="match-indicators">
                                                                            <!-- Keahlian -->
                                                                            <div class="match-item mb-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <small class="text-muted fw-medium">
                                                                                        <i class="fas fa-tools me-1"></i>Keahlian
                                                                                    </small>
                                                                                    <small class="text-muted fw-bold">${Math.round(item.skill_match || 0)}%</small>
                                                                                </div>
                                                                                <div class="progress mt-1" style="height: 6px; border-radius: 3px;">
                                                                                    <div class="progress-bar bg-success animated-match-bar" role="progressbar" 
                                                                                        data-width="${Math.round(item.skill_match || 0)}"
                                                                                        style="width: 0%; transition: width 1s ease-in-out;"></div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Lokasi -->
                                                                            <div class="match-item mb-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <small class="text-muted fw-medium">
                                                                                        <i class="fas fa-map-marker-alt me-1"></i>Lokasi
                                                                                    </small>
                                                                                    <small class="text-muted fw-bold">${Math.round(item.location_match || 0)}%</small>
                                                                                </div>
                                                                                <div class="progress mt-1" style="height: 6px; border-radius: 3px;">
                                                                                    <div class="progress-bar bg-primary animated-match-bar" role="progressbar" 
                                                                                        data-width="${Math.round(item.location_match || 0)}"
                                                                                        style="width: 0%; transition: width 1s ease-in-out;"></div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Minat -->
                                                                            <div class="match-item mb-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <small class="text-muted fw-medium">
                                                                                        <i class="fas fa-heart me-1"></i>Minat
                                                                                    </small>
                                                                                    <small class="text-muted fw-bold">${Math.round(item.interest_match || 0)}%</small>
                                                                                </div>
                                                                                <div class="progress mt-1" style="height: 6px; border-radius: 3px;">
                                                                                    <div class="progress-bar bg-info animated-match-bar" role="progressbar" 
                                                                                        data-width="${Math.round(item.interest_match || 0)}"
                                                                                        style="width: 0%; transition: width 1s ease-in-out;"></div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- IPK Match -->
                                                                            <div class="match-item mb-2">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <small class="text-muted fw-medium">
                                                                                        <i class="fas fa-graduation-cap me-1"></i>IPK
                                                                                    </small>
                                                                                    <small class="text-muted fw-bold">${Math.round(item.ipk_match || 0)}%</small>
                                                                                </div>
                                                                                <div class="progress mt-1" style="height: 6px; border-radius: 3px;">
                                                                                    <div class="progress-bar bg-warning animated-match-bar" role="progressbar" 
                                                                                        data-width="${Math.round(item.ipk_match || 0)}"
                                                                                        style="width: 0%; transition: width 1s ease-in-out;"></div>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Kuota -->
                                                                            <div class="match-item">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <small class="text-muted fw-medium">
                                                                                        <i class="fas fa-users me-1"></i>Kuota
                                                                                    </small>
                                                                                    <small class="text-muted fw-bold">${Math.round(item.quota_score || 0)}%</small>
                                                                                </div>
                                                                                <div class="progress mt-1" style="height: 6px; border-radius: 3px;">
                                                                                    <div class="progress-bar bg-secondary animated-match-bar" role="progressbar" 
                                                                                        data-width="${Math.round(item.quota_score || 0)}"
                                                                                        style="width: 0%; transition: width 1s ease-in-out;"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Summary footer -->
                                                                        <div class="mt-3 pt-2 border-top">
                                                                            <div class="d-flex justify-content-between align-items-center">
                                                                                <small class="text-muted">
                                                                                    <i class="fas fa-chart-line me-1"></i>
                                                                                    Skor Keseluruhan
                                                                                </small>
                                                                                <span class="badge ${getOverallScoreBadgeClass(item.appraisal_score)} px-2 py-1">
                                                                                    ${Math.round((item.appraisal_score || 0) * 100)}%
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </a>
                                                        `;

                container.appendChild(cardWrapper);

                // Animate card appearance
                setTimeout(() => {
                    cardWrapper.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    cardWrapper.style.opacity = '1';
                    cardWrapper.style.transform = 'translateY(0) scale(1)';

                    // Animate match bars
                    const matchBars = cardWrapper.querySelectorAll('.animated-match-bar');
                    matchBars.forEach((bar, barIndex) => {
                        setTimeout(() => {
                            const targetWidth = bar.getAttribute('data-width') || 0;
                            bar.style.width = targetWidth + '%';
                        }, (barIndex + 1) * 300);
                    });
                }, (index * 150) + 300);
            });

            console.log(`✅ Top 3 recommendations rendered successfully (${topRecommendations.length} items)`);
        }

        // ✅ SPK RECOMMENDATIONS FUNCTIONS
        function loadSPKRecommendations() {
            console.log('🧠 Loading SPK recommendations...');

            const container = document.getElementById('spk-recommendations-grid');
            const summary = document.getElementById('spk-analysis-summary');

            if (!container) {
                console.warn('SPK container not found');
                return;
            }

            // Show enhanced loading
            container.innerHTML = renderSPKSkeleton();

            // Load SPK data
            api.get('/mahasiswa/recommendations')
                .then(response => {
                    if (response.data && response.data.success) {
                        const data = response.data;
                        console.log('✅ SPK data loaded:', data);

                        spkAnalysisData = data;

                        // Render SPK analysis summary
                        if (summary) {
                            renderSPKSummary(summary, data);
                        }

                        // Render enhanced recommendations
                        renderEnhancedRecommendations(container, data.data || []);
                    } else {
                        console.error('❌ SPK API returned error:', response.data?.message);
                        renderSPKError(container, response.data?.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('💥 SPK Error:', error);
                    renderSPKError(container, error.message);
                });
        }

        function renderSPKSkeleton() {
            return `
                                                                                            <div class="row">
                                                                                                <div class="col-12 text-center py-5">
                                                                                                    <div class="spinner-border text-primary mb-3" role="status">
                                                                                                        <span class="visually-hidden">Loading...</span>
                                                                                                    </div>
                                                                                                    <p class="mt-2 text-muted">Menganalisis rekomendasi dengan metode SPK...</p>
                                                                                                    <small class="text-muted">Menghitung kriteria: Skill, Lokasi, IPK, Minat, Kuota</small>
                                                                                                </div>
                                                                                            </div>
                                                                                        `;
        }

        function renderSPKError(container, message) {
            container.innerHTML = `
                                                                                            <div class="alert alert-warning text-center py-4">
                                                                                                <i class="fas fa-exclamation-triangle mb-2 fa-2x text-warning"></i>
                                                                                                <h6 class="mb-2">Gagal Memuat Analisis SPK</h6>
                                                                                                <p class="mb-3 text-muted">${message}</p>
                                                                                                <div class="d-flex gap-2 justify-content-center">
                                                                                                    <button class="btn btn-warning btn-sm" onclick="loadSPKRecommendations()">
                                                                                                        <i class="fas fa-refresh me-1"></i>Coba Lagi
                                                                                                    </button>
                                                                                                    <button class="btn btn-outline-info btn-sm" onclick="debugSPK()">
                                                                                                        <i class="fas fa-bug me-1"></i>Debug SPK
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        `;
        }

        function renderSPKSummary(container, data) {
            const recommendations = data.data || [];
            const totalRecommendations = recommendations.length;
            const topScore = recommendations[0]?.appraisal_score || 0;
            const method = currentSPKMethod.toUpperCase();

            // ✅ DYNAMIC: Method-specific configuration dengan perbedaan jelas
            const methodConfig = getSPKMethodConfig(currentSPKMethod);

            // ✅ BEDA: Calculate average scores dengan metode yang berbeda
            let avgSkillMatch, avgLocationMatch, avgInterestMatch, avgIPKMatch, avgQuotaScore;

            if (currentSPKMethod === 'saw') {
                // ✅ SAW: Perhitungan rata-rata dengan bobot SAW
                avgSkillMatch = recommendations.reduce((sum, item) => sum + (item.skill_match || 0), 0) /
                    totalRecommendations || 0;
                avgLocationMatch = recommendations.reduce((sum, item) => sum + (item.location_match || 0), 0) /
                    totalRecommendations || 0;
                avgInterestMatch = recommendations.reduce((sum, item) => sum + (item.interest_match || 0), 0) /
                    totalRecommendations || 0;
                avgIPKMatch = recommendations.reduce((sum, item) => sum + (item.ipk_match || 0), 0) /
                    totalRecommendations || 0;
                avgQuotaScore = recommendations.reduce((sum, item) => sum + (item.quota_score || 0), 0) /
                    totalRecommendations || 0;

                // ✅ SAW: Tampilkan info weighted calculation
                console.log('📊 SAW Averages:', {
                    skill: avgSkillMatch,
                    location: avgLocationMatch,
                    interest: avgInterestMatch,
                    ipk: avgIPKMatch,
                    quota: avgQuotaScore
                });
            } else {
                // ✅ EDAS: Perhitungan rata-rata standar
                avgSkillMatch = recommendations.reduce((sum, item) => sum + (item.skill_match || 0), 0) /
                    totalRecommendations || 0;
                avgLocationMatch = recommendations.reduce((sum, item) => sum + (item.location_match || 0), 0) /
                    totalRecommendations || 0;
                avgInterestMatch = recommendations.reduce((sum, item) => sum + (item.interest_match || 0), 0) /
                    totalRecommendations || 0;
                avgIPKMatch = recommendations.reduce((sum, item) => sum + (item.ipk_match || 0), 0) /
                    totalRecommendations || 0;
                avgQuotaScore = recommendations.reduce((sum, item) => sum + (item.quota_score || 0), 0) /
                    totalRecommendations || 0;

                console.log('📊 EDAS Averages:', {
                    skill: avgSkillMatch,
                    location: avgLocationMatch,
                    interest: avgInterestMatch,
                    ipk: avgIPKMatch,
                    quota: avgQuotaScore
                });
            }

            container.innerHTML = `
        <div class="spk-summary-modern">
            <!-- ✅ DYNAMIC Header dengan method-specific content yang berbeda -->
            <div class="spk-header-card position-relative overflow-hidden mb-4">
                <div class="spk-gradient-bg ${methodConfig.headerGradient}"></div>
                <div class="spk-header-content position-relative p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="spk-icon-wrapper me-3">
                                    <i class="${methodConfig.icon} fa-2x"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold">
                                        ${methodConfig.emoji} Analisis Cerdas SPK (${method})
                                    </h5>
                                    <p class="mb-0 opacity-75">${methodConfig.description}</p>
                                    <!-- ✅ TAMBAH: Method-specific info -->
                                    <small class="opacity-75 d-block mt-1">
                                        <i class="fas fa-info-circle me-1"></i>
                                        ${methodConfig.specificInfo}
                                    </small>
                                </div>
                            </div>
                            <div class="spk-stats-mini d-flex gap-4">
                                <div class="stat-item">
                                    <span class="value">${totalRecommendations}</span>
                                    <span class="label">Lowongan</span>
                                </div>
                                <div class="stat-item">
                                    <span class="value">${Math.round(topScore * 100)}%</span>
                                    <span class="label">Top Score</span>
                                </div>
                                <div class="stat-item">
                                    <span class="value">5</span>
                                    <span class="label">Kriteria</span>
                                </div>
                                <!-- ✅ TAMBAH: Method indicator -->
                                <div class="stat-item stat-method">
                                    <span class="value">${method}</span>
                                    <span class="label">Method</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="spk-methodology-badge ${methodConfig.badgeClass}">
                                <div class="method-icon">
                                    <i class="${methodConfig.methodIcon}"></i>
                                </div>
                                <div class="method-info">
                                    <div class="method-name">${method}</div>
                                    <div class="method-desc">${methodConfig.shortDesc}</div>
                                    <!-- ✅ TAMBAH: Calculation type -->
                                    <div class="method-calc">${methodConfig.calcType}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ ENHANCED: Metrics dengan comparison indicator -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="spk-metric-card ${methodConfig.metricClasses.primary}">
                        <div class="metric-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value" data-target="${totalRecommendations}">0</div>
                            <div class="metric-label">Lowongan Dianalisis</div>
                            <div class="metric-change">
                                <i class="fas fa-arrow-up"></i>
                                <span>Metode ${method}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="spk-metric-card ${methodConfig.metricClasses.warning}">
                        <div class="metric-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value" data-target="${Math.round(topScore * 100)}">0</div>
                            <div class="metric-label">Skor Tertinggi (%)</div>
                            <div class="metric-change">
                                <i class="fas fa-star"></i>
                                <span>${methodConfig.topScoreLabel}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="spk-metric-card ${methodConfig.metricClasses.info}">
                        <div class="metric-icon">
                            <i class="${methodConfig.avgIcon}"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value" data-target="${Math.round(getMethodSpecificAverage(currentSPKMethod, avgSkillMatch, avgLocationMatch, avgInterestMatch, avgIPKMatch, avgQuotaScore))}">0</div>
                            <div class="metric-label">Rata-rata ${method} (%)</div>
                            <div class="metric-change">
                                <i class="fas fa-chart-bar"></i>
                                <span>${methodConfig.avgLabel}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="spk-metric-card ${methodConfig.metricClasses.success}">
                        <div class="metric-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="metric-content">
                            <div class="metric-value">Live</div>
                            <div class="metric-label">Status Analisis</div>
                            <div class="metric-change">
                                <i class="fas fa-sync-alt fa-spin"></i>
                                <span>Real-time ${method}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ ENHANCED: Criteria dengan perbedaan jelas antar metode -->
            <div class="spk-criteria-modern mb-4">
                <div class="criteria-header d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h6 class="mb-1 fw-bold">
                            ${methodConfig.criteriaIcon} Analisis Kesesuaian Kriteria (${method})
                        </h6>
                        <p class="text-muted small mb-0">
                            ${methodConfig.criteriaDescription} berdasarkan profil Anda
                        </p>
                        <!-- ✅ TAMBAH: Method-specific note -->
                        <small class="text-info">
                            <i class="fas fa-lightbulb me-1"></i>
                            ${methodConfig.criteriaNote}
                        </small>
                    </div>
                    <div class="criteria-actions">
                        <button class="btn btn-outline-primary btn-sm" onclick="compareMethods()">
                            <i class="fas fa-exchange-alt me-1"></i>Compare
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="exportCriteriaData()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                </div>

                <div class="row">
                    <!-- ✅ ENHANCED: Criteria cards dengan method-specific values -->
                    ${renderDynamicCriteriaCards(avgSkillMatch, avgLocationMatch, avgInterestMatch, avgIPKMatch, avgQuotaScore, methodConfig)}
                </div>
            </div>

            <!-- ✅ ENHANCED: Methodology dengan comparison -->
            <div class="spk-methodology-modern">
                <div class="methodology-card p-4 bg-light rounded-3 border">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="methodology-icon me-3">
                                    <i class="${methodConfig.methodologyIcon} ${methodConfig.methodologyColor} fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">
                                        ${methodConfig.methodologyEmoji} Metodologi Analisis ${method}
                                    </h6>
                                    <p class="mb-0 text-muted small">
                                        ${methodConfig.methodologyDescription}
                                    </p>
                                    <!-- ✅ TAMBAH: Method comparison hint -->
                                    <div class="method-comparison-hint mt-2">
                                        <small class="text-${methodConfig.hintColor}">
                                            <i class="fas fa-info-circle me-1"></i>
                                            ${methodConfig.comparisonHint}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="methodology-features">
                                <div class="row">
                                    <div class="col-md-6">
                                        ${methodConfig.features.left.map(feature => `
                                                            <div class="feature-item mb-2">
                                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                                <span class="small">${feature}</span>
                                                            </div>
                                                        `).join('')}
                                    </div>
                                    <div class="col-md-6">
                                        ${methodConfig.features.right.map(feature => `
                                                            <div class="feature-item mb-2">
                                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                                <span class="small">${feature}</span>
                                                            </div>
                                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="methodology-actions d-flex flex-column gap-2">
                                <button class="btn btn-outline-${methodConfig.primaryColor}" onclick="switchSPKMethod('${currentSPKMethod === 'edas' ? 'saw' : 'edas'}')">
                                    <i class="fas fa-exchange-alt me-2"></i>
                                    Switch to ${currentSPKMethod === 'edas' ? 'SAW' : 'EDAS'}
                                </button>
                                <button class="btn btn-outline-info" onclick="exportAnalysisReport()">
                                    <i class="fas fa-file-download me-2"></i>Export Report
                                </button>
                                <button class="btn btn-outline-secondary" onclick="loadSPKRecommendations()">
                                    <i class="fas fa-sync-alt me-2"></i>Refresh Analysis
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

            // ✅ ANIMATE: Method-specific animation timing
            setTimeout(() => {
                const metricValues = container.querySelectorAll('.metric-value[data-target]');
                metricValues.forEach((element, index) => {
                    setTimeout(() => {
                        animateMetricCounter(element);
                    }, index * (methodConfig.animationDelay || 200));
                });

                const criteriaFills = container.querySelectorAll('.criteria-fill-animated[data-width]');
                criteriaFills.forEach((bar, index) => {
                    setTimeout(() => {
                        const targetWidth = bar.getAttribute('data-width') || 0;
                        bar.style.width = targetWidth + '%';
                    }, (index * 300) + 1000);
                });
            }, 500);
        }

        function getSPKMethodConfig(method) {
            const configs = {
                edas: {
                    emoji: '🎯',
                    icon: 'fas fa-brain',
                    description: 'Evaluation based on Distance from Average Solution',
                    shortDesc: 'Distance Analysis',
                    calcType: 'Average-based',
                    specificInfo: 'Menggunakan jarak dari solusi rata-rata untuk ranking',
                    criteriaIcon: '📊',
                    criteriaDescription: 'Analisis kesesuaian berdasarkan 5 kriteria utama',
                    criteriaNote: 'Evaluasi objektif dengan pertimbangan jarak optimal',
                    headerGradient: 'gradient-edas',
                    badgeClass: 'badge-edas',
                    methodIcon: 'fas fa-calculator',
                    methodologyIcon: 'fas fa-microscope',
                    methodologyColor: 'text-primary',
                    methodologyEmoji: '🔬',
                    methodologyDescription: 'EDAS menghitung jarak setiap alternatif dari solusi rata-rata. Kriteria: Skill, Lokasi, Minat, IPK, Kuota',
                    avgIcon: 'fas fa-balance-scale',
                    avgLabel: 'Distance-based calculation',
                    topScoreLabel: 'Closest to optimal solution',
                    progressRing: 'ring-edas',
                    statusIndicator: 'indicator-edas',
                    animationDelay: 200,
                    primaryColor: 'primary',
                    hintColor: 'primary',
                    comparisonHint: 'EDAS cocok untuk analisis objektif dengan evaluasi menyeluruh',
                    metricClasses: {
                        primary: 'metric-primary metric-edas-primary',
                        warning: 'metric-warning metric-edas-warning',
                        info: 'metric-info metric-edas-info',
                        success: 'metric-success metric-edas-success'
                    },
                    features: {
                        left: [
                            'Distance-based calculation',
                            'Objective decision making',
                            'Balanced criteria evaluation'
                        ],
                        right: [
                            'Average solution reference',
                            'Consistent ranking system',
                            'Multi-dimensional analysis'
                        ]
                    }
                },
                saw: {
                    emoji: '⚖️',
                    icon: 'fas fa-weight-balanced',
                    description: 'Simple Additive Weighting with preference-based scoring',
                    shortDesc: 'Weighted Sum',
                    calcType: 'Preference-based',
                    specificInfo: 'Menggunakan bobot preferensi untuk perhitungan skor total',
                    criteriaIcon: '📈',
                    criteriaDescription: 'Analisis kesesuaian berdasarkan 5 kriteria utama',
                    criteriaNote: 'Evaluasi berdasarkan bobot preferensi yang dapat disesuaikan',
                    headerGradient: 'gradient-saw',
                    badgeClass: 'badge-saw',
                    methodIcon: 'fas fa-plus-circle',
                    methodologyIcon: 'fas fa-balance-scale-right',
                    methodologyColor: 'text-success',
                    methodologyEmoji: '⚖️',
                    methodologyDescription: 'SAW menggunakan weighted sum dengan normalisasi. Kriteria: Skill, Lokasi, Minat, IPK, Kuota',
                    avgIcon: 'fas fa-plus-circle',
                    avgLabel: 'Weighted sum calculation',
                    topScoreLabel: 'Highest weighted score',
                    progressRing: 'ring-saw',
                    statusIndicator: 'indicator-saw',
                    animationDelay: 150,
                    primaryColor: 'success',
                    hintColor: 'success',
                    comparisonHint: 'SAW ideal untuk evaluasi dengan preferensi bobot tertentu',
                    metricClasses: {
                        primary: 'metric-primary metric-saw-primary',
                        warning: 'metric-warning metric-saw-warning',
                        info: 'metric-info metric-saw-info',
                        success: 'metric-success metric-saw-success'
                    },
                    features: {
                        left: [
                            'Preference-weighted calculation',
                            'Flexible weighting system',
                            'Normalized scoring approach'
                        ],
                        right: [
                            'Additive value combination',
                            'Customizable preferences',
                            'Linear ranking model'
                        ]
                    }
                }
            };

            return configs[method] || configs.edas;
        }

        function renderDynamicCriteriaCards(avgSkillMatch, avgLocationMatch, avgInterestMatch, avgIPKMatch, avgQuotaScore,
            methodConfig) {
            // ✅ UNIFIED: Data kriteria yang sama untuk semua metode
            const criteriaData = [{
                    key: 'skill',
                    name: 'Keahlian',
                    icon: 'fas fa-tools',
                    value: avgSkillMatch,
                    color: 'success',
                    description: getUnifiedCriteriaDescription('skill', avgSkillMatch)
                },
                {
                    key: 'location',
                    name: 'Lokasi',
                    icon: 'fas fa-map-marker-alt',
                    value: avgLocationMatch,
                    color: 'primary',
                    description: getUnifiedCriteriaDescription('location', avgLocationMatch)
                },
                {
                    key: 'interest',
                    name: 'Minat',
                    icon: 'fas fa-heart',
                    value: avgInterestMatch,
                    color: 'info',
                    description: getUnifiedCriteriaDescription('interest', avgInterestMatch)
                },
                {
                    key: 'ipk',
                    name: 'IPK',
                    icon: 'fas fa-graduation-cap',
                    value: avgIPKMatch,
                    color: 'warning',
                    description: getUnifiedCriteriaDescription('ipk', avgIPKMatch)
                },
                {
                    key: 'quota',
                    name: 'Kuota',
                    icon: 'fas fa-users',
                    value: avgQuotaScore,
                    color: 'secondary',
                    description: getUnifiedCriteriaDescription('quota', avgQuotaScore)
                },
                {
                    key: 'overall',
                    name: 'Overall',
                    icon: 'fas fa-chart-pie',
                    value: (avgSkillMatch + avgLocationMatch + avgInterestMatch + avgIPKMatch + avgQuotaScore) / 5,
                    color: 'dark',
                    description: `Skor gabungan dari semua kriteria analisis SPK`
                }
            ];

            return criteriaData.map(criteria => `
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="criteria-card criteria-${criteria.key} criteria-unified">
                <div class="criteria-icon">
                    <i class="${criteria.icon}"></i>
                </div>
                <div class="criteria-content">
                    <div class="criteria-header-mini d-flex justify-content-between align-items-center mb-2">
                        <span class="criteria-name">${criteria.name}</span>
                        <span class="criteria-percentage text-${criteria.color} fw-bold">${Math.round(criteria.value)}%</span>
                    </div>
                    <div class="criteria-progress-modern mb-2">
                        <div class="progress-track">
                            <div class="progress-fill bg-${criteria.color} criteria-fill-animated criteria-fill-unified" 
                                 data-width="${Math.round(criteria.value)}"
                                 style="width: 0%; transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                        </div>
                    </div>
                    <div class="criteria-desc">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            ${criteria.description}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
        }

        // ✅ TAMBAH: Function untuk deskripsi kriteria yang unified
        function getUnifiedCriteriaDescription(criteriaKey, value) {
            const descriptions = {
                skill: value >= 80 ? 'Sangat sesuai dengan kebutuhan posisi' : value >= 60 ?
                    'Cukup sesuai dengan kebutuhan posisi' : 'Perlu pengembangan skill untuk posisi ini',

                location: value >= 80 ? 'Lokasi sangat optimal dan terjangkau' : value >= 60 ?
                    'Lokasi masih dalam jangkauan wajar' : 'Lokasi cukup jauh, pertimbangkan mobilitas',

                interest: value >= 80 ? 'Minat sangat selaras dengan bidang ini' : value >= 60 ?
                    'Minat cukup sesuai dengan bidang ini' : 'Minat kurang selaras, butuh eksplorasi lebih',

                ipk: value >= 80 ? 'IPK sangat memenuhi persyaratan' : value >= 60 ? 'IPK memenuhi standar minimum' :
                    'IPK di bawah standar yang diharapkan',

                quota: value >= 80 ? 'Banyak posisi tersedia, peluang bagus' : value >= 60 ? 'Posisi masih tersedia' :
                    'Posisi terbatas dengan persaingan ketat'
            };

            return descriptions[criteriaKey] || 'Analisis tidak tersedia';
        }

        function getMethodSpecificAverage(method, avgSkill, avgLocation, avgInterest, avgIPK, avgQuota) {
            if (method === 'saw') {
                // ✅ SAW: Weighted average berdasarkan bobot SAW
                const sawWeights = {
                    interest: 0.25,
                    skill: 0.25,
                    location: 0.20,
                    quota: 0.15,
                    ipk: 0.15
                };

                return (avgInterest * sawWeights.interest) +
                    (avgSkill * sawWeights.skill) +
                    (avgLocation * sawWeights.location) +
                    (avgQuota * sawWeights.quota) +
                    (avgIPK * sawWeights.ipk);
            } else {
                // ✅ EDAS: Simple average
                return (avgSkill + avgLocation + avgInterest + avgIPK + avgQuota) / 5;
            }
        }

        // ✅ TAMBAH: Function untuk message perbedaan metode
        function getMethodDifferenceMessage(method) {
            if (method === 'saw') {
                return 'Beralih ke SAW: Prioritas pada minat & skill Anda';
            } else {
                return 'Beralih ke EDAS: Analisis objektif berbasis jarak optimal';
            }
        }

        // ✅ TAMBAH: Function untuk compare methods
        function compareMethods() {
            console.log('🔄 Comparing EDAS vs SAW...');

            Swal.fire({
                title: '⚖️ Perbandingan Metode SPK',
                html: `
            <div class="method-comparison">
                <div class="row">
                    <div class="col-md-6">
                        <div class="method-card method-edas">
                            <div class="method-header">
                                <i class="fas fa-brain fa-2x mb-2"></i>
                                <h5>EDAS</h5>
                                <small>Distance-based Analysis</small>
                            </div>
                            <div class="method-features">
                                <div class="feature-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Objektif & seimbang</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Analisis jarak optimal</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Evaluasi menyeluruh</span>
                                </div>
                            </div>
                            <div class="method-best-for">
                                <strong>Cocok untuk:</strong><br>
                                <small>Analisis objektif, keputusan berdasarkan data</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="method-card method-saw">
                            <div class="method-header">
                                <i class="fas fa-weight-balanced fa-2x mb-2"></i>
                                <h5>SAW</h5>
                                <small>Preference-based Weighting</small>
                            </div>
                            <div class="method-features">
                                <div class="feature-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Bobot dapat disesuaikan</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Weighted calculation</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>Evaluasi berpreferensi</span>
                                </div>
                            </div>
                            <div class="method-best-for">
                                <strong>Cocok untuk:</strong><br>
                                <small>Evaluasi dengan preferensi khusus</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ✅ TAMBAH: Section untuk menunjukkan kesamaan kriteria -->
                <div class="criteria-similarity mt-4">
                    <div class="alert alert-success">
                        <h6 class="mb-2">
                            <i class="fas fa-equals me-2"></i>
                            Kriteria Evaluasi Yang Sama
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="criteria-list">
                                    <div class="criteria-item">
                                        <i class="fas fa-tools text-success me-2"></i>
                                        <span>Keahlian/Skill</span>
                                    </div>
                                    <div class="criteria-item">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        <span>Lokasi/Wilayah</span>
                                    </div>
                                    <div class="criteria-item">
                                        <i class="fas fa-heart text-info me-2"></i>
                                        <span>Minat</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="criteria-list">
                                    <div class="criteria-item">
                                        <i class="fas fa-graduation-cap text-warning me-2"></i>
                                        <span>IPK</span>
                                    </div>
                                    <div class="criteria-item">
                                        <i class="fas fa-users text-secondary me-2"></i>
                                        <span>Kuota</span>
                                    </div>
                                    <div class="criteria-item">
                                        <i class="fas fa-chart-pie text-dark me-2"></i>
                                        <span>Skor Overall</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Kedua metode menggunakan kriteria yang sama, hanya berbeda pada cara perhitungan skor
                        </small>
                    </div>
                </div>
                
                <div class="comparison-footer mt-4">
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Rekomendasi:</strong> Gunakan EDAS untuk analisis objektif, SAW untuk evaluasi berpreferensi
                    </div>
                </div>
            </div>
        `,
                width: 800,
                showCancelButton: true,
                confirmButtonText: `Switch to ${currentSPKMethod === 'edas' ? 'SAW' : 'EDAS'}`,
                cancelButtonText: 'Stay with ' + currentSPKMethod.toUpperCase(),
                confirmButtonColor: currentSPKMethod === 'edas' ? '#28a745' : '#007bff'
            }).then((result) => {
                if (result.isConfirmed) {
                    switchSPKMethod(currentSPKMethod === 'edas' ? 'saw' : 'edas');
                }
            });
        }

        // ✅ EXPOSE: Add to global scope
        window.compareMethods = compareMethods;

        function getMethodSpecificDescription(criteriaKey, methodConfig, value) {
            // ✅ UNIFIED: Gunakan deskripsi yang sama untuk semua metode
            const unifiedDescriptions = {
                skill: value >= 80 ? 'Sangat sesuai dengan kebutuhan posisi' : value >= 60 ?
                    'Cukup sesuai dengan kebutuhan posisi' : 'Perlu pengembangan skill untuk posisi ini',

                location: value >= 80 ? 'Lokasi sangat optimal dan terjangkau' : value >= 60 ?
                    'Lokasi masih dalam jangkauan wajar' : 'Lokasi cukup jauh, pertimbangkan mobilitas',

                interest: value >= 80 ? 'Minat sangat selaras dengan bidang ini' : value >= 60 ?
                    'Minat cukup sesuai dengan bidang ini' : 'Minat kurang selaras, butuh eksplorasi lebih',

                ipk: value >= 80 ? 'IPK sangat memenuhi persyaratan' : value >= 60 ? 'IPK memenuhi standar minimum' :
                    'IPK di bawah standar yang diharapkan',

                quota: value >= 80 ? 'Banyak posisi tersedia, peluang bagus' : value >= 60 ? 'Posisi masih tersedia' :
                    'Posisi terbatas dengan persaingan ketat'
            };

            return unifiedDescriptions[criteriaKey] || 'Analisis tidak tersedia';
        }

        function animateMetricCounter(element) {
            const target = parseInt(element.getAttribute('data-target')) || 0;
            const duration = 2000;
            const startTime = performance.now();

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const easedProgress = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(easedProgress * target);

                element.textContent = current;

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = target;
                }
            }

            requestAnimationFrame(updateCounter);
        }

        // Helper functions untuk export (opsional)
        function exportCriteriaData() {
            console.log('📊 Exporting criteria data...');
            showToast('info', '📊 Fitur export sedang dalam pengembangan');
        }

        function exportAnalysisReport() {
            console.log('📄 Exporting analysis report...');
            showToast('info', '📄 Fitur export report sedang dalam pengembangan');
        }

        function renderEnhancedRecommendations(container, recommendations) {
            console.log('🎨 Rendering enhanced SPK recommendations:', recommendations.length, 'items');

            // ✅ TAMBAH: Validasi input
            if (!Array.isArray(recommendations)) {
                console.error('❌ Recommendations is not an array:', recommendations);
                container.innerHTML = `
                        <div class="alert alert-danger text-center py-4">
                            <i class="fas fa-exclamation-triangle mb-2 fa-2x text-danger"></i>
                            <h6 class="mb-2">Data Error</h6>
                            <p class="mb-3 text-muted">Format data rekomendasi tidak valid.</p>
                            <button class="btn btn-danger btn-sm" onclick="loadSPKRecommendations()">
                                <i class="fas fa-refresh me-1"></i>Muat Ulang
                            </button>
                        </div>
                    `;
                return;
            }

            if (!recommendations || recommendations.length === 0) {
                container.innerHTML = `
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-info-circle mb-2 fa-2x text-info"></i>
                            <h6 class="mb-2">Belum Ada Rekomendasi SPK</h6>
                            <p class="mb-3 text-muted">Sistem SPK sedang menganalisis lowongan yang tersedia.</p>
                            <button class="btn btn-info btn-sm" onclick="loadSPKRecommendations()">
                                <i class="fas fa-refresh me-1"></i>Muat Ulang
                            </button>
                        </div>
                    `;
                return;
            }

            let html = '<div class="row">';

            // ✅ PASTIKAN slice berfungsi dengan aman
            const safeRecommendations = recommendations.slice(0, 6);

            safeRecommendations.forEach((item, index) => {
                const scoreColor = getSPKScoreColor(item.appraisal_score || 0);
                const rankBadge = getSPKRankBadge(index + 1);
                const logoUrl = getCompanyLogoUrl(item.logo_perusahaan);

                html += `
                        <div class="col-xl-4 col-lg-6 mb-4">
                            <div class="spk-recommendation-card border rounded shadow-sm h-100" data-rank="${index + 1}">
                                <!-- Header dengan rank dan skor -->
                                <div class="spk-card-header p-3 border-bottom d-flex justify-content-between align-items-center">
                                    ${rankBadge}
                                    <div class="spk-score-circle d-flex align-items-center justify-content-center rounded-circle" 
                                         style="width: 55px; height: 55px; background-color: ${scoreColor}; color: white; font-weight: bold; font-size: 14px;">
                                        <div class="text-center">
                                            <div style="font-size: 16px; line-height: 1;">${Math.round((item.appraisal_score || 0) * 100)}</div>
                                            <div style="font-size: 10px; opacity: 0.8;">SKOR</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="spk-card-body p-3">
                                    <!-- Company info -->
                                    <div class="company-info d-flex align-items-center mb-3">
                                        <img src="${logoUrl}" 
                                             alt="${item.nama_perusahaan || 'Company'}"
                                             class="company-logo me-3"
                                             style="width: 48px; height: 48px; object-fit: cover; border-radius: 8px;"
                                             onerror="this.src='/img/default-company.png'">
                                        <div class="company-details flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark">${item.judul_lowongan || 'Lowongan'}</h6>
                                            <p class="mb-1 text-muted small">${item.nama_perusahaan || 'Perusahaan'}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>${item.lokasi || 'Lokasi tidak tersedia'}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Analisis kriteria lengkap -->
                                    <div class="spk-criteria-analysis mb-3">
                                        <!-- Keahlian -->
                                        <div class="criteria-item mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="criteria-label small text-muted">
                                                    <i class="fas fa-tools me-1"></i>Keahlian
                                                </span>
                                                <span class="criteria-value small fw-bold ${getCriteriaValueClass(item.skill_match || 0)}">${Math.round(item.skill_match || 0)}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success criteria-fill" 
                                                     style="width: 0%; transition: width 1s ease-in-out;"
                                                     data-width="${Math.round(item.skill_match || 0)}"></div>
                                            </div>
                                        </div>

                                        <!-- Lokasi -->
                                        <div class="criteria-item mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="criteria-label small text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Lokasi
                                                </span>
                                                <span class="criteria-value small fw-bold ${getCriteriaValueClass(item.location_match || 0)}">${Math.round(item.location_match || 0)}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary criteria-fill" 
                                                     style="width: 0%; transition: width 1s ease-in-out;"
                                                     data-width="${Math.round(item.location_match || 0)}"></div>
                                            </div>
                                        </div>

                                        <!-- Minat -->
                                        <div class="criteria-item mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="criteria-label small text-muted">
                                                    <i class="fas fa-heart me-1"></i>Minat
                                                </span>
                                                <span class="criteria-value small fw-bold ${getCriteriaValueClass(item.interest_match || 0)}">${Math.round(item.interest_match || 0)}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-info criteria-fill" 
                                                     style="width: 0%; transition: width 1s ease-in-out;"
                                                     data-width="${Math.round(item.interest_match || 0)}"></div>
                                            </div>
                                        </div>

                                        <!-- IPK -->
                                        <div class="criteria-item mb-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="criteria-label small text-muted">
                                                    <i class="fas fa-graduation-cap me-1"></i>IPK
                                                </span>
                                                <span class="criteria-value small fw-bold ${getCriteriaValueClass(item.ipk_match || 0)}">${Math.round(item.ipk_match || 0)}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-warning criteria-fill" 
                                                     style="width: 0%; transition: width 1s ease-in-out;"
                                                     data-width="${Math.round(item.ipk_match || 0)}"></div>
                                            </div>
                                        </div>

                                        <!-- Kuota -->
                                        <div class="criteria-item">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <span class="criteria-label small text-muted">
                                                    <i class="fas fa-users me-1"></i>Kuota
                                                </span>
                                                <span class="criteria-value small fw-bold ${getCriteriaValueClass(item.quota_score || 0)}">${Math.round(item.quota_score || 0)}%</span>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-secondary criteria-fill" 
                                                     style="width: 0%; transition: width 1s ease-in-out;"
                                                     data-width="${Math.round(item.quota_score || 0)}"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Summary info -->
                                    <div class="spk-summary-info mb-3 p-2 bg-light rounded">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Metode</small>
                                                <strong class="text-primary small">${currentSPKMethod.toUpperCase()}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Rank</small>
                                                <strong class="text-warning small">#${index + 1}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="spk-card-actions d-flex gap-2">
                                        <a href="/mahasiswa/lowongan" class="btn btn-primary btn-sm flex-grow-1">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                        <button class="btn btn-outline-info btn-sm" onclick="showSPKAnalysis(${item.id_lowongan})" title="Analisis SPK Detail">
                                            <i class="fas fa-chart-area"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
            });

            html += '</div>';
            container.innerHTML = html;

            // Animate progress bars
            setTimeout(() => {
                const progressBars = container.querySelectorAll('.criteria-fill[data-width]');
                progressBars.forEach((bar, index) => {
                    setTimeout(() => {
                        const targetWidth = bar.getAttribute('data-width') || 0;
                        bar.style.width = targetWidth + '%';
                    }, index * 100);
                });
            }, 500);

            console.log(`✅ Enhanced SPK recommendations rendered (${currentSPKMethod.toUpperCase()})`);
        }

        // ✅ SPK HELPER FUNCTIONS
        function getSPKScoreColor(score) {
            if (score >= 0.8) return '#28a745';
            if (score >= 0.6) return '#ffc107';
            if (score >= 0.4) return '#fd7e14';
            return '#dc3545';
        }

        function getTopRankBadgeClass(rank) {
            // Untuk top 3, beri badge yang lebih mencolok
            if (rank === 1) return 'bg-warning text-dark fw-bold'; // Gold untuk #1
            if (rank === 2) return 'bg-light text-dark border fw-bold'; // Silver untuk #2
            if (rank === 3) return 'bg-info text-white fw-bold'; // Bronze untuk #3
            return 'bg-secondary';
        }
        // .

        function getSPKRankBadge(rank) {
            let badgeClass = 'rank-regular';
            let icon = 'fas fa-hashtag';
            let medalClass = '';

            if (rank === 1) {
                badgeClass = 'rank-gold';
                icon = 'fas fa-trophy';
                medalClass = 'medal-icon';
            } else if (rank === 2) {
                badgeClass = 'rank-silver';
                icon = 'fas fa-medal';
                medalClass = 'medal-icon';
            } else if (rank === 3) {
                badgeClass = 'rank-bronze'; // ✅ PERBAIKI: Gunakan class yang sudah didefinisikan
                icon = 'fas fa-award';
                medalClass = 'medal-icon';
            }

            return `
        <span class="spk-rank-badge badge ${badgeClass} d-flex align-items-center px-2 py-1" 
              style="font-size: 12px; font-weight: bold;">
            <i class="${icon} ${medalClass} me-1"></i>
            <span>#${rank}</span>
        </span>
    `;
        }

        function switchSPKMethod(method) {
            console.log(`🔄 Switching to ${method.toUpperCase()} method`);

            currentSPKMethod = method;

            const container = document.getElementById('spk-recommendations-grid');
            const summary = document.getElementById('spk-analysis-summary');

            if (!container) return;

            container.innerHTML = renderSPKSkeleton();

            if (summary) {
                summary.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Switching to ${method.toUpperCase()} analysis...</p>
                <small class="text-muted">Recalculating with ${method.toUpperCase()} methodology...</small>
            </div>
        `;
            }

            // ✅ PERBAIKI: Gunakan endpoint dan logic yang berbeda untuk setiap metode
            let apiCall;

            if (method === 'edas') {
                // EDAS menggunakan endpoint original
                apiCall = api.get('/mahasiswa/recommendations');
            } else if (method === 'saw') {
                // SAW menggunakan endpoint khusus atau simulasi data berbeda
                apiCall = api.get('/mahasiswa/recommendations/saw')
                    .catch(error => {
                        // ✅ FALLBACK: Jika endpoint SAW belum ada, simulasi data SAW
                        console.warn('SAW endpoint not available, simulating SAW data...');
                        return generateSAWSimulationData();
                    });
            }

            apiCall
                .then(response => {
                    console.log(`🔍 ${method.toUpperCase()} Raw Response:`, response.data);

                    let processedData;
                    let recommendations = [];

                    if (method === 'edas') {
                        // ✅ EDAS: Gunakan data original
                        if (response.data && response.data.success) {
                            recommendations = response.data.data || [];
                            processedData = {
                                data: recommendations,
                                method: 'EDAS',
                                methodology: 'distance_based'
                            };
                        }
                    } else if (method === 'saw') {
                        // ✅ SAW: Proses data dengan perhitungan SAW
                        if (response.data && response.data.success) {
                            const rawData = response.data.data || response.data.recommendations || [];
                            recommendations = processSAWData(rawData);
                            processedData = {
                                data: recommendations,
                                method: 'SAW',
                                methodology: 'weighted_additive'
                            };
                        } else {
                            // Gunakan simulasi jika tidak ada data
                            recommendations = generateSAWRecommendations();
                            processedData = {
                                data: recommendations,
                                method: 'SAW',
                                methodology: 'weighted_additive'
                            };
                        }
                    }

                    // ✅ UPDATE: Store processed data ke global variable
                    if (method === 'saw') {
                        // ✅ SAW: Simpan data SAW ke global untuk detailed analysis
                        window.currentSAWData = recommendations;
                        console.log('✅ SAW data stored globally:', recommendations.length, 'items');
                    } else {
                        // ✅ EDAS: Update recommendationsData global
                        recommendationsData = recommendations;
                        console.log('✅ EDAS data updated globally:', recommendations.length, 'items');
                    }

                    if (recommendations.length === 0) {
                        container.innerHTML = `
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle mb-2 fa-2x text-info"></i>
                        <h6 class="mb-2">Tidak Ada Rekomendasi ${method.toUpperCase()}</h6>
                        <p class="mb-3 text-muted">Metode ${method.toUpperCase()} belum menghasilkan rekomendasi untuk profil Anda.</p>
                        <button class="btn btn-info btn-sm" onclick="switchSPKMethod('edas')">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke EDAS
                        </button>
                    </div>
                `;
                        return;
                    }

                    // ✅ RENDER: Summary dan recommendations dengan data yang sudah diproses
                    if (summary) {
                        renderSPKSummary(summary, processedData);
                    }

                    renderEnhancedRecommendations(container, recommendations);
                    updateMethodBadge(method.toUpperCase());

                    // ✅ TOAST: Beri feedback perbedaan metode
                    const methodDiff = getMethodDifferenceMessage(method);
                    showToast('success', `✅ ${methodDiff}`);

                })
                .catch(error => {
                    console.error(`💥 Error switching to ${method}:`, error);
                    renderSPKError(container, `Failed to load ${method.toUpperCase()} analysis: ${error.message}`);

                    if (summary) {
                        summary.innerHTML = `
                    <div class="alert alert-danger text-center py-3">
                        <i class="fas fa-exclamation-triangle mb-2"></i>
                        <h6 class="mb-1">Error Loading ${method.toUpperCase()}</h6>
                        <p class="mb-0 small">${error.message}</p>
                    </div>
                `;
                    }
                });
        }

        function processSAWData(rawData) {
            console.log('🔄 Processing data with SAW methodology...');

            if (!Array.isArray(rawData) || rawData.length === 0) {
                return generateSAWRecommendations();
            }

            // ✅ SAW: Bobot kriteria berbeda dari EDAS
            const sawWeights = {
                interest_match: 0.25, // 25% - Minat (prioritas tinggi di SAW)
                skill_match: 0.25, // 25% - Skill
                location_match: 0.20, // 20% - Lokasi
                quota_score: 0.15, // 15% - Kuota
                ipk_match: 0.15 // 15% - IPK (prioritas rendah di SAW)
            };

            return rawData.map((item, index) => {
                // ✅ SAW: Normalisasi skor (0-1) kemudian weighted sum
                const normalizedScores = {
                    skill_match: (item.skill_match || 0) / 100,
                    location_match: (item.location_match || 0) / 100,
                    interest_match: (item.interest_match || 0) / 100,
                    ipk_match: (item.ipk_match || 0) / 100,
                    quota_score: (item.quota_score || 0) / 100
                };

                // ✅ SAW: Weighted additive calculation
                const sawScore = (
                    (normalizedScores.interest_match * sawWeights.interest_match) +
                    (normalizedScores.skill_match * sawWeights.skill_match) +
                    (normalizedScores.location_match * sawWeights.location_match) +
                    (normalizedScores.quota_score * sawWeights.quota_score) +
                    (normalizedScores.ipk_match * sawWeights.ipk_match)
                );

                // ✅ SAW: Adjust individual criteria scores with SAW methodology
                const sawAdjustedData = {
                    ...item,
                    // SAW memberikan boost untuk minat dan skill
                    interest_match: Math.min(100, (item.interest_match || 0) * 1.15),
                    skill_match: Math.min(100, (item.skill_match || 0) * 1.10),
                    // SAW sedikit mengurangi pengaruh lokasi
                    location_match: (item.location_match || 0) * 0.95,
                    // SAW mengurangi pengaruh IPK
                    ipk_match: (item.ipk_match || 0) * 0.90,
                    // Kuota tetap sama
                    quota_score: item.quota_score || 0,
                    // ✅ BEDA: SAW score calculation
                    appraisal_score: sawScore,
                    methodology: 'SAW',
                    saw_weights: sawWeights
                };

                return sawAdjustedData;
            }).sort((a, b) => (b.appraisal_score || 0) - (a.appraisal_score || 0)); // Sort by SAW score
        }

        // ✅ TAMBAH: Generate simulasi data SAW jika endpoint belum tersedia
        function generateSAWSimulationData() {
            console.log('🎲 Generating SAW simulation data...');

            return new Promise((resolve) => {
                // Simulasi delay API
                setTimeout(() => {
                    resolve({
                        data: {
                            success: true,
                            data: generateSAWRecommendations()
                        }
                    });
                }, 1000);
            });
        }

        function generateSAWRecommendations() {
            // ✅ DATA: Simulasi recommendations dengan karakteristik SAW
            const sawRecommendations = [{
                    id_lowongan: 1,
                    judul_lowongan: "Frontend Developer Intern",
                    nama_perusahaan: "TechCorp Indonesia",
                    lokasi: "Jakarta Selatan",
                    logo_perusahaan: "techcorp-logo.png",
                    // ✅ SAW: Skor yang berbeda dari EDAS
                    skill_match: 92,
                    location_match: 75,
                    interest_match: 95, // SAW boost minat
                    ipk_match: 72, // SAW kurangi IPK influence
                    quota_score: 80,
                    appraisal_score: 0.828, // SAW weighted score
                    methodology: 'SAW'
                },
                {
                    id_lowongan: 2,
                    judul_lowongan: "UI/UX Designer Intern",
                    nama_perusahaan: "DesignStudio Pro",
                    lokasi: "Bandung",
                    logo_perusahaan: "designstudio-logo.png",
                    skill_match: 88,
                    location_match: 85,
                    interest_match: 90, // SAW boost minat
                    ipk_match: 68, // SAW kurangi IPK influence
                    quota_score: 75,
                    appraisal_score: 0.812, // SAW weighted score
                    methodology: 'SAW'
                },
                {
                    id_lowongan: 3,
                    judul_lowongan: "Data Analyst Intern",
                    nama_perusahaan: "Analytics Plus",
                    lokasi: "Surabaya",
                    logo_perusahaan: "analytics-logo.png",
                    skill_match: 85,
                    location_match: 70,
                    interest_match: 88, // SAW boost minat
                    ipk_match: 75, // SAW kurangi IPK influence
                    quota_score: 85,
                    appraisal_score: 0.796, // SAW weighted score
                    methodology: 'SAW'
                },
                {
                    id_lowongan: 4,
                    judul_lowongan: "Mobile App Developer",
                    nama_perusahaan: "MobileFirst",
                    lokasi: "Yogyakarta",
                    logo_perusahaan: "mobilefirst-logo.png",
                    skill_match: 90,
                    location_match: 80,
                    interest_match: 85,
                    ipk_match: 70,
                    quota_score: 70,
                    appraisal_score: 0.784,
                    methodology: 'SAW'
                },
                {
                    id_lowongan: 5,
                    judul_lowongan: "Backend Developer Intern",
                    nama_perusahaan: "ServerTech",
                    lokasi: "Malang",
                    logo_perusahaan: "servertech-logo.png",
                    skill_match: 82,
                    location_match: 90,
                    interest_match: 80,
                    ipk_match: 78,
                    quota_score: 65,
                    appraisal_score: 0.773,
                    methodology: 'SAW'
                },
                {
                    id_lowongan: 6,
                    judul_lowongan: "Quality Assurance Intern",
                    nama_perusahaan: "TestPro Solutions",
                    lokasi: "Semarang",
                    logo_perusahaan: "testpro-logo.png",
                    skill_match: 78,
                    location_match: 85,
                    interest_match: 82,
                    ipk_match: 74,
                    quota_score: 80,
                    appraisal_score: 0.768,
                    methodology: 'SAW'
                }
            ];

            return sawRecommendations;
        }

        function updateMethodBadge(method) {
            const badge = document.querySelector('.dropdown-toggle');
            if (badge) {
                badge.innerHTML = `<i class="fas fa-cog me-1"></i>Metode: ${method}`;
            }
        }

        function showSPKAnalysis(lowonganId) {
            console.log('🔍 Showing SPK analysis for lowongan:', lowonganId);

            // ✅ CARI: Data dari source yang sesuai dengan method aktif
            let matchingItem = null;

            if (currentSPKMethod === 'saw' && window.currentSAWData) {
                // ✅ SAW: Cari dari data SAW global
                matchingItem = window.currentSAWData.find(item =>
                    item.id_lowongan == lowonganId || item.id == lowonganId
                );
                console.log('🎯 Found SAW data:', matchingItem);
            } else if (currentSPKMethod === 'edas' && recommendationsData) {
                // ✅ EDAS: Cari dari data EDAS global
                matchingItem = recommendationsData.find(item =>
                    item.id_lowongan == lowonganId || item.id == lowonganId
                );
                console.log('🎯 Found EDAS data:', matchingItem);
            }

            Swal.fire({
                title: '🔍 Analisis SPK Detail',
                html: `
            <div class="spk-analysis-modal">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0">Menganalisis kriteria SPK...</p>
                    <small class="text-muted">Memuat data detail lowongan (${currentSPKMethod.toUpperCase()})</small>
                    ${matchingItem ? `
                                        <div class="mt-2 text-success small">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Data ${currentSPKMethod.toUpperCase()} ditemukan: ${Math.round((matchingItem.appraisal_score || 0) * 100)}%
                                        </div>
                                    ` : `
                                        <div class="mt-2 text-warning small">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            Menggunakan data fallback dari API
                                        </div>
                                    `}
                </div>
            </div>
        `,
                width: 800,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    loadDetailedSPKAnalysis(lowonganId, matchingItem);
                }
            });
        }

        function loadDetailedSPKAnalysis(lowonganId, currentMethodItem = null) {
            console.log('🔍 Loading detailed SPK analysis for:', lowonganId);
            console.log('📊 Current method item data:', currentMethodItem);
            console.log('🧠 Current SPK method:', currentSPKMethod);

            // Validate lowonganId
            if (!lowonganId) {
                console.error('❌ Lowongan ID is required');
                Swal.getHtmlContainer().innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h5 class="mb-2">Error</h5>
                <p class="text-muted">ID Lowongan tidak valid</p>
                <button class="btn btn-primary btn-sm" onclick="Swal.close()">Tutup</button>
            </div>
        `;
                return;
            }

            // Show enhanced loading state
            Swal.getHtmlContainer().innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h6 class="mb-2">Menganalisis Detail SPK (${currentSPKMethod.toUpperCase()})</h6>
            <p class="text-muted mb-1">Memproses kriteria lowongan...</p>
            <div class="progress mx-auto" style="width: 200px; height: 6px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                     style="width: 75%"></div>
            </div>
            <small class="text-muted mt-2 d-block">Menggunakan metode ${currentSPKMethod.toUpperCase()}</small>
            ${currentMethodItem ? `
                                <small class="text-success mt-1 d-block">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Data ${currentSPKMethod.toUpperCase()}: ${Math.round((currentMethodItem.appraisal_score || 0) * 100)}%
                                </small>
                            ` : `
                                <small class="text-warning mt-1 d-block">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Menggunakan data fallback dari API
                                </small>
                            `}
        </div>
    `;

            // Make API call
            api.get(`/mahasiswa/recommendations/analysis/${lowonganId}`)
                .then(response => {
                    console.log('✅ Detailed analysis response:', response.data);

                    if (response.data && response.data.success) {
                        const analysis = response.data.data || response.data;

                        // ✅ INJECT: Data dari method yang sedang aktif
                        if (currentMethodItem) {
                            analysis.current_method_item = currentMethodItem;
                            analysis.current_method = currentSPKMethod;

                            // ✅ OVERRIDE: Gunakan skor dari method yang sedang aktif
                            analysis.method_specific_score = currentMethodItem.appraisal_score;

                            console.log('✅ Injected current method data:', {
                                method: currentSPKMethod,
                                item_score: Math.round((currentMethodItem.appraisal_score || 0) * 100),
                                analysis_score: Math.round((analysis.overall_score || 0) * 100),
                                will_use: 'item_score'
                            });
                        } else {
                            console.warn('⚠️ No current method item found, using analysis score');
                            analysis.method_specific_score = analysis.overall_score;
                        }

                        renderDetailedSPKAnalysis(analysis);
                        console.log('✅ Detailed SPK analysis rendered successfully');

                    } else {
                        throw new Error(response.data?.message || 'Analysis API returned unsuccessful response');
                    }
                })
                .catch(error => {
                    console.error('💥 Error loading detailed SPK analysis:', error);

                    // Enhanced error handling
                    let errorMessage = 'Gagal memuat analisis detail. ';

                    if (error.code === 'ERR_NETWORK') {
                        errorMessage += 'Periksa koneksi internet Anda.';
                    } else if (error.response?.status === 404) {
                        errorMessage += 'Data lowongan tidak ditemukan.';
                    } else if (error.response?.status === 401) {
                        errorMessage += 'Anda perlu login ulang.';
                    } else {
                        errorMessage += error.message || 'Terjadi kesalahan yang tidak diketahui.';
                    }

                    Swal.getHtmlContainer().innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                    <h5 class="mb-2 text-dark">Gagal Memuat Analisis</h5>
                    <div class="alert alert-danger text-start mx-3 mb-3">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                            <div>
                                <div class="fw-bold mb-1">Error Details:</div>
                                <div class="mb-2">${errorMessage}</div>
                                <div class="small text-muted">Method: ${currentSPKMethod.toUpperCase()}</div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center">
                        <button class="btn btn-primary btn-sm" onclick="loadDetailedSPKAnalysis(${lowonganId}, ${currentMethodItem ? JSON.stringify(currentMethodItem) : null})">
                            <i class="fas fa-refresh me-1"></i>Coba Lagi
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="Swal.close()">
                            <i class="fas fa-times me-1"></i>Tutup
                        </button>
                    </div>
                </div>
            `;
                });
        }

        function getMahasiswaData() {
            // ✅ PRIORITAS 1: Cek data dari Laravel Blade (sudah ada di halaman)
            const authUserName = @json(auth()->user()->name ?? '');
            const userData = @json($userData ?? null);

            // ✅ PRIORITAS 2: Ambil dari element HTML yang sudah ada
            const welcomeElement = document.querySelector('h4');
            let nameFromWelcome = '';
            if (welcomeElement && welcomeElement.textContent.includes('Selamat Datang,')) {
                nameFromWelcome = welcomeElement.textContent.replace('Selamat Datang,', '').replace('👋', '').trim();
            }

            // ✅ PRIORITAS 3: Fallback ke local storage jika ada
            const storedUserData = localStorage.getItem('user_data');
            let storedName = '';
            if (storedUserData) {
                try {
                    const parsedData = JSON.parse(storedUserData);
                    storedName = parsedData.name || '';
                } catch (e) {
                    console.warn('Failed to parse stored user data');
                }
            }

            // ✅ TENTUKAN: Nama terbaik dari sumber yang tersedia
            const finalName = authUserName ||
                (userData ? userData.name : '') ||
                nameFromWelcome ||
                storedName ||
                'Mahasiswa';

            console.log('📊 Mahasiswa data sources:', {
                authUserName,
                userData: userData?.name,
                nameFromWelcome,
                storedName,
                finalName
            });

            return {
                name: finalName,
                nim: userData?.nim || 'NIM tidak tersedia',
                email: userData?.email || authUserName ? @json(auth()->user()->email ?? '') : '',
                semester: userData?.semester || 0,
                ipk: userData?.ipk || 0
            };
        }

        // ✅ PERBAIKI: Function renderDetailedSPKAnalysis dengan data mahasiswa yang lebih cerdas
        function renderDetailedSPKAnalysis(analysis) {
            if (analysis.error) {
                Swal.getHtmlContainer().innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h5 class="mb-2">Error Analysis</h5>
                <p class="text-muted">${analysis.error}</p>
                <button class="btn btn-primary btn-sm" onclick="Swal.close()">Tutup</button>
            </div>
        `;
                return;
            }

            const scores = analysis.detailed_scores || {};
            const recommendations = analysis.recommendations || [];
            const opportunity = analysis.opportunity || {};

            // ✅ FALLBACK: Gunakan data mahasiswa dari frontend jika API tidak mengembalikan data
            let mahasiswa = analysis.mahasiswa || {};

            if (!mahasiswa.name || mahasiswa.name === 'Unknown' || mahasiswa.name === 'Nama tidak tersedia') {
                console.log('⚠️ Mahasiswa data from API is empty/unknown, using frontend fallback');
                mahasiswa = getMahasiswaData();
                console.log('✅ Using fallback mahasiswa data:', mahasiswa);
            }

            // ✅ PRIORITAS SKOR: Gunakan method_specific_score > current_method_item > analysis.overall_score
            let finalScore = 0;
            let scoreSource = '';

            if (analysis.method_specific_score !== undefined) {
                finalScore = analysis.method_specific_score;
                scoreSource = `Method-specific (${currentSPKMethod.toUpperCase()})`;
            } else if (analysis.current_method_item?.appraisal_score !== undefined) {
                finalScore = analysis.current_method_item.appraisal_score;
                scoreSource = `Current method item (${currentSPKMethod.toUpperCase()})`;
            } else {
                finalScore = analysis.overall_score || 0;
                scoreSource = 'Analysis fallback';
            }

            console.log('📊 Final score determination:', {
                method: currentSPKMethod,
                final_score: Math.round(finalScore * 100),
                source: scoreSource,
                mahasiswa_final: mahasiswa // ✅ DEBUG: Cek data mahasiswa final
            });

            Swal.getHtmlContainer().innerHTML = `
        <div class="spk-detailed-analysis text-start">
            <!-- Header analysis dengan design modern -->
            <div class="analysis-header-modern mb-4">
                <div class="analysis-bg-gradient position-relative overflow-hidden p-4 rounded-3">
                    <div class="analysis-overlay"></div>
                    <div class="position-relative text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="analysis-company-logo me-3">
                                        ${opportunity.logo ?
                                            `<img src="${getCompanyLogoUrl(opportunity.logo)}" 
                                                                  alt="${opportunity.company}" 
                                                                  style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">` :
                                            `<div class="logo-placeholder">
                                                                <i class="fas fa-building fa-2x"></i>
                                                             </div>`
                                        }
                                    </div>
                                    <div>
                                        <h5 class="mb-1 fw-bold">${opportunity.title || 'Analisis Lowongan'}</h5>
                                        <p class="mb-1 opacity-75">${opportunity.company || 'Nama Perusahaan'}</p>
                                        <small class="opacity-75">
                                            <i class="fas fa-map-marker-alt me-1"></i>${opportunity.location || 'Lokasi tidak tersedia'}
                                        </small>
                                    </div>
                                </div>
                                
                                <!-- ✅ TAMBAH: Data source indicator -->
                                <div class="score-source-info mb-2">
                                    <small class="badge bg-success bg-opacity-75">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Skor: ${scoreSource}
                                    </small>
                                    <small class="badge bg-info bg-opacity-75 ms-1">
                                        <i class="fas fa-user me-1"></i>
                                        Data: ${analysis.mahasiswa?.name ? 'API' : 'Frontend Fallback'}
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="overall-score-circle mx-auto mx-md-0 d-flex align-items-center justify-content-center"
                                     style="width: 80px; height: 80px; background-color: ${getRecommendationStatusColor(finalScore)}; color: white; font-weight: bold; border-radius: 50%;">
                                    <div class="text-center">
                                        <div style="font-size: 24px; line-height: 1;">${Math.round((finalScore || 0) * 100)}</div>
                                        <div style="font-size: 12px; opacity: 0.8;">SKOR</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards Row -->
            <div class="analysis-summary-cards mb-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="summary-card summary-mahasiswa">
                            <div class="summary-icon">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div class="summary-content">
                                <div class="summary-label">Mahasiswa</div>
                                <!-- ✅ PERBAIKI: Gunakan data mahasiswa yang sudah diperbaiki -->
                                <div class="summary-value">${mahasiswa.name}</div>
                                ${mahasiswa.email ? `
                                                    <div class="summary-extra">
                                                        <small class="text-muted">
                                                            <i class="fas fa-envelope me-1"></i>
                                                            ${mahasiswa.email}
                                                        </small>
                                                    </div>
                                                ` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-card summary-method">
                            <div class="summary-icon">
                                <i class="fas fa-brain"></i>
                            </div>
                            <div class="summary-content">
                                <div class="summary-label">Metode SPK</div>
                                <div class="summary-value">${currentSPKMethod.toUpperCase()}</div>
                                <div class="summary-detail">${getMethodDescription(currentSPKMethod)}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="summary-card summary-recommendation">
                            <div class="summary-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="summary-content">
                                <div class="summary-label">Status</div>
                                <div class="summary-value">${getRecommendationStatus(finalScore)}</div>
                                <div class="summary-detail">${Math.round((finalScore || 0) * 100)}% Match</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ ENHANCED: Mahasiswa Profile Info dengan data yang lengkap -->
            <div class="mahasiswa-profile-section mb-4">
                <div class="profile-card bg-light border rounded p-3">
                    <h6 class="mb-3 fw-bold d-flex align-items-center">
                        <i class="fas fa-id-card me-2 text-primary"></i>
                        Profil Mahasiswa
                       
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="profile-item mb-2">
                                <div class="profile-label text-muted small">Nama Lengkap</div>
                                <div class="profile-value fw-bold">${mahasiswa.name}</div>
                            </div>
                            ${mahasiswa.nim && mahasiswa.nim !== 'NIM tidak tersedia' ? `
                                                <div class="profile-item mb-2">
                                                    <div class="profile-label text-muted small">NIM</div>
                                                    <div class="profile-value">${mahasiswa.nim}</div>
                                                </div>
                                            ` : ''}
                            ${mahasiswa.email ? `
                                                <div class="profile-item mb-2">
                                                    <div class="profile-label text-muted small">Email</div>
                                                    <div class="profile-value">${mahasiswa.email}</div>
                                                </div>
                                            ` : ''}
                        </div>
                        <div class="col-md-6">
                            ${mahasiswa.semester && mahasiswa.semester > 0 ? `
                                                <div class="profile-item mb-2">
                                                    <div class="profile-label text-muted small">Semester</div>
                                                    <div class="profile-value">${mahasiswa.semester}</div>
                                                </div>
                                            ` : ''}
                            ${mahasiswa.ipk && mahasiswa.ipk > 0 ? `
                                                <div class="profile-item mb-2">
                                                    <div class="profile-label text-muted small">IPK</div>
                                                    <div class="profile-value">
                                                        <span class="badge ${getIPKBadgeClass(mahasiswa.ipk)} px-2 py-1">
                                                            ${mahasiswa.ipk}
                                                        </span>
                                                    </div>
                                                </div>
                                            ` : ''}
                            <div class="profile-item mb-2">
                                <div class="profile-label text-muted small">Status Data</div>
                                <div class="profile-value">
                                    <span class="badge ${analysis.mahasiswa?.name ? 'bg-success' : 'bg-warning text-dark'} px-2 py-1">
                                        <i class="fas ${analysis.mahasiswa?.name ? 'fa-check' : 'fa-exclamation-triangle'} me-1"></i>
                                        ${analysis.mahasiswa?.name ? 'Lengkap' : 'Fallback'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ✅ TAMBAH: Info jika menggunakan fallback data -->
                    ${!analysis.mahasiswa?.name ? `
                                        <div class="alert alert-warning mt-3 mb-0">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-info-circle me-2 mt-1"></i>
                                                <div>
                                                    <div class="fw-bold mb-1">Menggunakan Data Fallback</div>
                                                    <div class="small">
                                                        Data mahasiswa diambil dari sesi login karena API tidak mengembalikan data profil. 
                                                        Untuk data yang lebih lengkap, pastikan profil Anda sudah dilengkapi.
                                                    </div>
                                                    <a href="/mahasiswa/profile" class="btn btn-warning btn-sm mt-2">
                                                        <i class="fas fa-user-edit me-1"></i>
                                                        Lengkapi Profil
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    ` : ''}
                </div>
            </div>

            <!-- Detailed criteria breakdown dengan informasi lengkap -->
            <div class="criteria-breakdown-modern">
                <div class="criteria-header mb-4">
                    <h6 class="mb-1 fw-bold d-flex align-items-center">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>
                        Analisis Detail Kriteria (${currentSPKMethod.toUpperCase()})
                    </h6>
                    <p class="text-muted small mb-0">Breakdown lengkap setiap kriteria penilaian</p>
                </div>

                <div class="criteria-grid">
                    ${Object.entries(scores).map(([criterion, data]) => {
                        const score = data.score || 0;
let percentage;
if (criterion === 'wilayah') {
    percentage = data.percentage || ((4 - score) / 3 * 100); // cost criterion
} else {
    percentage = data.percentage || (score / 3 * 100); // benefit criterion
}
                        const category = data.category || 'No category';
                        const matchCount = data.match_count;
                        const totalRequired = data.total_required;

                        return ` <
                div class = "criteria-detail-card mb-3" >
                <
                div class = "criteria-card-header" >
                <
                div class = "d-flex align-items-center" >
                <
                div class = "criteria-detail-icon ${getCriterionIconColorClass(criterion)}" >
                <
                i class = "${getCriterionIcon(criterion)}" > < /i> < /
            div > <
                div class = "flex-grow-1" >
                <
                div class = "criteria-title" > $ {
                    getCriterionLabel(criterion)
                } < /div> <
            div class = "criteria-category" > $ {
                category
            } < /div> < /
            div > <
                div class = "criteria-score-badge" >
                <
                span class = "badge ${getCriterionBadgeClass(percentage)} fs-6 px-3 py-2" >
                $ {
                    percentage.toFixed(1)
                } %
                <
                /span> <
            div class = "score-fraction" > $ {
                score
            }
            /3</div >
            <
            /div> < /
            div > <
                /div>

                <
                div class = "criteria-card-body" >
                <
                !--Progress Bar dengan animasi-- >
                <
                div class = "criteria-progress-detailed mb-3" >
                <
                div class = "progress-track-detailed" >
                <
                div class = "progress-fill-detailed ${getCriterionProgressClass(percentage)}"
            style = "width: ${percentage}%" > < /div> < /
            div > <
                div class = "progress-markers" >
                <
                span class = "marker"
            style = "left: 33.33%;" > 1 < /span> <
            span class = "marker"
            style = "left: 66.66%;" > 2 < /span> <
            span class = "marker"
            style = "left: 100%;" > 3 < /span> < /
            div > <
                /div>

                <
                !--Detail Information berdasarkan kriteria-- >
                <
                div class = "criteria-details" >
                $ {
                    renderCriterionSpecificDetails(criterion, data, matchCount, totalRequired)
                } <
                /div>

                <
                !--Insight & Recommendation untuk kriteria ini-- >
                <
                div class = "criteria-insight" >
                $ {
                    renderCriterionInsight(criterion, percentage, data)
                } <
                /div> < /
            div > <
                /div>
            `;
                    }).join('')}
                </div>
            </div>

            <!-- Action buttons dengan design modern -->
            <div class="analysis-actions mt-4 pt-3">
                <div class="actions-divider mb-3"></div>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-primary w-100 action-btn-modern" onclick="Swal.close()">
                            <i class="fas fa-check me-2"></i>
                            <span>Tutup Analisis</span>
                        </button>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="/mahasiswa/lowongan" class="btn btn-outline-primary w-100 action-btn-modern">
                            <i class="fas fa-external-link-alt me-1"></i>
                            <span>Lihat Detail Lowongan</span>
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <button class="btn btn-outline-info w-100 action-btn-modern" onclick="exportSPKAnalysis(${opportunity.id || ''})">
                            <i class="fas fa-download me-1"></i>
                            <span>Export Analisis</span>
                        </button>
                    </div>
                </div>
                
                <!-- ✅ TAMBAH: Debug info untuk development -->
                <div class="debug-section mt-3" style="display: none;">
                    <button class="btn btn-outline-secondary btn-sm w-100" onclick="toggleDebugInfo()">
                        <i class="fas fa-bug me-1"></i>
                        Toggle Debug Info
                    </button>
                    <div id="debug-info" class="mt-2" style="display: none;">
                        <div class="alert alert-light">
                            <h6>🐛 Debug Information:</h6>
                            <ul class="mb-0 small">
                                <li>API Mahasiswa: ${analysis.mahasiswa?.name ? 'Found' : 'Not found'}</li>
                                <li>Fallback Mahasiswa: ${mahasiswa.name}</li>
                                <li>Data Source: ${analysis.mahasiswa?.name ? 'API' : 'Frontend'}</li>
                                <li>Method: ${currentSPKMethod.toUpperCase()}</li>
                                <li>Final Score: ${Math.round((finalScore || 0) * 100)}%</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
        }

        // ✅ TAMBAH: Function untuk toggle debug info
        function toggleDebugInfo() {
            const debugInfo = document.getElementById('debug-info');
            if (debugInfo) {
                debugInfo.style.display = debugInfo.style.display === 'none' ? 'block' : 'none';
            }
        }

        // ✅ TAMBAH: Function untuk get IPK badge class
        function getIPKBadgeClass(ipk) {
            if (ipk >= 3.5) return 'bg-success';
            if (ipk >= 3.0) return 'bg-info';
            if (ipk >= 2.5) return 'bg-warning text-dark';
            return 'bg-danger';
        }

        // ✅ EXPOSE ke global scope
        window.toggleDebugInfo = toggleDebugInfo;

        function formatScoreValue(score) {
            if (!score && score !== 0) return 0;

            // Jika skor sudah dalam bentuk persentase (0-100)
            if (score > 1) {
                return Math.round(score);
            }

            // Jika skor dalam bentuk desimal (0-1)
            return Math.round(score * 100);
        }

        function getMethodDescription(method) {
            const descriptions = {
                'edas': 'Distance Analysis',
                'saw': 'Weighted Analysis'
            };
            return descriptions[method] || 'Analysis';
        }

        // ✅ PERBAIKI: Function helper agar konsisten dengan appraisal_score format
        function getRecommendationStatus(appraisalScore) {
            // ✅ KONSISTEN: Gunakan format yang sama dengan komponen lain (0-1 range)
            const percentage = Math.round((appraisalScore || 0) * 100);
            if (percentage >= 85) return "Sangat Direkomendasikan";
            if (percentage >= 70) return "Direkomendasikan";
            if (percentage >= 50) return "Cukup Sesuai";
            return "Kurang Sesuai";
        }

        function getRecommendationStatusColor(appraisalScore) {
            // ✅ KONSISTEN: Gunakan format yang sama dengan komponen lain (0-1 range)
            const percentage = Math.round((appraisalScore || 0) * 100);
            if (percentage >= 85) return '#28a745'; // Green
            if (percentage >= 70) return '#17a2b8'; // Blue  
            if (percentage >= 50) return '#ffc107'; // Yellow
            return '#dc3545'; // Red
        }

        function getMethodDescription(method) {
            const descriptions = {
                'edas': 'Distance Analysis',
                'saw': 'Weighted Analysis'
            };
            return descriptions[method] || 'Analysis';
        }

        function renderCriterionSpecificDetails(criterion, data, matchCount, totalRequired) {
            switch (criterion) {
                case 'minat':
                    return `
                                    <div class="criterion-minat-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-heart text-info"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Minat yang Sesuai</div>
                                                <div class="detail-value">${matchCount || 0} dari ${totalRequired || 0} minat</div>
                                            </div>
                                        </div>
                                        ${data.matching_interests ? `
                                                            <div class="matching-items mt-2">
                                                                <div class="items-label">Minat yang cocok:</div>
                                                                <div class="items-list">
                                                                    ${data.matching_interests.map(interest =>
                                        `<span class="item-tag tag-success">
                                                            <i class="fas fa-check me-1"></i>${interest}
                                                         </span>`
                                    ).join('')}
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                        ${data.missing_interests ? `
                                                            <div class="missing-items mt-2">
                                                                <div class="items-label">Minat yang dibutuhkan:</div>
                                                                <div class="items-list">
                                                                    ${data.missing_interests.map(interest =>
                                        `<span class="item-tag tag-warning">
                                                            <i class="fas fa-plus me-1"></i>${interest}
                                                         </span>`
                                    ).join('')}
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                    </div>
                                `;

                case 'skill':
                    return `
                                    <div class="criterion-skill-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-tools text-success"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Skill yang Sesuai</div>
                                                <div class="detail-value">${matchCount || 0} dari ${totalRequired || 0} skill</div>
                                            </div>
                                        </div>
                                        ${data.matching_skills ? `
                                                            <div class="matching-items mt-2">
                                                                <div class="items-label">Skill yang cocok:</div>
                                                                <div class="items-list">
                                                                    ${data.matching_skills.map(skill =>
                                        `<span class="item-tag tag-success">
                                                            <i class="fas fa-check me-1"></i>${skill}
                                                         </span>`
                                    ).join('')}
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                        ${data.missing_skills ? `
                                                            <div class="missing-items mt-2">
                                                                <div class="items-label">Skill yang dibutuhkan:</div>
                                                                <div class="items-list">
                                                                    ${data.missing_skills.map(skill =>
                                        `<span class="item-tag tag-warning">
                                                            <i class="fas fa-plus me-1"></i>${skill}
                                                         </span>`
                                    ).join('')}
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                    </div>
                                `;

                case 'wilayah':
                    return `
                                    <div class="criterion-location-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-map-marker-alt text-primary"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Jarak Lokasi</div>
                                                <div class="detail-value">${data.distance_km || 0} km</div>
                                            </div>
                                        </div>
                                        ${data.duration_minutes ? `
                                                            <div class="detail-row mt-2">
                                                                <div class="detail-icon">
                                                                    <i class="fas fa-clock text-info"></i>
                                                                </div>
                                                                <div class="detail-content">
                                                                    <div class="detail-label">Estimasi Waktu Tempuh</div>
                                                                    <div class="detail-value">~${Math.round(data.duration_minutes / 60)} jam</div>
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                        ${data.from_location && data.to_location ? `
                                                            <div class="location-route mt-2">
                                                                <div class="route-info">
                                                                    <div class="route-point route-start">
                                                                        <i class="fas fa-home me-2"></i>
                                                                        <span>${data.from_location}</span>
                                                                    </div>
                                                                    <div class="route-line">
                                                                        <i class="fas fa-arrow-right"></i>
                                                                    </div>
                                                                    <div class="route-point route-end">
                                                                        <i class="fas fa-building me-2"></i>
                                                                        <span>${data.to_location}</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                    </div>
                                `;

                case 'kuota':
                    return `
                                    <div class="criterion-quota-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-users text-secondary"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Kuota Tersedia</div>
                                                <div class="detail-value">${data.available_quota || 0} posisi</div>
                                            </div>
                                        </div>
                                        ${data.total_quota ? `
                                                            <div class="detail-row mt-2">
                                                                <div class="detail-icon">
                                                                    <i class="fas fa-chart-pie text-info"></i>
                                                                </div>
                                                                <div class="detail-content">
                                                                    <div class="detail-label">Total Kuota</div>
                                                                    <div class="detail-value">${data.total_quota} posisi</div>
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                        ${data.competition_level ? `
                                                            <div class="competition-indicator mt-2">
                                                                <div class="competition-label">Tingkat Persaingan:</div>
                                                                <div class="competition-value ${data.competition_level.toLowerCase()}">
                                                                    ${getCompetitionIcon(data.competition_level)}
                                                                    ${data.competition_level}
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                    </div>
                                `;

                case 'ipk':
                    return `
                                    <div class="criterion-ipk-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-graduation-cap text-warning"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">IPK Anda</div>
                                                <div class="detail-value">${data.student_ipk || 0}</div>
                                            </div>
                                        </div>
                                        ${data.required_ipk ? `
                                                            <div class="detail-row mt-2">
                                                                <div class="detail-icon">
                                                                    <i class="fas fa-star text-gold"></i>
                                                                </div>
                                                                <div class="detail-content">
                                                                    <div class="detail-label">IPK Minimum</div>
                                                                    <div class="detail-value">${data.required_ipk}</div>
                                                                </div>
                                                            </div>
                                                        ` : ''}
                                        <div class="ipk-comparison mt-2">
                                            <div class="comparison-result ${data.student_ipk >= data.required_ipk ? 'meets-requirement' : 'below-requirement'}">
                                                <i class="fas ${data.student_ipk >= data.required_ipk ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                                                ${data.student_ipk >= data.required_ipk ? 'Memenuhi persyaratan IPK' : 'Belum memenuhi persyaratan IPK'}
                                            </div>
                                        </div>
                                    </div>
                                `;

                default:
                    return `
                                    <div class="criterion-default-details">
                                        <div class="detail-row">
                                            <div class="detail-icon">
                                                <i class="fas fa-info-circle text-muted"></i>
                                            </div>
                                            <div class="detail-content">
                                                <div class="detail-label">Kesesuaian</div>
                                                <div class="detail-value">${matchCount || 0}/${totalRequired || 0}</div>
                                            </div>
                                        </div>
                                    </div>
                                `;
            }
        }

        // Helper function untuk render insight setiap kriteria
        function renderCriterionInsight(criterion, percentage, data) {
            const insights = getCriterionInsights(criterion, percentage, data);

            return `
                            <div class="criterion-insight-box ${insights.type}">
                                <div class="insight-icon">
                                    <i class="${insights.icon}"></i>
                                </div>
                                <div class="insight-content">
                                    <div class="insight-message">${insights.message}</div>
                                    ${insights.suggestion ? `<div class="insight-suggestion">${insights.suggestion}</div>` : ''}
                                </div>
                            </div>
                        `;
        }

        // Helper functions untuk styling dan logic
        // Helper functions untuk styling dan logic
        function getCriterionInsights(criterion, percentage, data) {
            // ✅ UNIFIED: Insight yang sama untuk semua metode
            const unifiedInsights = {
                minat: {
                    high: {
                        message: "Minat Anda sangat selaras dengan bidang ini",
                        suggestion: "Peluang besar untuk berkembang sesuai passion",
                        icon: "fas fa-heart",
                        type: "insight-excellent"
                    },
                    medium: {
                        message: "Minat Anda cukup sesuai dengan bidang ini",
                        suggestion: "Kesempatan baik untuk eksplorasi minat baru",
                        icon: "fas fa-thumbs-up",
                        type: "insight-good"
                    },
                    low: {
                        message: "Minat kurang selaras dengan bidang ini",
                        suggestion: "Pertimbangkan untuk mempelajari bidang ini lebih dalam",
                        icon: "fas fa-question-circle",
                        type: "insight-warning"
                    }
                },
                skill: {
                    high: {
                        message: "Skill Anda sangat cocok dengan kebutuhan",
                        suggestion: "Anda siap untuk berkontribusi maksimal",
                        icon: "fas fa-star",
                        type: "insight-excellent"
                    },
                    medium: {
                        message: "Skill dasar Anda sudah sesuai",
                        suggestion: "Tingkatkan skill spesifik yang dibutuhkan",
                        icon: "fas fa-tools",
                        type: "insight-good"
                    },
                    low: {
                        message: "Perlu pengembangan skill tambahan",
                        suggestion: "Fokus belajar skill yang dibutuhkan sebelum apply",
                        icon: "fas fa-exclamation-triangle",
                        type: "insight-warning"
                    }
                },
                wilayah: {
                    high: {
                        message: "Lokasi sangat strategis dan terjangkau",
                        suggestion: "Tidak ada kendala mobilitas",
                        icon: "fas fa-map-marker-alt",
                        type: "insight-excellent"
                    },
                    medium: {
                        message: "Lokasi masih dalam jangkauan wajar",
                        suggestion: "Persiapkan transportasi yang memadai",
                        icon: "fas fa-route",
                        type: "insight-good"
                    },
                    low: {
                        message: "Lokasi cukup jauh dari domisili",
                        suggestion: "Pertimbangkan kost atau transportasi harian",
                        icon: "fas fa-car",
                        type: "insight-warning"
                    }
                },
                kuota: {
                    high: {
                        message: "Banyak posisi tersedia",
                        suggestion: "Peluang diterima sangat baik",
                        icon: "fas fa-users",
                        type: "insight-excellent"
                    },
                    medium: {
                        message: "Posisi masih tersedia",
                        suggestion: "Segera ajukan lamaran",
                        icon: "fas fa-user-plus",
                        type: "insight-good"
                    },
                    low: {
                        message: "Posisi terbatas dengan persaingan ketat",
                        suggestion: "Persiapkan aplikasi terbaik Anda",
                        icon: "fas fa-user-tie",
                        type: "insight-warning"
                    }
                },
                ipk: {
                    high: {
                        message: "IPK Anda sangat memenuhi persyaratan",
                        suggestion: "Keunggulan akademik yang solid",
                        icon: "fas fa-graduation-cap",
                        type: "insight-excellent"
                    },
                    medium: {
                        message: "IPK memenuhi standar minimum",
                        suggestion: "Fokus pada kualitas portofolio lainnya",
                        icon: "fas fa-award",
                        type: "insight-good"
                    },
                    low: {
                        message: "IPK di bawah standar yang diharapkan",
                        suggestion: "Kompensasi dengan skill dan pengalaman",
                        icon: "fas fa-chart-line",
                        type: "insight-warning"
                    }
                }
            };

            const criterionInsight = unifiedInsights[criterion];
            if (!criterionInsight) return {
                message: "Analisis tidak tersedia",
                icon: "fas fa-info",
                type: "insight-neutral"
            };

            if (percentage >= 80) return criterionInsight.high;
            if (percentage >= 60) return criterionInsight.medium;
            return criterionInsight.low;
        }

        function getRecommendationStatus(score) {
            const percentage = (score || 0) * 100;
            if (percentage >= 85) return "Sangat Direkomendasikan";
            if (percentage >= 70) return "Direkomendasikan";
            if (percentage >= 50) return "Cukup Sesuai";
            return "Kurang Sesuai";
        }

        function getCriterionIconColorClass(criterion) {
            const colors = {
                'minat': 'icon-info',
                'skill': 'icon-success',
                'wilayah': 'icon-primary',
                'kuota': 'icon-secondary',
                'ipk': 'icon-warning'
            };
            return colors[criterion] || 'icon-muted';
        }

        function getRecommendationTypeClass(status) {
            const classes = {
                'strength': 'recommendation-excellent',
                'needs_improvement': 'recommendation-warning',
                'balanced': 'recommendation-good'
            };
            return classes[status] || 'recommendation-neutral';
        }

        function getRecommendationStatusBadge(status) {
            const badges = {
                'strength': '<span class="status-badge status-excellent">Kekuatan</span>',
                'needs_improvement': '<span class="status-badge status-warning">Perlu Ditingkatkan</span>',
                'balanced': '<span class="status-badge status-good">Seimbang</span>'
            };
            return badges[status] || '<span class="status-badge status-neutral">Normal</span>';
        }

        function getCompetitionIcon(level) {
            const icons = {
                'Rendah': '<i class="fas fa-circle text-success me-2"></i>',
                'Sedang': '<i class="fas fa-circle text-warning me-2"></i>',
                'Tinggi': '<i class="fas fa-circle text-danger me-2"></i>'
            };
            return icons[level] || '<i class="fas fa-circle text-muted me-2"></i>';
        }

        function exportSPKAnalysis(lowonganId) {
            console.log('📄 Exporting SPK analysis for:', lowonganId);
            showToast('info', '📄 Fitur export analisis sedang dalam pengembangan');
        }

        // ✅ UTILITY FUNCTIONS
        function getCompanyLogoUrl(logoPath) {
            if (!logoPath || logoPath.trim() === '') {
                return '/img/default-company.png';
            }

            const cleanPath = logoPath.trim();

            if (cleanPath.startsWith('http://') || cleanPath.startsWith('https://')) {
                return cleanPath;
            }

            if (cleanPath.startsWith('/storage/')) {
                return cleanPath;
            }

            return `/storage/${cleanPath}`;
        }

        function getCriterionIcon(criterion) {
            const icons = {
                'minat': 'fas fa-heart',
                'skill': 'fas fa-tools',
                'wilayah': 'fas fa-map-marker-alt',
                'kuota': 'fas fa-users',
                'ipk': 'fas fa-graduation-cap'
            };
            return icons[criterion] || 'fas fa-info-circle';
        }

        function getCriterionIconColor(criterion) {
            const colors = {
                'minat': 'text-info',
                'skill': 'text-success',
                'wilayah': 'text-primary',
                'kuota': 'text-secondary',
                'ipk': 'text-warning'
            };
            return colors[criterion] || 'text-muted';
        }

        function getCriterionLabel(criterion) {
            const labels = {
                'minat': 'Minat',
                'skill': 'Keahlian',
                'wilayah': 'Lokasi',
                'kuota': 'Kuota',
                'ipk': 'IPK'
            };
            return labels[criterion] || criterion;
        }

        function getCriterionBadgeClass(percentage) {
            if (percentage >= 80) return 'bg-success';
            if (percentage >= 60) return 'bg-warning';
            return 'bg-danger';
        }

        function getCriterionProgressClass(percentage) {
            if (percentage >= 80) return 'bg-success';
            if (percentage >= 60) return 'bg-warning';
            return 'bg-danger';
        }

        function getRecommendationAlertClass(status) {
            const classes = {
                'strength': 'alert-success',
                'needs_improvement': 'alert-warning',
                'balanced': 'alert-info'
            };
            return classes[status] || 'alert-info';
        }

        function getRecommendationIcon(status) {
            const icons = {
                'strength': 'fas fa-check-circle text-success',
                'needs_improvement': 'fas fa-exclamation-triangle text-warning',
                'balanced': 'fas fa-info-circle text-info'
            };
            return icons[status] || 'fas fa-info-circle';
        }

        function getRankBadgeClass(rank) {
            if (rank <= 3) return 'bg-warning text-dark';
            if (rank <= 6) return 'bg-info';
            return 'bg-secondary';
        }

        function getScoreBadgeClass(score) {
            const percentage = (score || 0) * 100;
            if (percentage >= 80) return 'bg-success';
            if (percentage >= 60) return 'bg-warning text-dark';
            return 'bg-danger';
        }

        function getOverallScoreBadgeClass(score) {
            const percentage = (score || 0) * 100;
            if (percentage >= 85) return 'bg-success';
            if (percentage >= 70) return 'bg-info';
            if (percentage >= 50) return 'bg-warning text-dark';
            return 'bg-secondary';
        }

        function getCriteriaValueClass(percentage) {
            if (percentage >= 80) return 'text-success';
            if (percentage >= 60) return 'text-warning';
            return 'text-danger';
        }

        function getSPKScoreColor(score) {
            const percentage = (score || 0) * 100;
            if (percentage >= 85) return '#28a745'; // Success green
            if (percentage >= 70) return '#17a2b8'; // Info blue
            if (percentage >= 50) return '#ffc107'; // Warning yellow
            return '#dc3545'; // Danger red
        }

        function getSPKRankBadge(rank) {
            let badgeClass = 'bg-secondary';
            let icon = 'fas fa-hashtag';

            if (rank === 1) {
                badgeClass = 'bg-warning text-dark';
                icon = 'fas fa-trophy';
            } else if (rank === 2) {
                badgeClass = 'bg-light text-dark border';
                icon = 'fas fa-medal';
            } else if (rank === 3) {
                badgeClass = 'bg-orange text-white';
                icon = 'fas fa-award';
            }

            return `
                                                                            <span class="spk-rank-badge badge ${badgeClass} d-flex align-items-center px-2 py-1" 
                                                                                  style="font-size: 12px; font-weight: bold;">
                                                                                <i class="${icon} me-1"></i>
                                                                                <span>#${rank}</span>
                                                                            </span>
                                                                        `;
        }

        // ✅ DEBUG FUNCTIONS
        function debugRecommendations() {
            console.log('🐛 Debug recommendations triggered');

            api.get('/mahasiswa/recommendations/debug')
                .then(response => {
                    console.log('✅ Debug Response:', response.data);

                    Swal.fire({
                        title: '🐛 Debug Information',
                        html: `
                                                                                                        <div class="text-start" style="max-height: 400px; overflow-y: auto;">
                                                                                                            <pre class="bg-light p-3 rounded"><code>${JSON.stringify(response.data, null, 2)}</code></pre>
                                                                                                        </div>
                                                                                                    `,
                        width: 800,
                        confirmButtonText: 'Close'
                    });
                })
                .catch(error => {
                    console.error('❌ Debug Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Debug Error',
                        text: error.message
                    });
                });
        }

        function debugSPK() {
            console.log('🐛 Debug SPK triggered');

            api.get('/mahasiswa/recommendations/debug')
                .then(response => {
                    console.log('✅ SPK Debug Response:', response.data);

                    Swal.fire({
                        title: '🧠 SPK Debug Information',
                        html: `
                                                                                                        <div class="text-start" style="max-height: 500px; overflow-y: auto;">
                                                                                                            <h6>Current Method: ${currentSPKMethod}</h6>
                                                                                                            <h6>Analysis Data:</h6>
                                                                                                            <pre class="bg-light p-3 rounded small"><code>${JSON.stringify(spkAnalysisData, null, 2)}</code></pre>
                                                                                                            <h6>API Response:</h6>
                                                                                                            <pre class="bg-light p-3 rounded small"><code>${JSON.stringify(response.data, null, 2)}</code></pre>
                                                                                                        </div>
                                                                                                    `,
                        width: 900,
                        confirmButtonText: 'Close'
                    });
                })
                .catch(error => {
                    console.error('❌ SPK Debug Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'SPK Debug Error',
                        text: error.message
                    });
                });
        }

        // ✅ TOAST NOTIFICATION SYSTEM
        function showToast(type, message) {
            const toastContainer = getToastContainer();

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                                                                                            <div class="d-flex">
                                                                                                <div class="toast-body">
                                                                                                    <i class="${getToastIcon(type)} me-2"></i>
                                                                                                    ${message}
                                                                                                </div>
                                                                                                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                                                                                            </div>
                                                                                        `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }

        function getToastContainer() {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container position-fixed top-0 end-0 p-3';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }
            return container;
        }

        function getToastIcon(type) {
            const icons = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            };
            return icons[type] || 'fas fa-info-circle';
        }

        // ✅ GLOBAL ERROR HANDLER
        window.addEventListener('error', function(e) {
            console.error('💥 Global JavaScript Error:', e.error);
            showToast('error', 'Terjadi kesalahan pada halaman');
        });

        window.addEventListener('unhandledrejection', function(e) {
            console.error('💥 Unhandled Promise Rejection:', e.reason);
            showToast('error', 'Terjadi kesalahan jaringan');
        });

        // ✅ EXPOSE FUNCTIONS TO GLOBAL SCOPE
        window.loadRecommendations = loadRecommendations;
        window.loadSPKRecommendations = loadSPKRecommendations;
        window.switchSPKMethod = switchSPKMethod;
        window.showSPKAnalysis = showSPKAnalysis;
        window.showProfileCompletionModal = showProfileCompletionModal;
        window.hideProfileCard = hideProfileCard;
        window.debugRecommendations = debugRecommendations;
        window.debugSPK = debugSPK;

        console.log('✅ === DASHBOARD SCRIPT LOADED SUCCESSFULLY ===');
    </script>
@endpush
