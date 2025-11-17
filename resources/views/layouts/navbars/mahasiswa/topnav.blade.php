{{-- filepath: d:\laragon\www\JTIintern\resources\views\layouts\navbars\mahasiswa\topnav.blade.php --}}

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-9 ">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="/img/Jti_polinema.png" alt="Logo" style="height: 32px;">
                <span class="ms-2" style="color: #2D2D2D; font-size: 20px; font-weight: 600;">JTIintern</span>
            </a>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.dashboard') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }} fw-medium">
                        Dashboard
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.lowongan') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.lowongan') ? 'active' : '' }} fw-medium">
                        Lowongan Magang
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.lamaran') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.lamaran') ? 'active' : '' }} fw-medium">
                        Status Lamaran
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.logaktivitas') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.logaktivitas') ? 'active' : '' }} fw-medium">
                        Log Aktivitas
                    </a>
                </li>
                <li class="nav-item px-2">
                    <a href="{{ route('mahasiswa.evaluasi') }}"
                        class="nav-link {{ request()->routeIs('mahasiswa.evaluasi') ? 'active' : '' }} fw-medium">
                        Riwayat Mangang
                    </a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <!-- ✅ ENHANCED: Modern Notification Bell -->
                <div class="dropdown position-relative">
                    <button class="btn p-0 border-0 notification-bell-btn" type="button" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" id="notificationDropdown" aria-label="Notifications"
                        aria-expanded="false">
                        <div class="notification-bell-container">
                            <i class="bi bi-bell notification-bell-icon" aria-hidden="true"></i>
                            <span class="notification-badge" id="notificationCount" style="display: none;"
                                aria-label="Unread notifications">0</span>
                        </div>
                    </button>

                    <!-- ✅ MODERN: Notification Dropdown -->
                    <div class="dropdown-menu dropdown-menu-end modern-notification-dropdown"
                        aria-labelledby="notificationDropdown">
                        <!-- Header -->
                        <div class="notification-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="notification-title">Notifikasi</h6>
                                    <small class="notification-subtitle" id="notificationSubtitle">Terbaru untuk
                                        Anda</small>
                                </div>
                                <div class="notification-actions">
                                    <button class="btn-action btn-mark-all" id="markAllRead" title="Tandai semua dibaca"
                                        aria-label="Mark all notifications as read">
                                        <i class="bi bi-check2-all" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown">
                                        <button class="btn-action btn-options" data-bs-toggle="dropdown"
                                            title="Opsi lainnya" aria-label="More options" aria-expanded="false">
                                            <i class="bi bi-three-dots" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end options-menu">
                                            <li>
                                                <button class="dropdown-item option-item"
                                                    onclick="notificationSystem.clearRead()">
                                                    <i class="bi bi-check-circle text-success" aria-hidden="true"></i>
                                                    <span>Hapus yang Dibaca</span>
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item option-item"
                                                    onclick="notificationSystem.clearExpired()">
                                                    <i class="bi bi-clock-history text-warning" aria-hidden="true"></i>
                                                    <span>Hapus Kedaluwarsa</span>
                                                </button>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <button class="dropdown-item option-item text-danger"
                                                    onclick="notificationSystem.clearAll()">
                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                    <span>Hapus Semua</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <div class="notification-loading" id="notificationLoading">
                            <div class="loading-container">
                                <div class="loading-spinner" aria-hidden="true"></div>
                                <span class="loading-text">Memuat notifikasi...</span>
                            </div>
                        </div>

                        <!-- Notification List -->
                        <div class="notification-list-container" id="notificationList" role="list">
                            <!-- Will be populated by JavaScript -->
                        </div>

                        <!-- Empty State -->
                        <div class="notification-empty" id="notificationEmpty" style="display: none;">
                            <div class="empty-container">
                                <div class="empty-icon">
                                    <i class="bi bi-bell-slash" aria-hidden="true"></i>
                                </div>
                                <h6 class="empty-title">Tidak ada notifikasi</h6>
                                <p class="empty-subtitle">Semua notifikasi akan muncul di sini</p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="notification-footer">
                            <button class="btn-refresh" onclick="notificationSystem.loadNotifications()"
                                aria-label="Refresh notifications">
                                <i class="bi bi-arrow-clockwise" aria-hidden="true"></i>
                                <span>Refresh</span>
                            </button>
                            <div class="footer-divider"></div>
                            <span class="auto-refresh-text">Auto-refresh: 30s</span>
                        </div>
                    </div>
                </div>

                <!-- ✅ Profile Dropdown (existing) -->
                <div class="dropdown">
                    <button class="btn rounded-circle profile-button" type="button" data-bs-toggle="dropdown"
                        data-bs-auto-close="true" style="width: 32px; height: 32px; background: #EFF6FF;"
                        aria-label="User profile menu" aria-expanded="false">
                        <span class="me-2 fw-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('mahasiswa.profile') }}">
                                <i class="bi bi-person me-2" aria-hidden="true"></i>Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="dropdown-item p-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

@push('css')
    <link href="{{ asset('assets/css/topnav.css') }}" rel="stylesheet" />
    <!-- ✅ SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('js')
    <!-- ✅ SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/js/notifications.js') }}"></script>
@endpush
