{{-- filepath: c:\laragon\www\JTIintern\resources\views\pages\mahasiswa\MhsLamaran.blade.php --}}

@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid py-4">

        @if (isset($magangInfo) && $magangInfo)
            <!-- Magang Aktif Card dengan Loading -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow border-0">
                        <!-- Skeleton Loader untuk Magang Card -->
                        <div class="skeleton-loader" id="magang-skeleton">
                            <div class="card-header pb-0">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="d-flex align-items-center">
                                            <div class="skeleton-avatar me-3"></div>
                                            <div>
                                                <div class="skeleton-text skeleton-text-lg mb-2"></div>
                                                <div class="skeleton-text skeleton-text-md mb-1"></div>
                                                <div class="skeleton-text skeleton-text-sm"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4 text-end">
                                        <div class="skeleton-badge"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="skeleton-text skeleton-text-sm mb-2"></div>
                                        <div class="skeleton-progress-bar mb-3"></div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                                <div class="skeleton-text skeleton-text-lg"></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                                <div class="skeleton-text skeleton-text-lg"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="skeleton-button"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Magang Card Content (Hidden Initially) -->
                        <div class="real-content d-none" id="real-magang">
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card shadow-sm border-0">
                                        {{-- ✅ KEEP: Header structure existing --}}
                                        <div class="card-header bg-gradient-primary">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="text-white mb-0">
                                                    <i class="fas fa-briefcase me-2"></i>
                                                    {{ $magangInfo['status'] === 'selesai' ? 'Magang Selesai' : 'Magang Aktif' }}
                                                </h6>
                                                <span
                                                    class="badge {{ $magangInfo['status'] === 'selesai' ? 'bg-light text-dark' : 'bg-success' }}">
                                                    {{ $magangInfo['status'] === 'selesai' ? 'Selesai' : 'Berlangsung' }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- ✅ KEEP: Body structure existing --}}
                                        <div class="card-body p-4">
                                            <div class="row">
                                                {{-- ✅ Company Info Section --}}
                                                <div class="col-lg-8">
                                                    <div class="company-info mb-4">
                                                        <div class="d-flex align-items-center mb-3">
                                                            @if ($magangInfo['logo_url'])
                                                                <img src="{{ $magangInfo['logo_url'] }}"
                                                                    class="company-logo me-3"
                                                                    alt="{{ $magangInfo['nama_perusahaan'] }}"
                                                                    style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px;">
                                                            @else
                                                                <div class="company-logo-placeholder me-3 bg-light d-flex align-items-center justify-content-center"
                                                                    style="width: 60px; height: 60px; border-radius: 8px;">
                                                                    <i class="fas fa-building text-muted"></i>
                                                                </div>
                                                            @endif

                                                            <div>
                                                                <h5 class="mb-1 text-dark">
                                                                    {{ $magangInfo['judul_lowongan'] }}</h5>
                                                                <p class="text-muted mb-0">
                                                                    {{ $magangInfo['nama_perusahaan'] }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- ✅ Progress Section --}}
                                                    <div class="progress-section mb-4">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-sm font-weight-bold">Progress Magang</span>
                                                            <span
                                                                class="text-sm text-{{ $magangInfo['status'] === 'selesai' ? 'secondary' : 'success' }}">
                                                                {{ $magangInfo['progress'] }}%
                                                            </span>
                                                        </div>

                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar {{ $magangInfo['status'] === 'selesai' ? 'bg-secondary' : 'bg-gradient-success' }}"
                                                                role="progressbar"
                                                                style="width: {{ $magangInfo['progress'] }}%"
                                                                aria-valuenow="{{ $magangInfo['progress'] }}"
                                                                aria-valuemin="0" aria-valuemax="100">
                                                            </div>
                                                        </div>

                                                        <div class="mt-2">
                                                            <small
                                                                class="text-muted">{{ $magangInfo['status_text'] }}</small>
                                                        </div>
                                                    </div>

                                                    {{-- ✅ Info Details Section --}}
                                                    <div class="info-details">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <div class="info-item">
                                                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                                    <span class="text-sm">
                                                                        <strong>Mulai:</strong>
                                                                        {{ $magangInfo['tgl_mulai_formatted'] }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="info-item">
                                                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                                                    <span class="text-sm">
                                                                        <strong>Selesai:</strong>
                                                                        {{ $magangInfo['tgl_selesai_formatted'] }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            @if ($magangInfo['total_durasi'] > 0)
                                                                <div class="col-md-6">
                                                                    <div class="info-item">
                                                                        <i class="fas fa-clock text-primary me-2"></i>
                                                                        <span class="text-sm">
                                                                            <strong>Durasi:</strong>
                                                                            {{ $magangInfo['total_durasi'] }} hari
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if ($magangInfo['sisa_hari'] > 0 && $magangInfo['status'] !== 'selesai')
                                                                <div class="col-md-6">
                                                                    <div class="info-item">
                                                                        <i
                                                                            class="fas fa-hourglass-half text-warning me-2"></i>
                                                                        <span class="text-sm">
                                                                            <strong>Sisa:</strong>
                                                                            {{ $magangInfo['sisa_hari'] }} hari
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- ✅ Action Section --}}
                                                <div class="col-lg-4">
                                                    {{-- ✅ Supervisor Info --}}
                                                    @if ($magangInfo['nama_pembimbing'])
                                                        <div class="supervisor-card bg-light rounded p-3 mb-3">
                                                            <div class="text-center">
                                                                <i class="fas fa-user-tie fa-2x text-primary mb-2"></i>
                                                                <h6 class="mb-1">Pembimbing</h6>
                                                                <p class="text-sm mb-1">
                                                                    {{ $magangInfo['nama_pembimbing'] }}</p>
                                                                @if ($magangInfo['nip_pembimbing'])
                                                                    <small class="text-muted">NIP:
                                                                        {{ $magangInfo['nip_pembimbing'] }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- ✅ Action Buttons --}}
                                                    <div class="action-buttons text-center">
                                                        @if ($magangInfo['status'] === 'aktif')
                                                            <a href="/mahasiswa/magang"
                                                                class="btn btn-primary btn-sm mb-2 w-100">
                                                                <i class="fas fa-eye me-1"></i> Detail Magang
                                                            </a>
                                                            <a href="/mahasiswa/log"
                                                                class="btn btn-outline-primary btn-sm w-100">
                                                                <i class="fas fa-book me-1"></i> Log Aktivitas
                                                            </a>
                                                        @else
                                                            <a href="/mahasiswa/evaluasi"
                                                                class="btn btn-outline-secondary btn-sm w-100 mb-2">
                                                                <i class="fas fa-chart-line me-1"></i> Lihat Evaluasi
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- ✅ EVALUASI SECTION: Only for completed magang --}}
                                            @if (isset($magangInfo['is_expired']) && $magangInfo['is_expired'] && $magangInfo['status'] === 'selesai')
                                                <hr class="my-4">

                                                {{-- ✅ Evaluasi Alert --}}
                                                <div class="evaluasi-section">
                                                    <div class="alert alert-warning border-0 shadow-sm">
                                                        <div class="d-flex align-items-start">
                                                            <i
                                                                class="fas fa-exclamation-triangle fa-lg text-warning me-3 mt-1"></i>
                                                            <div class="flex-grow-1">
                                                                <h6 class="alert-heading mb-2">Input Nilai Diperlukan</h6>
                                                                <p class="mb-3 text-sm">
                                                                    Magang Anda telah selesai. Silakan input nilai dari
                                                                    pengawas lapangan
                                                                    untuk menyelesaikan proses evaluasi.
                                                                </p>
                                                                <button class="btn btn-warning btn-sm"
                                                                    onclick="showEvaluasiForm({{ $magangInfo['id_magang'] }})">
                                                                    <i class="fas fa-edit me-1"></i> Input Nilai Sekarang
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- ✅ Evaluasi Form (Hidden by default) --}}
                                                    <div id="evaluasi-form-container-{{ $magangInfo['id_magang'] }}"
                                                        style="display: none;">
                                                        <div class="card border-warning mt-3 shadow-sm">
                                                            <div class="card-header bg-warning">
                                                                <h6 class="mb-0 text-dark">
                                                                    <i class="fas fa-clipboard-check me-2"></i>
                                                                    Form Input Nilai Magang
                                                                </h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <form id="evaluasi-form-{{ $magangInfo['id_magang'] }}"
                                                                    enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="hidden" name="id_magang"
                                                                        value="{{ $magangInfo['id_magang'] }}">

                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label text-sm font-weight-bold">
                                                                                <i
                                                                                    class="fas fa-star text-warning me-1"></i>
                                                                                Nilai dari Perusahaan *
                                                                            </label>
                                                                            <input type="number" name="nilai_perusahaan"
                                                                                class="form-control form-control-sm"
                                                                                min="0" max="100"
                                                                                step="0.1" placeholder="0-100"
                                                                                required>
                                                                            <div class="form-text">Masukkan nilai yang
                                                                                diberikan pengawas lapangan</div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label
                                                                                class="form-label text-sm font-weight-bold">
                                                                                <i
                                                                                    class="fas fa-file-upload text-info me-1"></i>
                                                                                File Nilai/Sertifikat *
                                                                            </label>
                                                                            <input type="file"
                                                                                name="file_nilai_perusahaan"
                                                                                class="form-control form-control-sm"
                                                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                                                            <div class="form-text">PDF/Gambar, maksimal 5MB
                                                                            </div>
                                                                        </div>
                                                                    </div>



                                                                    <div class="text-center mt-4">
                                                                        <button type="submit"
                                                                            class="btn btn-success me-2"
                                                                            id="submit-btn-{{ $magangInfo['id_magang'] }}">
                                                                            <i class="fas fa-paper-plane me-1"></i> Submit
                                                                            Evaluasi
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-outline-secondary"
                                                                            onclick="hideEvaluasiForm({{ $magangInfo['id_magang'] }})">
                                                                            <i class="fas fa-times me-1"></i> Batal
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($showLamaranHistory) && $showLamaranHistory)
            <!-- ✅ CONDITIONAL: Riwayat Lamaran Card - HANYA TAMPILKAN JIKA TIDAK ADA MAGANG AKTIF -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-header pb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0">Riwayat Lamaran</h6>
                                    <p class="text-sm mb-0">Pantau semua lamaran yang telah Anda ajukan</p>
                                </div>
                                <div class="card-header-controls d-flex align-items-center gap-2">
                                    <!-- Filter Dropdown -->
                                    <div class="filter-container">
                                        <select class="form-select form-select-sm" id="statusFilter"
                                            onchange="filterLamaran()" style="min-width: 140px;">
                                            <option value="all">Semua Status</option>
                                            <option value="menunggu">Menunggu</option>
                                            <option value="diterima">Diterima</option>
                                            <option value="ditolak">Ditolak</option>
                                        </select>
                                    </div>

                                    <!-- Refresh Button -->
                                    <button class="btn btn-outline-primary btn-sm" id="refreshLamaranBtn"
                                        onclick="refreshLamaranData()">
                                        <i class="fas fa-sync-alt me-1" id="refreshIcon"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <!-- Table Skeleton Loading -->
                            <div id="table-skeleton-loading">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <div class="skeleton-text skeleton-text-xs"></div>
                                                </th>
                                                <th>
                                                    <div class="skeleton-text skeleton-text-xs"></div>
                                                </th>
                                                <th>
                                                    <div class="skeleton-text skeleton-text-xs"></div>
                                                </th>
                                                <th>
                                                    <div class="skeleton-text skeleton-text-xs"></div>
                                                </th>
                                                <th>
                                                    <div class="skeleton-text skeleton-text-xs"></div>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($i = 1; $i <= 5; $i++)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="skeleton-avatar me-3"></div>
                                                            <div>
                                                                <div class="skeleton-text skeleton-text-sm mb-1"></div>
                                                                <div class="skeleton-text skeleton-text-xs"></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="skeleton-text skeleton-text-sm mb-1"></div>
                                                        <div class="skeleton-text skeleton-text-xs"></div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="skeleton-badge mx-auto"></div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="skeleton-text skeleton-text-xs mb-1"></div>
                                                        <div class="skeleton-text skeleton-text-xs"></div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <div class="skeleton-button-sm"></div>
                                                            <div class="skeleton-button-sm"></div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Real Table Content (Hidden Initially) -->
                            <div id="real-table-content" class="d-none">
                                @if (isset($lamaranHistory) && $lamaranHistory->count() > 0)
                                    <!-- Table with Data -->
                                    <div class="table-responsive">
                                        <table class="table table-hover align-items-center mb-0" id="lamaranTable">
                                            <thead class="table-header">
                                                <tr>
                                                    <th
                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Perusahaan</th>
                                                    <th
                                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                        Posisi</th>
                                                    <th
                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Status</th>
                                                    <th
                                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                        Tanggal</th>
                                                    <th class="text-secondary opacity-7"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="lamaranTableBody">
                                                @foreach ($lamaranHistory as $index => $lamaran)
                                                    <tr class="lamaran-row fade-in-row"
                                                        data-status="{{ $lamaran->status }}"
                                                        data-index="{{ $index }}">
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div class="company-avatar">
                                                                    @php
                                                                        $logoSrc = null;
                                                                        $hasLogo = false;

                                                                        if (
                                                                            isset($lamaran->logo_url) &&
                                                                            !empty($lamaran->logo_url)
                                                                        ) {
                                                                            $logoSrc = $lamaran->logo_url;
                                                                            $hasLogo = true;
                                                                        } elseif (
                                                                            isset($lamaran->logo) &&
                                                                            !empty($lamaran->logo)
                                                                        ) {
                                                                            if (
                                                                                str_starts_with($lamaran->logo, 'http')
                                                                            ) {
                                                                                $logoSrc = $lamaran->logo;
                                                                            } elseif (
                                                                                str_starts_with(
                                                                                    $lamaran->logo,
                                                                                    'storage/',
                                                                                )
                                                                            ) {
                                                                                $logoSrc = asset($lamaran->logo);
                                                                            } else {
                                                                                $logoSrc = asset(
                                                                                    'storage/' . $lamaran->logo,
                                                                                );
                                                                            }
                                                                            $hasLogo = true;
                                                                        }
                                                                    @endphp

                                                                    @if ($hasLogo && $logoSrc)
                                                                        <img src="{{ $logoSrc }}"
                                                                            class="avatar avatar-sm me-3 border-radius-lg"
                                                                            alt="Logo {{ $lamaran->nama_perusahaan }}"
                                                                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center\\'><i class=\\'fas fa-building text-white text-sm\\'></i></div>';">
                                                                    @else
                                                                        <div
                                                                            class="avatar avatar-sm bg-gradient-secondary me-3 border-radius-lg d-flex align-items-center justify-content-center">
                                                                            <i
                                                                                class="fas fa-building text-white text-sm"></i>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm font-weight-bold">
                                                                        {{ $lamaran->nama_perusahaan }}</h6>
                                                                    @if ($lamaran->nama_kota)
                                                                        <p class="text-xs text-secondary mb-0">
                                                                            <i
                                                                                class="fas fa-map-marker-alt me-1"></i>{{ $lamaran->nama_kota }}
                                                                        </p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p class="text-sm font-weight-bold mb-0">
                                                                {{ $lamaran->judul_lowongan }}</p>
                                                            <p class="text-xs text-secondary mb-0">
                                                                {{ Str::limit($lamaran->deskripsi_lowongan ?? 'Tidak ada deskripsi', 50) }}
                                                            </p>
                                                        </td>
                                                        <td class="align-middle text-center text-sm">
                                                            @if ($lamaran->status == 'diterima')
                                                                <span class="badge bg-gradient-success status-badge">
                                                                    <i class="fas fa-check me-1"></i>Diterima
                                                                </span>
                                                            @elseif($lamaran->status == 'ditolak')
                                                                <span class="badge bg-gradient-danger status-badge">
                                                                    <i class="fas fa-times me-1"></i>Ditolak
                                                                </span>
                                                            @else
                                                                <span class="badge bg-gradient-warning status-badge">
                                                                    <i class="fas fa-clock me-1"></i>Menunggu
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-secondary text-xs font-weight-bold">
                                                                {{ \Carbon\Carbon::parse($lamaran->tanggal_lamaran)->format('d M Y') }}
                                                            </span>
                                                            <br>
                                                            <span class="text-xs text-secondary">
                                                                {{ \Carbon\Carbon::parse($lamaran->tanggal_lamaran)->diffForHumans() }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <!-- ✅ DETAIL BUTTON -->
                                                                <button class="btn btn-link text-primary mb-0 px-1 me-1"
                                                                    onclick="detailLamaran({{ $lamaran->id_lamaran }})"
                                                                    data-bs-toggle="tooltip" title="Lihat Detail">
                                                                    <i class="fas fa-eye text-xs"></i>
                                                                </button>

                                                                <!-- ✅ CANCEL BUTTON (hanya untuk status menunggu) -->
                                                                @if ($lamaran->status === 'menunggu')
                                                                    <button class="btn btn-link text-danger mb-0 px-1"
                                                                        onclick="cancelLamaran({{ $lamaran->id_lamaran }})"
                                                                        data-bs-toggle="tooltip" title="Batalkan Lamaran">
                                                                        <i class="fas fa-times text-xs"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination if needed -->
                                    @if (method_exists($lamaranHistory, 'links'))
                                        <div class="mt-3">
                                            {{ $lamaranHistory->links() }}
                                        </div>
                                    @endif
                                @else
                                    <!-- Empty State Table -->
                                    <div class="empty-table-state text-center py-5">
                                        <div class="empty-table-icon mb-3">
                                            <i class="fas fa-clipboard-list"></i>
                                        </div>
                                        <h6 class="mb-2">Belum Ada Lamaran</h6>
                                        <p class="text-muted mb-4">Anda belum mengajukan lamaran magang. Mulai cari
                                            lowongan yang sesuai dengan minat Anda!</p>
                                        <a href="{{ route('mahasiswa.lowongan') }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-search me-2"></i>Cari Lowongan
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- ✅ TAMPILKAN PESAN KETIKA ADA MAGANG AKTIF -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow border-0">
                        <div class="card-body text-center py-5">
                            <div class="magang-active-state">
                                <div class="magang-active-icon mb-4">
                                    <i class="fas fa-briefcase fa-4x text-success opacity-8"></i>
                                </div>
                                <h5 class="mb-3 text-success">Selamat! Anda Sedang Magang</h5>
                                <p class="text-muted mb-4">
                                    Anda saat ini sedang menjalani magang aktif. Riwayat lamaran tidak ditampilkan selama
                                    masa magang berlangsung.
                                    Fokus pada aktivitas magang Anda dan jangan lupa untuk mengisi logbook secara rutin.
                                </p>
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('mahasiswa.logaktivitas') }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-book me-2"></i>Buka Logbook
                                    </a>
                                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-home me-2"></i>Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ✅ MODAL: Form Input Nilai Evaluasi -->
    <div class="modal fade" id="evaluasiFormModal" tabindex="-1" aria-labelledby="evaluasiFormModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="evaluasiFormModalLabel">Input Nilai Perusahaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="evaluasiForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" id="id_magang_input" name="id_magang" value="">

                        <div class="mb-3">
                            <label for="nilai_perusahaan" class="form-label">Nilai dari Perusahaan</label>
                            <input type="number" class="form-control" id="nilai_perusahaan" name="nilai_perusahaan"
                                min="0" max="100" required>
                        </div>

                        <div class="mb-3">
                            <label for="file_nilai_perusahaan" class="form-label">Upload File Nilai (PDF)</label>
                            <input type="file" class="form-control" id="file_nilai_perusahaan"
                                name="file_nilai_perusahaan" accept=".pdf" required>
                            <div class="form-text">File harus dalam format PDF</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="submitEvaluasi()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/lamaran.css') }}">
