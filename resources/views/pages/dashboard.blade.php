@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])

    <div class="container-fluid py-4">

        <!-- Active Period Card -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <div class="col-md-8 p-4">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-success me-2">Aktif</span>
                                    <h5 class="fw-bold mb-0" id="period-title">Memuat periode...</h5>
                                </div>
                                <div class="text-muted mb-3" id="period-description">Memuat informasi periode...</div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="small fw-bold text-muted mb-1">Tanggal Mulai Tahun Akademik</div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-check me-2" style="color:#5988FF;"></i>
                                            <span class="fw-bold" id="period-start-date">--/--/----</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="small fw-bold text-muted mb-1">Tanggal Akhir Tahun Akademik</div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-x me-2" style="color:#FF5252;"></i>
                                            <span class="fw-bold" id="period-end-date">--/--/----</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold">Progress Tahun Akademik</span>
                                        <span class="small text-muted" id="period-progress-text">0%</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div id="period-progress-bar" class="progress-bar bg-info" role="progressbar"
                                            style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <!-- Add days left counter -->
                                    <div class="text-end mt-2">
                                        <span class="badge bg-light text-dark" id="period-days-left">-- hari</span>
                                        tersisa dalam periode ini
                                    </div>
                                </div>
                            </div>
                            <!-- Add right panel with visual elements -->
                            <div class="col-md-4 d-none d-md-block"
                                style="background: linear-gradient(145deg, rgba(89,136,255,0.1) 0%, rgba(89,136,255,0.3) 100%);">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100 p-4">
                                    <div class="text-center mb-3">
                                        <i class="bi bi-clock-history" style="font-size: 48px; color: #5988FF;"></i>
                                    </div>
                                    <h3 class="fw-bold mb-2" id="period-days-left-large">-- hari</h3>
                                    <p class="mb-0 text-center">tersisa dalam periode ini</p>
                                    <div class="mt-3">
                                        <a href="/periode" class="btn btn-sm btn-outline-primary">Kelola Periode</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-4">
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="fw-bold mb-2" style="color: #2D2D2D;">Mahasiswa Aktif Magang</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="color: #5988FF; font-size: 48px;" id="mahasiswa-aktif">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </span>
                            <span class="d-flex align-items-center justify-content-center rounded"
                                style="width:68px;height:68px;background:rgba(182,203,255,0.4);">
                                <i class="bi bi-mortarboard-fill" style="color:#5988FF; font-size:45px;"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="fw-bold mb-2" style="color: #2D2D2D;">Perusahaan Mitra</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="color: #5988FF; font-size: 48px;" id="perusahaan-mitra">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </span>
                            <span class="d-flex align-items-center justify-content-center rounded"
                                style="width:64px;height:64px;background:#FECDCD;">
                                <i class="bi bi-building-fill" style="color:#FF5252; font-size:42px;"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="fw-bold mb-2" style="color: #2D2D2D;">Lowongan Magang Aktif</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="color: #5988FF; font-size: 48px;" id="lowongan-aktif">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </span>
                            <span class="d-flex align-items-center justify-content-center rounded"
                                style="width:64px;height:64px;background:#FFE8BE;">
                                <i class="bi bi-briefcase-fill" style="color:#F8A100; font-size:42px;"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold" style="color: #2D2D2D;">Permintaan Magang Terbaru</span>
                            <a href="/permintaan" class="fw-semibold" style="color: #4278FF;">Semua Permintaan</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Perusahaan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="latest-applications">
                                    <tr>
                                        <td colspan="3" class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100"
                    style="background: linear-gradient(158deg, rgba(187,206,255,0.58) 0%, rgba(246,230,247,0.62) 100%);">
                    <div class="card-body">
                        <div class="fw-bold mb-3" style="color: #2D2D2D;">Menu Cepat</div>
                        <!-- Menu Cepat dengan URL yang sudah diperbarui -->
                        <div class="list-group">
                            <a href="/dataMhs"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded mb-2">
                                <i class="fas fa-graduation-cap" style="color:#FFAE00;"></i>
                                <span class="fw-semibold" style="color: #2D2D2D;">Data Mahasiswa</span>
                            </a>
                            <a href="/data_perusahaan"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded mb-2">
                                <i class="fas fa-city" style="color:#2F78FF;"></i>
                                <span class="fw-semibold" style="color: #2D2D2D;">Data Perusahaan</span>
                            </a>
                            <a href="/dosen"
                                class="list-group-item list-group-item-action d-flex align-items-center gap-2 rounded mb-2">
                                <i class="fas fa-user-tie" style="color:#E091FF;"></i>
                                <span class="fw-semibold" style="color: #2D2D2D;">Data Dosen</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ SIMPLE: Automation widget untuk dashboard --}}

        @if(auth()->user()->role === 'admin')
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-magic me-2"></i>
                        Simple Automation
                    </h6>
                </div>
                <div class="card-body">
                    {{-- Status Cards --}}
                    <div class="row g-3 mb-3">
                        <div class="col-3">
                            <div class="text-center">
                                <div class="h4 mb-0" id="simple-active-count">-</div>
                                <small class="text-muted">Active</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-danger" id="simple-expired-count">-</div>
                                <small class="text-muted">Expired</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <div class="h4 mb-0 text-warning" id="simple-expiring-count">-</div>
                                <small class="text-muted">Expiring</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="text-center">
                                <small class="text-muted">Last Run</small>
                                <div class="small" id="simple-last-run">Never</div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-primary btn-sm w-100" onclick="runSimpleAutomation('completion')">
                                <i class="fas fa-play me-1"></i>
                                Complete Expired
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-warning btn-sm w-100" onclick="runSimpleAutomation('warning')">
                                <i class="fas fa-bell me-1"></i>
                                Send Warnings
                            </button>
                        </div>
                    </div>

                    {{-- Result Display --}}
                    <div id="simple-automation-result" class="mt-2"></div>
                </div>
            </div>
        </div>

        <script>
        // ✅ SIMPLE: Automation functions
        function loadSimpleAutomationStatus() {
            fetch('/api/dashboard/simple-automation-status')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('simple-active-count').textContent = data.data.active_magang;
                        document.getElementById('simple-expired-count').textContent = data.data.expired_magang;
                        document.getElementById('simple-expiring-count').textContent = data.data.expiring_soon;
                        document.getElementById('simple-last-run').textContent = 
                            data.data.last_auto_run !== 'Never' ? 
                            new Date(data.data.last_auto_run).toLocaleString('id-ID') : 
                            'Never';
                    }
                })
                .catch(error => console.error('Error loading automation status:', error));
        }

        function runSimpleAutomation(type) {
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Running...';

            fetch('/api/dashboard/trigger-simple-automation', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('simple-automation-result');
                
                if (data.success) {
                    if (type === 'completion') {
                        resultDiv.innerHTML = `<div class="alert alert-success">✅ Completed ${data.data.completed} magang (checked ${data.data.total_checked})</div>`;
                    } else {
                        resultDiv.innerHTML = `<div class="alert alert-success">📧 Sent ${data.data.notifications_sent} warnings</div>`;
                    }
                    setTimeout(() => loadSimpleAutomationStatus(), 2000);
                } else {
                    resultDiv.innerHTML = `<div class="alert alert-danger">❌ ${data.message}</div>`;
                }
            })
            .catch(error => {
                document.getElementById('simple-automation-result').innerHTML = 
                    '<div class="alert alert-danger">❌ Error occurred</div>';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }

        // ✅ LOAD: Initial status
        document.addEventListener('DOMContentLoaded', function() {
            loadSimpleAutomationStatus();
            
            // ✅ REFRESH: Every 2 minutes
            setInterval(loadSimpleAutomationStatus, 120000);
        });
        </script>
        @endif
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true // Penting! Ini akan mengirim cookies dengan request
        });

        function showError(elementId, message) {
            const element = document.getElementById(elementId);
            if (element) {
                element.innerHTML = `<span class="text-danger">${message}</span>`;
            }
        }

        // Fungsi untuk memuat data summary dashboard
        function loadDashboardSummary() {
            api.get('/dashboard/summary')
                .then(function(response) {
                    console.log('Respons dari server:', response.data); // Debugging
                    if (response.data.success) {
                        const data = response.data.data;
                        document.getElementById('mahasiswa-aktif').innerText = data.mahasiswa_aktif || '0';
                        document.getElementById('perusahaan-mitra').innerText = data.perusahaan_mitra || '0';
                        document.getElementById('lowongan-aktif').innerText = data.lowongan_aktif || '0';
                    } else {
                        console.error('Error dari server:', response.data.message);
                        showError('mahasiswa-aktif', 'Error');
                        showError('perusahaan-mitra', 'Error');
                        showError('lowongan-aktif', 'Error');
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    if (error.response && error.response.status === 401) {
                        // Redirect ke login jika tidak terautentikasi
                        window.location.href = '/login';
                    }
                    showError('mahasiswa-aktif', 'Error');
                    showError('perusahaan-mitra', 'Error');
                    showError('lowongan-aktif', 'Error');
                });
        }

        function loadActivePeriod() {
            axios.get('/api/dashboard/active-period')
                .then(function(response) {
                    if (response.data.success && response.data.data) {
                        const period = response.data.data;
                        console.log("Active period data:", period); // Debugging output

                        try {
                            // Format for displaying academic year
                            document.getElementById('period-title').innerText =
                                `Tahun Akademik ${period.waktu || 'Unknown'}`;
                            document.getElementById('period-description').innerText =
                                `Periode aktif yang sedang berjalan`;

                            // Safely parse dates with error handling
                            let startDate, endDate;
                            try {
                                startDate = period.tgl_mulai ? new Date(period.tgl_mulai) : new Date();
                                endDate = period.tgl_selesai ? new Date(period.tgl_selesai) : new Date(startDate
                                    .getTime() + 365 * 24 * 60 * 60 * 1000);

                                // Check if dates are valid
                                if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
                                    throw new Error('Invalid date format');
                                }
                            } catch (e) {
                                console.error("Date parsing error:", e);
                                startDate = new Date();
                                endDate = new Date(startDate.getTime() + 365 * 24 * 60 * 60 * 1000);
                            }

                            const options = {
                                day: 'numeric',
                                month: 'short',
                                year: 'numeric'
                            };

                            document.getElementById('period-start-date').innerText = startDate.toLocaleDateString(
                                'id-ID', options);
                            document.getElementById('period-end-date').innerText = endDate.toLocaleDateString('id-ID',
                                options);

                            // Calculate actual academic year progress
                            const today = new Date();
                            const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                            const daysElapsed = Math.min(totalDays, Math.ceil((today - startDate) / (1000 * 60 * 60 *
                                24)));
                            const daysLeft = Math.max(0, Math.ceil((endDate - today) / (1000 * 60 * 60 * 24)));

                            // Calculate progress percentage
                            const progressPercent = Math.max(0, Math.min(100, Math.round((daysElapsed / totalDays) *
                                100)));

                            // Update progress bar
                            const progressBar = document.getElementById('period-progress-bar');
                            if (progressBar) {
                                progressBar.style.width = `${progressPercent}%`;
                                progressBar.setAttribute('aria-valuenow', progressPercent);
                            }

                            // Update text elements if they exist
                            const progressText = document.getElementById('period-progress-text');
                            if (progressText) {
                                progressText.innerText = `${progressPercent}%`;
                            }

                            const daysLeftElement = document.getElementById('period-days-left');
                            if (daysLeftElement) {
                                daysLeftElement.innerText = `${daysLeft} hari`;
                            }

                            // Update the large days counter AFTER daysLeft is calculated
                            const daysLeftElementLarge = document.getElementById('period-days-left-large');
                            if (daysLeftElementLarge) {
                                daysLeftElementLarge.innerText = `${daysLeft} hari`;
                            }

                            // Remove any no-period-active message if it exists
                            const noPeriodAlert = document.getElementById('no-period-alert');
                            if (noPeriodAlert) {
                                noPeriodAlert.remove();
                            }
                        } catch (err) {
                            console.error("Error processing period data:", err);
                            handleNoPeriodActive("Terjadi kesalahan saat memproses data periode");
                        }
                    } else {
                        // No active period found
                        handleNoPeriodActive();
                    }
                })
                .catch(function(error) {
                    console.error('Error loading active period:', error);
                    handleNoPeriodActive("Terjadi kesalahan saat memuat data periode");
                });
        }

        function handleNoPeriodActive(errorMessage = null) {
            // Update title and description
            const daysLeftElementLarge = document.getElementById('period-days-left-large');
            if (daysLeftElementLarge) {
                daysLeftElementLarge.innerText = '0 hari';
            }
            const titleElement = document.getElementById('period-title');
            if (titleElement) {
                titleElement.innerText = 'Tidak ada periode aktif';
            }

            const descElement = document.getElementById('period-description');
            if (descElement) {
                descElement.innerText = errorMessage || 'Silahkan aktifkan periode di menu Kelola Periode';
            }

            // Update dates and progress elements
            const startDateElement = document.getElementById('period-start-date');
            if (startDateElement) {
                startDateElement.innerText = '--/--/----';
            }

            const endDateElement = document.getElementById('period-end-date');
            if (endDateElement) {
                endDateElement.innerText = '--/--/----';
            }

            const progressTextElement = document.getElementById('period-progress-text');
            if (progressTextElement) {
                progressTextElement.innerText = '0%';
            }

            const daysLeftElement = document.getElementById('period-days-left');
            if (daysLeftElement) {
                daysLeftElement.innerText = '0 hari';
            }

            // Update progress bar
            const progressBar = document.getElementById('period-progress-bar');
            if (progressBar) {
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
            }

            // Add warning alert if not already present
            if (!document.getElementById('no-period-alert')) {
                const alertDiv = document.createElement('div');
                alertDiv.id = 'no-period-alert';
                alertDiv.className = 'alert alert-warning mt-3';
                alertDiv.innerHTML = `
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        <strong>Perhatian!</strong> Tidak ada periode aktif saat ini.
                                        <div class="mt-1">
                                            <a href="/periode" class="btn btn-sm btn-warning">Aktifkan Periode</a>
                                        </div>
                                    </div>
                                </div>
                            `;

                // Find the container and safely append the alert
                const container = document.querySelector('.card-body .row.g-0 .col-md-8');
                if (container) {
                    container.appendChild(alertDiv);
                }
            }
        }

        // Fungsi untuk memuat data aplikasi terbaru
        function loadLatestApplications() {
            api.get('/dashboard/latest-applications')
                .then(function(response) {
                    if (response.data.success) {
                        const applications = response.data.data;
                        const tableBody = document.getElementById('latest-applications');

                        tableBody.innerHTML = '';

                        if (applications.length === 0) {
                            tableBody.innerHTML = `
                                                                                                <tr>
                                                                                                    <td colspan="3" class="text-center">
                                                                                                        Tidak ada permintaan magang terbaru
                                                                                                    </td>
                                                                                                </tr>
                                                                                            `;
                            return;
                        }

                        applications.forEach(app => {
                            const statusClass = app.status === 'diterima' ? 'bg-primary' : 'bg-secondary';
                            const statusLabel = app.status === 'diterima' ? 'Diterima' : 'Menunggu';

                            const row = `
                                                                                                <tr>
                                                                                                    <td>
                                                                                                        <div class="fw-bold" style="color: #2D2D2D;">${app.nama_mahasiswa}</div>
                                                                                                        <div class="text-muted small fw-bold">NIM : ${app.nim}</div>
                                                                                                    </td>
                                                                                                    <td>${app.perusahaan}</td>
                                                                                                    <td>
                                                                                                        <span class="badge rounded-pill ${statusClass}">${statusLabel}</span>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            `;
                            tableBody.innerHTML += row;
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Error loading latest applications:', error);
                    document.getElementById('latest-applications').innerHTML = `
                                                                                        <tr>
                                                                                            <td colspan="3" class="text-center text-danger">
                                                                                                Gagal memuat data. Coba lagi nanti.
                                                                                            </td>
                                                                                        </tr>
                                                                                    `;
                });
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardSummary();
            loadLatestApplications();
            loadActivePeriod();
        });
    </script>
@endpush
