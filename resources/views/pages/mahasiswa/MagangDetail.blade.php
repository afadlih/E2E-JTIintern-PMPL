<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\mahasiswa\magang-detail.blade.php -->

@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid px-10">
        @if(isset($magangInfo) && $magangInfo)
            <!-- Detail Magang Content -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="mb-0">Detail Magang</h3>
                        @if(isset($magangInfo['status_text']))
                            @if($magangInfo['status_text'] === 'Sedang berlangsung')
                                <div class="badge badge-magang">
                                    <i class="bi bi-play-circle me-1"></i>Magang Aktif
                                </div>
                            @elseif(str_contains($magangInfo['status_text'], 'Akan dimulai'))
                                <div class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i>{{ $magangInfo['status_text'] }}
                                </div>
                            @elseif($magangInfo['status_text'] === 'Magang telah selesai')
                                <div class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Selesai
                                </div>
                            @else
                                <div class="badge bg-secondary">
                                    <i class="bi bi-calendar-x me-1"></i>Belum Terjadwal
                                </div>
                            @endif
                        @else
                            <div class="badge badge-magang">Magang Aktif</div>
                        @endif
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <!-- Header Section -->
                            <div class="d-flex align-items-center mb-4">
                                @if($magangInfo['data']->logo_perusahaan)
                                    <img src="{{ asset('storage/' . $magangInfo['data']->logo_perusahaan) }}"
                                        alt="{{ $magangInfo['data']->nama_perusahaan }}" class="company-logo-lg me-4">
                                @else
                                    <div class="company-logo-placeholder me-4">
                                        {{ substr($magangInfo['data']->nama_perusahaan, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <h4 class="mb-1">{{ $magangInfo['data']->judul_lowongan }}</h4>
                                    <h5 class="mb-3 text-primary">{{ $magangInfo['data']->nama_perusahaan }}</h5>
                                    <div class="d-flex align-items-center">
                                        @if(isset($magangInfo['data']->tgl_mulai) && isset($magangInfo['data']->tgl_selesai))
                                            <span class="me-3">
                                                <i class="bi bi-calendar-check me-2"></i>
                                                {{ \Carbon\Carbon::parse($magangInfo['data']->tgl_mulai)->format('d M Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($magangInfo['data']->tgl_selesai)->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="me-3">
                                                <i class="bi bi-calendar-x me-2"></i>
                                                <span class="text-muted">Jadwal belum ditentukan</span>
                                            </span>
                                        @endif
                                        
                                        <div class="progress-container">
                                            <div class="progress" style="width: 120px; height: 8px;">
                                                <div class="progress-bar" style="width: {{ $magangInfo['progress'] ?? 0 }}%"></div>
                                            </div>
                                            <span class="progress-text">{{ $magangInfo['progress'] ?? 0 }}% selesai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Info Cards -->
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <div class="detail-card">
                                        <div class="detail-card-icon">
                                            <i class="bi bi-person-workspace"></i>
                                        </div>
                                        <div>
                                            <h6>Pembimbing</h6>
                                            <p class="mb-0">
                                                {{ $magangInfo['data']->nama_pembimbing ?? 'Belum ditentukan' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-card">
                                        <div class="detail-card-icon">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <div>
                                            @if(isset($magangInfo['status_text']) && str_contains($magangInfo['status_text'], 'Akan dimulai'))
                                                <h6>Dimulai Dalam</h6>
                                                <p class="mb-0">{{ $magangInfo['sisaHari'] ?? 0 }} Hari</p>
                                            @elseif(isset($magangInfo['status_text']) && $magangInfo['status_text'] === 'Magang telah selesai')
                                                <h6>Status</h6>
                                                <p class="mb-0 text-success">Selesai</p>
                                            @else
                                                <h6>Sisa Waktu</h6>
                                                <p class="mb-0">{{ $magangInfo['sisaHari'] ?? 0 }} Hari</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-card">
                                        <div class="detail-card-icon">
                                            <i class="bi bi-calendar-week"></i>
                                        </div>
                                        <div>
                                            <h6>Durasi Total</h6>
                                            <p class="mb-0">
                                                @if(isset($magangInfo['totalDurasi']) && $magangInfo['totalDurasi'] > 0)
                                                    {{ $magangInfo['totalDurasi'] }} Hari 
                                                    <small class="text-muted">({{ round($magangInfo['totalDurasi'] / 30, 1) }} bulan)</small>
                                                @else
                                                    Belum ditentukan
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ganti seluruh bagian tabs -->
                            <div class="action-buttons mt-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <a href="{{ route('mahasiswa.logaktivitas') }}"
                                            class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                            <i class="bi bi-journal-text me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-bold">Log Kegiatan</div>
                                                <small class="text-muted">Catat aktivitas harian</small>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-md-4">
                                        <button
                                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center"
                                            disabled>
                                            <i class="bi bi-list-task me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-bold">Tugas</div>
                                                <small class="text-muted">Segera hadir</small>
                                            </div>
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button
                                            class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center"
                                            disabled>
                                            <i class="bi bi-journal-plus me-2"></i>
                                            <div class="text-start">
                                                <div class="fw-bold">Logbook</div>
                                                <small class="text-muted">Segera hadir</small>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Active Internship -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body p-5 text-center">
                            <div class="empty-state-icon mb-4">
                                <i class="bi bi-clipboard-x"></i>
                            </div>
                            <h4>Tidak Ada Magang Aktif</h4>
                            <p class="text-muted mb-4">Anda belum terdaftar dalam program magang aktif saat ini.</p>
                            <a href="{{ route('mahasiswa.lowongan') }}" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>Cari Lowongan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('css')
    <style>
        /* Styling lanjutan dari dashboard */
        .company-logo-lg {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: contain;
            background: #f8f9fe;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            padding: 12px;
        }

        .company-logo-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            background: linear-gradient(135deg, #96B3FF, #5e72e4);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .badge-magang {
            background: linear-gradient(135deg, #96B3FF, #5e72e4);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.75rem;
            box-shadow: 0 3px 6px rgba(94, 114, 228, 0.15);
        }

        .progress-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 30px;
            overflow: hidden;
        }

        .progress-bar {
            background: linear-gradient(135deg, #96B3FF, #5e72e4);
            border-radius: 30px;
        }

        .progress-text {
            font-size: 0.8rem;
            color: #5e72e4;
            font-weight: 600;
        }

        .detail-card {
            display: flex;
            align-items: center;
            background: #f8f9fe;
            padding: 1.25rem;
            border-radius: 12px;
            height: 100%;
        }

        .detail-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #5e72e4;
            margin-right: 1rem;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05);
        }

        .detail-card h6 {
            color: #8898aa;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .detail-card p {
            color: #344767;
            font-weight: 600;
            font-size: 1rem;
        }

        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #8898aa;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            margin-right: 1rem;
        }

        .nav-tabs .nav-link.active {
            color: #5e72e4;
            border-bottom: 2px solid #5e72e4;
            background: transparent;
        }

        .tab-content {
            background: #f8f9fe;
            border-radius: 0 0 10px 10px;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f8f9fe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #8898aa;
            margin: 0 auto;
        }
    </style>
@endpush