@endpush



@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. ✅ GLOBAL VARIABLES
        const serverData = {
            magangInfo: @json(isset($magangInfo) && $magangInfo ? true : false),
            magangData: @json($magangInfo ?? null),
            lowonganRoute: "{{ route('mahasiswa.lowongan') }}"
        };

        console.log('🎯 Server data loaded:', serverData);

        $(document).ready(function() {
            console.log('🚀 === LOADING CONTENT AND TABLES ===');
            simulateContentLoading();
        });

        function simulateContentLoading() {
            console.log('⏳ Starting content loading simulation...');
            console.log('📊 Server data check:', {
                hasMagangInfo: serverData.magangInfo,
                magangData: serverData.magangData
            });

            setTimeout(() => loadStatsCard(1), 300);
            setTimeout(() => loadStatsCard(2), 600);
            setTimeout(() => loadStatsCard(3), 900);
            setTimeout(() => loadStatsCard(4), 1200);

            // ✅ FIX: Simplify magang loading logic with fallbacks
            if (serverData.magangInfo && serverData.magangData) {
                console.log(`🏢 Loading magang card with status: ${serverData.magangData.status}`);

                // Show skeleton first
                const skeleton = document.getElementById('magang-skeleton');
                if (skeleton) {
                    skeleton.classList.remove('d-none');
                }

                // Set timeout to ensure we don't get stuck in skeleton
                const fallbackTimer = setTimeout(() => {
                    console.log('⚠️ Fallback timer triggered - loading magang card directly');
                    loadMagangCard();
                }, 5000); // 5-second fallback

                // Try to check evaluation first
                try {
                    checkEvaluationExists(serverData.magangData.id_magang, fallbackTimer);
                } catch (error) {
                    console.error('❌ Error during evaluation check:', error);
                    clearTimeout(fallbackTimer);
                    loadMagangCard(); // Fallback to regular card on error
                }
            } else {
                console.log('ℹ️ No magang data - showing lamaran history only');
            }

            setTimeout(() => loadTableContent(), 2000);
        }

        function showEvaluasiForm(id_magang) {
            // Hide any other open forms (optional)
            document.querySelectorAll('[id^="evaluasi-form-container-"]').forEach(form => {
                form.style.display = 'none';
            });

            // Show the specific form
            document.getElementById('evaluasi-form-container-' + id_magang).style.display = 'block';

            // Set up the form submission handler
            setupEvaluasiSubmission(id_magang);
        }

        function hideEvaluasiForm(id_magang) {
            // Hide the specific form
            document.getElementById('evaluasi-form-container-' + id_magang).style.display = 'none';
        }

        // ✅ UPDATE: Function untuk check evaluation status
        function checkEvaluationExists(idMagang, fallbackTimer) {
            console.log('🔍 Checking if evaluation exists for magang:', idMagang);

            // Make API call to check evaluation status
            fetch(`/api/evaluasi-magang/status/${idMagang}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('📊 Evaluation status check result:', data);

                    // Clear fallback timer since we got a response
                    if (fallbackTimer) clearTimeout(fallbackTimer);

                    // Hide skeleton regardless of response
                    const skeleton = document.getElementById('magang-skeleton');
                    if (skeleton) {
                        skeleton.style.transition = 'opacity 0.3s ease';
                        skeleton.style.opacity = '0';
                        setTimeout(() => skeleton.classList.add('d-none'), 300);
                    }

                    if (data.success && data.data) {
                        // ✅ UPDATE: Jika ada evaluasi, tampilkan completed message
                        console.log('✅ Evaluation exists - showing completed message');
                        showCompletedEvaluationMessage(data.data);

                        // ✅ NEW: Update lamaran history visibility
                        showLamaranHistorySection();
                    } else {
                        // Evaluation doesn't exist, load regular magang card
                        loadMagangCard();
                    }
                })
                .catch(error => {
                    console.error('❌ Error checking evaluation status:', error);

                    // Clear fallback timer since we're handling the error
                    if (fallbackTimer) clearTimeout(fallbackTimer);

                    // On error, still try to load magang card
                    loadMagangCard();
                });
        }

        // ✅ UPDATE: Function untuk menampilkan pesan setelah submit evaluasi
        function showCompletedEvaluationMessage(evaluationData) {
            console.log('📝 Showing completed evaluation message:', evaluationData);

            // ✅ HIDE magangInfo content
            const realContent = document.getElementById('real-magang');
            if (!realContent) return;

            // ✅ NEW: Show completion message
            const completedHTML = `
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-gradient-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Evaluasi Magang Telah Disubmit
                            </h6>
                            <span class="badge bg-light text-dark">
                                ${evaluationData.status_evaluasi === 'pending' ? 'Menunggu Review' : 'Selesai'}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-success mb-4">
                            <i class="fas fa-clipboard-check me-2"></i>
                            <strong>Terima kasih! Evaluasi magang Anda telah berhasil disubmit.</strong>
                            <p class="mb-0 mt-2">Dosen pembimbing akan segera mereview dan memproses evaluasi Anda.</p>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nilai dari Perusahaan:</label>
                                    <h4>${evaluationData.nilai_perusahaan}</h4>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Status Evaluasi:</label>
                                    <span class="badge ${evaluationData.status_evaluasi === 'pending' ? 'bg-warning' : 'bg-success'} px-3 py-2">
                                        ${evaluationData.status_evaluasi === 'pending' ? 'Menunggu Review' : 'Selesai'}
                                    </span>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Submit:</label>
                                    <p>${new Date(evaluationData.created_at).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    })}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6 text-center">
                                <div class="completion-icon mb-3">
                                    <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                                </div>
                                
                                <a href="/mahasiswa/evaluasi" class="btn btn-outline-primary btn-sm mt-3">
                                    <i class="fas fa-chart-line me-2"></i> Lihat Data Evaluasi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // ✅ UPDATE: Remove existing content and show completion message
            realContent.innerHTML = completedHTML;
            realContent.classList.remove('d-none');
            realContent.style.opacity = '0';
            realContent.style.transform = 'translateY(30px)';
            realContent.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';

            setTimeout(() => {
                realContent.style.opacity = '1';
                realContent.style.transform = 'translateY(0)';

                // ✅ NEW: Show lamaran history after a short delay
                setTimeout(() => {
                    showLamaranHistorySection();
                }, 500);
            }, 50);
        }



        // ✅ NEW: Function untuk load data riwayat lamaran
        function loadLamaranHistoryData() {
            console.log('📊 Loading lamaran history data');

            // Show loading state
            const tableSkeletonLoading = document.getElementById('table-skeleton-loading');
            const realTableContent = document.getElementById('real-table-content');

            if (!tableSkeletonLoading || !realTableContent) return;

            tableSkeletonLoading.classList.remove('d-none');
            realTableContent.classList.add('d-none');

            // Load data from API
            fetch('/api/mahasiswa/lamaran/data')
                .then(response => response.json())
                .then(data => {
                    console.log('📋 Lamaran history data loaded:', data);

                    if (data.success && data.lamaranHistory && data.lamaranHistory.length > 0) {
                        // Build table HTML
                        let tableHTML = `
                            <div class="table-responsive">
                                <table class="table table-flush align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Perusahaan</th>
                                            <th>Posisi</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lamaranTableBody">
                        `;

                        data.lamaranHistory.forEach((lamaran, index) => {
                            const status = lamaran.status;
                            let statusBadge = '';

                            if (status === 'menunggu') {
                                statusBadge = '<span class="badge bg-warning text-dark">Menunggu</span>';
                            } else if (status === 'diterima') {
                                statusBadge = '<span class="badge bg-success">Diterima</span>';
                            } else if (status === 'ditolak') {
                                statusBadge = '<span class="badge bg-danger">Ditolak</span>';
                            }

                            tableHTML += `
                                <tr class="lamaran-row fade-in-row" data-status="${status}">
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="avatar avatar-sm rounded-circle bg-light me-2">
                                                ${lamaran.logo_url ? 
                                                    `<img src="${lamaran.logo_url}" alt="${lamaran.nama_perusahaan}" class="w-100 h-100">` :
                                                    `<i class="fas fa-building text-secondary"></i>`
                                                }
                                            </div>
                                            <span class="text-sm font-weight-bold">${lamaran.nama_perusahaan}</span>
                                        </div>
                                    </td>
                                    <td>${lamaran.judul_lowongan}</td>
                                    <td class="text-center">${statusBadge}</td>
                                    <td class="text-center">
                                        <span class="text-xs">${new Date(lamaran.tanggal_lamaran).toLocaleDateString('id-ID')}</span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-info" onclick="detailLamaran(${lamaran.id_lamaran})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });

                        tableHTML += `
                                    </tbody>
                                </table>
                            </div>
                        `;

                        realTableContent.innerHTML = tableHTML;
                    } else {
                        // Show empty state
                        realTableContent.innerHTML = `
                            <div class="text-center p-5">
                                <i class="fas fa-inbox fa-3x text-secondary mb-3"></i>
                                <h6>Belum Ada Riwayat Lamaran</h6>
                                <p class="text-muted">Anda belum pernah mengajukan lamaran magang.</p>
                                <a href="/mahasiswa/lowongan" class="btn btn-primary btn-sm mt-2">
                                    <i class="fas fa-search me-2"></i>Cari Lowongan
                                </a>
                            </div>
                        `;
                    }

                    // Hide skeleton and show content
                    tableSkeletonLoading.classList.add('d-none');
                    realTableContent.classList.remove('d-none');

                    // Animate table rows
                    animateTableRows();
                })
                .catch(error => {
                    console.error('❌ Error loading lamaran history:', error);

                    // Show error state
                    realTableContent.innerHTML = `
                        <div class="text-center p-5">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h6>Gagal Memuat Data</h6>
                            <p class="text-muted">Terjadi kesalahan saat memuat riwayat lamaran.</p>
                            <button class="btn btn-primary btn-sm mt-2" onclick="loadLamaranHistoryData()">
                                <i class="fas fa-sync me-2"></i>Coba Lagi
                            </button>
                        </div>
                    `;

                    // Hide skeleton and show content
                    tableSkeletonLoading.classList.add('d-none');
                    realTableContent.classList.remove('d-none');
                });
        }

        // 2. ✅ API CONFIGURATION
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            withCredentials: true,
            timeout: 10000
        });

        // 3. ✅ API INTERCEPTORS
        api.interceptors.request.use(
            config => {
                console.log('📤 API Request:', {
                    method: config.method?.toUpperCase(),
                    url: config.url,
                    data: config.data
                });
                return config;
            },
            error => {
                console.error('❌ Request Error:', error);
                return Promise.reject(error);
            }
        );

        api.interceptors.response.use(
            response => {
                console.log('📥 API Response:', {
                    status: response.status,
                    url: response.config.url,
                    success: response.data?.success
                });
                return response;
            },
            error => {
                console.error('❌ Response Error:', {
                    status: error.response?.status,
                    url: error.config?.url,
                    message: error.message
                });
                return Promise.reject(error);
            }
        );

        // 4. ✅ HELPER FUNCTIONS
        function getStatusBadgeHTML(status) {
            const badges = {
                'menunggu': '<span class="badge bg-warning text-dark px-3 py-2"><i class="fas fa-clock me-1"></i>Menunggu Konfirmasi</span>',
                'diterima': '<span class="badge bg-success px-3 py-2"><i class="fas fa-check me-1"></i>Diterima</span>',
                'ditolak': '<span class="badge bg-danger px-3 py-2"><i class="fas fa-times me-1"></i>Ditolak</span>'
            };
            return badges[status] || '<span class="badge bg-secondary px-3 py-2">Status Tidak Diketahui</span>';
        }

        function getLamaranLogoHTML(lamaran) {
            if (lamaran.logo_url && lamaran.logo_url !== '') {
                return `<img src="${lamaran.logo_url}" 
                     class="avatar avatar-xl rounded-circle border" 
                     alt="Logo ${lamaran.nama_perusahaan}"
                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\\'avatar avatar-xl bg-gradient-secondary rounded-circle d-flex align-items-center justify-content-center\\'>'+
                             '<i class=\\'fas fa-building text-white text-lg\\'></i></div>';">`;
            } else {
                return `<div class="avatar avatar-xl bg-gradient-secondary rounded-circle d-flex align-items-center justify-content-center">
                    <i class="fas fa-building text-white text-lg"></i>
                </div>`;
            }
        }

        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('id-ID', options);
        }

        function getRelativeTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 1) return 'Kemarin';
            if (diffDays < 7) return `${diffDays} hari lalu`;
            if (diffDays < 30) return `${Math.ceil(diffDays / 7)} minggu lalu`;
            return `${Math.ceil(diffDays / 30)} bulan lalu`;
        }

        function getStatusText(status) {
            const statusTexts = {
                'all': 'Semua Status',
                'menunggu': 'Menunggu',
                'diterima': 'Diterima',
                'ditolak': 'Ditolak'
            };
            return statusTexts[status] || status;
        }

        // 5. ✅ ANIMATION FUNCTIONS
        function animateCounter(element) {
            const target = parseInt(element.dataset.target) || 0;
            const duration = 1200;
            const startTime = performance.now();

            function updateCounter(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(easeOut * target);

                element.textContent = current;

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        element.style.transition = 'transform 0.2s ease';
                        element.style.transform = 'scale(1)';
                    }, 100);
                }
            }

            requestAnimationFrame(updateCounter);
        }

        function animateTableRows() {
            const rows = document.querySelectorAll('.fade-in-row');

            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-30px)';

                setTimeout(() => {
                    row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, index * 100);
            });
        }

        function initializeTooltips() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        }

        // 6. ✅ LOADING FUNCTIONS
        function simulateContentLoading() {
            console.log('⏳ Starting content loading simulation...');

            setTimeout(() => loadStatsCard(1), 300);
            setTimeout(() => loadStatsCard(2), 600);
            setTimeout(() => loadStatsCard(3), 900);
            setTimeout(() => loadStatsCard(4), 1200);

            // ✅ FIX: Show magang card for BOTH aktif AND selesai status
            if (serverData.magangInfo && serverData.magangData) {
                console.log(`🏢 Loading magang card with status: ${serverData.magangData.status}`);

                // First check if evaluasi already exists
                checkEvaluationExists(serverData.magangData.id_magang);
            } else {
                console.log('ℹ️ No magang data - showing lamaran history only');
            }

            setTimeout(() => loadTableContent(), 2000);
        }

        function loadStatsCard(cardNumber) {
            const skeleton = document.getElementById('skeleton-stats-' + cardNumber);
            const realContent = document.getElementById('real-stats-' + cardNumber);

            if (!skeleton || !realContent) return;

            skeleton.style.transition = 'opacity 0.3s ease';
            skeleton.style.opacity = '0';

            setTimeout(() => {
                skeleton.classList.add('d-none');
                realContent.classList.remove('d-none');

                realContent.style.opacity = '0';
                realContent.style.transform = 'translateY(20px)';
                realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';

                setTimeout(() => {
                    realContent.style.opacity = '1';
                    realContent.style.transform = 'translateY(0)';

                    const counter = realContent.querySelector('.counter-number');
                    if (counter) {
                        setTimeout(() => animateCounter(counter), 200);
                    }
                }, 50);
            }, 300);
        }

        function loadMagangCard() {
            console.log('🏢 Loading magang card...');

            const skeleton = document.getElementById('magang-skeleton');
            const realContent = document.getElementById('real-magang');

            if (!skeleton || !realContent) {
                console.error('❌ Magang card elements not found:', {
                    skeleton: !!skeleton,
                    realContent: !!realContent
                });
                return;
            }

            // Hide skeleton
            skeleton.style.transition = 'opacity 0.4s ease';
            skeleton.style.opacity = '0';
            setTimeout(() => {
                skeleton.classList.add('d-none');

                // Show real content
                console.log('✅ Showing real magang content');
                realContent.classList.remove('d-none');
                realContent.style.opacity = '0';
                realContent.style.transform = 'translateY(30px)';
                realContent.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';

                setTimeout(() => {
                    realContent.style.opacity = '1';
                    realContent.style.transform = 'translateY(0)';
                    console.log('✅ Magang card loaded successfully');
                }, 50);
            }, 400);
        }

        function loadTableContent() {
            const skeleton = document.getElementById('table-skeleton-loading');
            const realContent = document.getElementById('real-table-content');

            if (!skeleton || !realContent) return;

            skeleton.style.transition = 'opacity 0.4s ease';
            skeleton.style.opacity = '0';

            setTimeout(() => {
                skeleton.classList.add('d-none');
                realContent.classList.remove('d-none');

                realContent.style.opacity = '0';
                realContent.style.transform = 'translateY(20px)';
                realContent.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';

                setTimeout(() => {
                    realContent.style.opacity = '1';
                    realContent.style.transform = 'translateY(0)';

                    animateTableRows();
                    initializeTooltips();
                }, 50);
            }, 400);
        }

        // 7. ✅ DOM READY
        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 === LOADING CONTENT AND TABLES ===');
            simulateContentLoading();
        });

        // 8. ✅ MAIN FUNCTIONS (detailLamaran, cancelLamaran, dll...)
        function detailLamaran(id) {
            console.log('👀 Opening detail for lamaran ID:', id);

            // Show loading modal
            Swal.fire({
                title: 'Memuat Detail...',
                html: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"></div></div>',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get detail data via AJAX
            api.get(`/mahasiswa/lamaran/${id}/detail`)
                .then(response => {
                    console.log('✅ Detail lamaran response:', response.data);

                    if (response.data?.success) {
                        const lamaran = response.data.data;
                        showDetailModal(lamaran);
                    } else {
                        throw new Error(response.data?.message || 'Data tidak ditemukan');
                    }
                })
                .catch(error => {
                    console.error('❌ Error loading detail:', error);

                    let errorMessage = 'Gagal memuat detail lamaran';
                    if (error.response?.status === 404) {
                        errorMessage = 'Data lamaran tidak ditemukan';
                    } else if (error.response?.data?.message) {
                        errorMessage = error.response.data.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Detail',
                        text: errorMessage,
                        confirmButtonText: 'OK'
                    });
                });
        }

        // ✅ NEW: Function untuk menampilkan modal detail yang menarik
        function showDetailModal(lamaran) {
            const statusBadge = getStatusBadgeHTML(lamaran.status);
            const logoHTML = getLamaranLogoHTML(lamaran);

            const detailHTML = `
        <div class="modal-detail-lamaran">
            <!-- Header Section -->
            <div class="detail-header text-center mb-4">
                <div class="company-logo-large mb-3">
                    ${logoHTML}
                </div>
                <h4 class="mb-2 text-dark font-weight-bold">${lamaran.judul_lowongan}</h4>
                <h6 class="mb-2 text-muted">${lamaran.nama_perusahaan}</h6>
                ${lamaran.nama_kota ? `<p class="text-sm text-secondary mb-3"><i class="fas fa-map-marker-alt me-1"></i>${lamaran.nama_kota}</p>` : ''}
                ${statusBadge}
            </div>

            <!-- Info Cards -->
            <div class="row mb-4">
                <div class="col-6">
                    <div class="info-card bg-light p-3 rounded">
                        <div class="d-flex align-items-center">
                            <div class="info-icon bg-primary text-white rounded-circle me-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Tanggal Lamaran</small>
                                <strong>${formatDate(lamaran.tanggal_lamaran)}</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="info-card bg-light p-3 rounded">
                        <div class="d-flex align-items-center">
                            <div class="info-icon bg-success text-white rounded-circle me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Kapasitas</small>
                                <strong>${lamaran.kapasitas || 'Tidak terbatas'}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Section -->
            ${lamaran.deskripsi_lowongan ? `
                                                                                                                                                                    <div class="description-section mb-4">
                                                                                                                                                                        <h6 class="mb-2"><i class="fas fa-file-alt me-2"></i>Deskripsi Posisi</h6>
                                                                                                                                                                        <div class="description-content bg-light p-3 rounded">
                                                                                                                                                                            <p class="mb-0 text-sm">${lamaran.deskripsi_lowongan}</p>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                ` : ''}

            <!-- Requirements Section -->
            ${lamaran.min_ipk ? `
                                                                                                                                                                    <div class="requirements-section mb-4">
                                                                                                                                                                        <h6 class="mb-2"><i class="fas fa-check-circle me-2"></i>Persyaratan</h6>
                                                                                                                                                                        <div class="bg-light p-3 rounded">
                                                                                                                                                                            <div class="d-flex align-items-center">
                                                                                                                                                                                <i class="fas fa-graduation-cap text-primary me-2"></i>
                                                                                                                                                                                <span class="text-sm">Minimal IPK: <strong>${lamaran.min_ipk}</strong></span>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div>
                                                                                                                                                                    </div>
                                                                                                                                                                ` : ''}

            <!-- Company Info -->
            <div class="company-info-section">
                <h6 class="mb-2"><i class="fas fa-building me-2"></i>Informasi Perusahaan</h6>
                <div class="bg-light p-3 rounded">
                    ${lamaran.alamat_perusahaan ? `
                                                                                                                                                                            <div class="d-flex align-items-start mb-2">
                                                                                                                                                                                <i class="fas fa-map-marker-alt text-primary me-2 mt-1"></i>
                                                                                                                                                                                <span class="text-sm">${lamaran.alamat_perusahaan}</span>
                                                                                                                                                                            </div>
                                                                                                                                                                        ` : ''}
                    ${lamaran.perusahaan_email ? `
                                                                                                                                                                            <div class="d-flex align-items-center mb-2">
                                                                                                                                                                                <i class="fas fa-envelope text-primary me-2"></i>
                                                                                                                                                                                <a href="mailto:${lamaran.perusahaan_email}" class="text-sm text-decoration-none">${lamaran.perusahaan_email}</a>
                                                                                                                                                                            </div>
                                                                                                                                                                        ` : ''}
                    ${lamaran.website ? `
                                                                                                                                                                            <div class="d-flex align-items-center">
                                                                                                                                                                                <i class="fas fa-globe text-primary me-2"></i>
                                                                                                                                                                                <a href="${lamaran.website}" target="_blank" class="text-sm text-decoration-none">${lamaran.website}</a>
                                                                                                                                                                            </div>
                                                                                                                                                                        ` : ''}
                </div>
            </div>
        </div>
    `;

            // Show detail modal dengan action buttons
            const modalButtons = {};

            // Add cancel button if status is 'menunggu'
            if (lamaran.status === 'menunggu') {
                modalButtons['Batalkan Lamaran'] = {
                    text: 'Batalkan Lamaran',
                    value: 'cancel',
                    className: 'btn btn-danger btn-sm'
                };
            }

            modalButtons['Tutup'] = {
                text: 'Tutup',
                value: 'close',
                className: 'btn btn-secondary btn-sm'
            };

            Swal.fire({
                title: 'Detail Lamaran',
                html: detailHTML,
                showCancelButton: false,
                showConfirmButton: false,
                width: '600px',
                customClass: {
                    popup: 'detail-lamaran-modal',
                    htmlContainer: 'detail-content-wrapper'
                },
                footer: generateModalFooter(lamaran),
                allowOutsideClick: true
            });
        }

        // ✅ ENHANCED: Generate modal footer dengan action buttons
        function generateModalFooter(lamaran) {
            let footerHTML = '<div class="modal-footer-actions d-flex justify-content-between align-items-center w-100">';

            // Left side - Close button
            footerHTML += '<button type="button" class="btn btn-secondary btn-sm" onclick="Swal.close()">Tutup</button>';

            // Right side - Action buttons
            footerHTML += '<div class="action-buttons">';

            if (lamaran.status === 'menunggu') {
                footerHTML += `<button type="button" class="btn btn-danger btn-sm me-2" onclick="cancelLamaran(${lamaran.id_lamaran})">
            <i class="fas fa-times me-1"></i>Batalkan
        </button>`;
            }

            footerHTML += '</div></div>';

            return footerHTML;
        }

        // ✅ ENHANCED: Cancel lamaran dengan AJAX refresh yang benar
        function cancelLamaran(id) {
            console.log('🗑️ Canceling lamaran ID:', id);

            Swal.fire({
                title: 'Batalkan Lamaran?',
                html: `
            <div class="text-center">
                <div class="mb-3">
                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                </div>
                <p class="mb-2">Apakah Anda yakin ingin membatalkan lamaran ini?</p>
                <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan Anda harus melamar ulang jika berubah pikiran.</small>
            </div>
        `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check me-1"></i>Ya, Batalkan',
                cancelButtonText: '<i class="fas fa-times me-1"></i>Tidak',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-danger btn-sm me-2',
                    cancelButton: 'btn btn-secondary btn-sm'
                },
                buttonsStyling: false,
                reverseButtons: true,
                focusCancel: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Show processing modal
                    Swal.fire({
                        title: 'Membatalkan Lamaran...',
                        html: `
                    <div class="d-flex flex-column align-items-center">
                        <div class="spinner-border text-danger mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0 text-muted">Sedang memproses pembatalan lamaran</p>
                    </div>
                `,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX Request untuk cancel
                    api.delete(`/mahasiswa/lamaran/${id}/cancel`)
                        .then(response => {
                            console.log('✅ Cancel success:', response.data);

                            if (response.data?.success) {
                                // Success modal
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    html: `
                                <div class="text-center">
                                    <p class="mb-2">Lamaran telah berhasil dibatalkan</p>
                                    <small class="text-muted">Data akan diperbarui secara otomatis</small>
                                </div>
                            `,
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#28a745',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    customClass: {
                                        confirmButton: 'btn btn-success btn-sm'
                                    },
                                }).then(() => {
                                    // Refresh table data
                                    loadTableContent();
                                });
                            } else {
                                throw new Error(response.data?.message || 'Gagal membatalkan lamaran');
                            }
                        })
                        .catch(error => {
                            console.error('❌ Error canceling lamaran:', error);

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat membatalkan lamaran',
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }


        // ✅ UPDATE: Function submitEvaluasi untuk auto-refresh
        function setupEvaluasiSubmission(idMagang) {
            const form = document.getElementById(`evaluasi-form-${idMagang}`);

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Show loading state
                    const submitBtn = document.getElementById(`submit-btn-${idMagang}`);
                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

                    // Create FormData object to handle file uploads
                    const formData = new FormData(form);

                    // Send the data using fetch API
                    fetch('/api/evaluasi-magang/submit', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw new Error(JSON.stringify(errorData));
                                }).catch(e => {
                                    throw new Error('Server returned an error: ' + response.status);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            // Handle success
                            console.log("Success response:", data);

                            // Show success notification
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Evaluasi magang berhasil disubmit',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Reload the page - this will now show only the history section
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            // Handle error
                            console.error('Error details:', error);

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat mengirim evaluasi. Silakan coba lagi.',
                                confirmButtonColor: '#dc3545'
                            });
                        })
                        .finally(() => {
                            // Reset button state
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        });
                });
            } else {
                console.error(`Form with ID evaluasi-form-${idMagang} not found`);
            }
        }
        // Di bagian function refreshLamaranData()
        function refreshLamaranData() {
            console.log('🔄 Refreshing lamaran data...');

            // Animate refresh icon
            const refreshIcon = document.getElementById('refreshIcon');
            if (refreshIcon) {
                refreshIcon.classList.add('fa-spin');
            }

            // Show table skeleton
            const tableSkeletonLoading = document.getElementById('table-skeleton-loading');
            const realTableContent = document.getElementById('real-table-content');

            if (tableSkeletonLoading && realTableContent) {
                realTableContent.classList.add('d-none');
                tableSkeletonLoading.classList.remove('d-none');
            }

            // Fetch lamaran data from API
            api.get('/mahasiswa/lamaran/data')
                .then(response => {
                    console.log('✅ Lamaran data refreshed:', response.data);

                    if (response.data.success) {
                        const lamaranData = response.data.lamaranHistory || [];

                        // Update table data
                        updateTableData(lamaranData);

                        // ✅ FIX: Update statistics counter
                        if (response.data.statistik) {
                            updateStatsCounters(response.data.statistik);
                        }

                        // ✅ FIX: Make sure menunggu badges are shown
                        updateLamaranStatusBadges();
                    } else {
                        throw new Error(response.data.message || 'Failed to refresh data');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: 'Terjadi kesalahan saat memuat data lamaran.',
                        confirmButtonText: 'Coba Lagi'
                    });
                })
                .finally(() => {
                    // Stop refresh icon animation
                    if (refreshIcon) {
                        refreshIcon.classList.remove('fa-spin');
                    }

                    // Hide skeleton and show real content
                    if (tableSkeletonLoading && realTableContent) {
                        tableSkeletonLoading.classList.add('d-none');
                        realTableContent.classList.remove('d-none');
                    }
                });
        }

        // Tambahkan fungsi untuk pre-cache status lamaran
        function preLoadLamaranStatus() {
            // Ambil semua id lowongan dari halaman
            const lowonganCards = document.querySelectorAll('[data-lowongan-id]');
            const lowonganIds = Array.from(lowonganCards).map(card => card.dataset.lowonganId);

            if (lowonganIds.length === 0) return;

            console.log('🔄 Pre-loading application status for lowongan IDs:', lowonganIds);

            // Buat satu request untuk mengambil status semua lowongan
            fetch('/api/mahasiswa/lamaran/batch-status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        ids: lowonganIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cache status di sessionStorage
                        sessionStorage.setItem('lamaranStatus', JSON.stringify(data.status));
                        console.log('✅ Cached application status:', data.status);

                        // Update UI langsung
                        updateAllLamaranButtons();
                    }
                })
                .catch(err => console.error('❌ Error pre-loading application status:', err));
        }

        // Tambahkan fungsi untuk update semua tombol lamaran
        function updateAllLamaranButtons() {
            const statusCache = JSON.parse(sessionStorage.getItem('lamaranStatus') || '{}');

            document.querySelectorAll('[data-lowongan-id]').forEach(card => {
                const lowonganId = card.dataset.lowonganId;
                const applyButton = card.querySelector('.apply-button');

                if (!applyButton) return;

                if (statusCache[lowonganId]) {
                    const status = statusCache[lowonganId].status;
                    updateButtonStatus(applyButton, status);
                }
            });
        }

        // Update fungsi checkApplicationStatus
        function checkApplicationStatus(lowonganId, buttonElement) {
            // Coba ambil dari cache dulu
            const statusCache = JSON.parse(sessionStorage.getItem('lamaranStatus') || '{}');

            if (statusCache[lowonganId]) {
                console.log('📋 Using cached status for lowongan', lowonganId, statusCache[lowonganId]);
                updateButtonStatus(buttonElement, statusCache[lowonganId].status);
                return; // Gunakan cache, jangan request API
            }

            // Jika tidak ada di cache, baru request API
            fetch(`/api/mahasiswa/lamaran/check/${lowonganId}`)
                .then(response => response.json())
                .then(data => {
                    console.log('📋 Application status for lowongan', lowonganId, data);

                    if (data.success) {
                        // Update cache
                        const statusCache = JSON.parse(sessionStorage.getItem('lamaranStatus') || '{}');
                        statusCache[lowonganId] = {
                            status: data.applied ? data.status : 'available',
                            lastChecked: new Date().toISOString()
                        };
                        sessionStorage.setItem('lamaranStatus', JSON.stringify(statusCache));

                        // Update UI
                        updateButtonStatus(buttonElement, data.applied ? data.status : 'available');
                    }
                })
                .catch(err => console.error('❌ Error checking application status:', err));
        }

        // Tambahkan fungsi untuk dipanggil saat document loaded
        document.addEventListener('DOMContentLoaded', function() {
            preLoadLamaranStatus();
        });
    </script>
@endpush
