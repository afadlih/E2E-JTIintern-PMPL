@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.mahasiswa.topnav')

    <div class="container-fluid px-10 py-4">
        <!-- Profile Header -->
        <div class="card mb-4 bg-gradient-primary border-0">
            <div class="card-body p-4">
                <!-- Header Skeleton -->
                <div id="header-skeleton" class="profile-header-skeleton">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="skeleton-avatar-container">
                                <div class="skeleton-avatar-large"></div>
                                <div class="skeleton-avatar-edit"></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="skeleton-text-white skeleton-text-lg mb-2"></div>
                            <div class="skeleton-text-white skeleton-text-md"></div>
                        </div>
                        <div class="col-auto">
                            <div class="skeleton-button"></div>
                        </div>
                    </div>
                </div>

                <!-- Real Header Content -->
                <div id="header-content" class="real-header-content d-none">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="avatar-container">
                                <div id="current-avatar" class="avatar-profile"
                                    style="{{ $mahasiswaData && isset($mahasiswaData->foto) && $mahasiswaData->foto ? 'background-image: url(' . asset('storage/' . $mahasiswaData->foto) . ')' : '' }}">
                                    @if (!$mahasiswaData || !isset($mahasiswaData->foto) || !$mahasiswaData->foto)
                                        <span>{{ $userData ? substr($userData->name, 0, 1) : 'M' }}</span>
                                    @endif
                                </div>
                                <div class="avatar-edit" id="upload-trigger">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <form id="avatar-form" style="display:none">
                                    <input type="file" id="avatar-upload" name="foto" accept="image/*">
                                </form>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="text-white mb-1">{{ $userData->name ?? 'Nama Mahasiswa' }}</h4>
                            <p class="text-white-50 mb-0">{{ $userData->email ?? 'mahasiswa@email.com' }}</p>
                        </div>
                        <div class="col-auto">
                            <button id="edit-profile-btn" class="btn btn-sm btn-light">
                                <i class="fas fa-pencil-alt me-2"></i>Edit Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Completion Alert -->
        @if (isset($profileCompletion) && !$profileCompletion['is_complete'])
            <div class="row mb-4">
                <div class="col-12">
                    <div class="profile-incomplete-card">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="warning-icon me-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h6 style = "color : white;" class="mb-1 fw-bold">Profil Belum Lengkap</h6>
                                    <p class="mb-0 text-sm">Lengkapi profil untuk mendapatkan rekomendasi yang lebih akurat.
                                    </p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
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

        <div class="row">
            <!-- Personal Information Container - Left Side -->
            <div class="col-12 col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header p-3">
                        <!-- Info Header Skeleton -->
                        <div id="info-header-skeleton" class="info-header-skeleton">
                            <div class="skeleton-text-md mb-0"></div>
                        </div>

                        <!-- Real Info Header -->
                        <div id="info-header-content" class="real-info-header d-none">
                            <h6 class="mb-0">Informasi Mahasiswa</h6>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <!-- Profile Info Skeleton -->
                        <div id="profile-info-skeleton" class="profile-info-skeleton">
                            <div class="row mb-4">
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <div class="skeleton-info-item mb-3">
                                            <div class="skeleton-label mb-1"></div>
                                            <div class="skeleton-text-md"></div>
                                        </div>
                                    @endfor
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <div class="skeleton-info-item mb-3">
                                            <div class="skeleton-label mb-1"></div>
                                            <div class="skeleton-text-md"></div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    <div class="skeleton-text-lg mb-2"></div>
                                    <div class="skeleton-text-md"></div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="skeleton-section-title mb-3"></div>
                                    <div class="skeleton-text-md"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Profile Content -->
                        <div id="profile-info-content" class="real-profile-content d-none">
                            <!-- View Mode -->
                            <div id="profile-view-mode">
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Pribadi</h6>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">Nama Lengkap</label>
                                            <p class="mb-0" data-profile="name">
                                                {{ $userData->name ?? 'Belum ada data nama' }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">Email</label>
                                            <p class="mb-0" data-profile="email">
                                                {{ $userData->email ?? 'Belum ada data email' }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">NIM</label>
                                            <p class="mb-0" data-profile="nim">
                                                {{ $mahasiswaData->nim ?? 'Belum ada data NIM' }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">No. HP/WhatsApp</label>
                                            <p class="mb-0" data-profile="no_hp">
                                                {{ $mahasiswaData->telp ?? 'Belum ada data nomor HP' }}</p>
                                        </div>

                                        <!-- CV Section in View Mode -->
                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">Curriculum Vitae
                                                (CV)</label>
                                            @if (isset($mahasiswaData->cv) && $mahasiswaData->cv)
                                                <div class="cv-view-container">
                                                    <div class="p-3 border rounded bg-light">
                                                        <div class="d-flex align-items-center">
                                                            <div class="cv-icon me-3">
                                                                <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                                            </div>
                                                            <div class="cv-info flex-grow-1">
                                                                <h6 class="mb-1">CV Saat Ini</h6>
                                                                <p class="text-muted mb-0 small">
                                                                    @if ($mahasiswaData->cv_updated_at)
                                                                        Terakhir diperbarui:
                                                                        {{ \Carbon\Carbon::parse($mahasiswaData->cv_updated_at)->format('d M Y H:i') }}
                                                                    @else
                                                                        CV telah diupload
                                                                    @endif
                                                                </p>
                                                            </div>
                                                            <div class="cv-actions">
                                                                <a href="{{ asset('storage/' . $mahasiswaData->cv) }}"
                                                                    class="btn btn-sm btn-primary" target="_blank">
                                                                    <i class="fas fa-eye me-1"></i>Lihat CV
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="mb-0 text-muted">Belum ada CV yang diupload</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-6 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Akademik</h6>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">Kelas</label>
                                            <p class="mb-0" data-profile="kelas">
                                                {{ $kelasData->nama_kelas ?? 'Belum ada data kelas' }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">Program Studi</label>
                                            <p class="mb-0" data-profile="prodi">
                                                {{ $kelasData->kode_prodi ?? 'Belum ada data prodi' }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">Tahun Masuk</label>
                                            <p class="mb-0" data-profile="tahun_masuk">
                                                {{ $kelasData->tahun_masuk ?? 'Belum ada data tahun masuk' }}</p>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-sm font-weight-bold">IPK</label>
                                            <p class="mb-0" data-profile="ipk">
                                                {{ $mahasiswaData->ipk ?? 'Belum ada data IPK' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alamat & Wilayah Info -->
                                <div class="col-12 mb-3">
                                    <h6 class="text-uppercase text-sm text-muted mb-2">Alamat & Lokasi</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-sm font-weight-bold">Alamat Lengkap</label>
                                        <p class="mb-0" data-profile="alamat">
                                            {{ $mahasiswaData->alamat ?? 'Belum ada data alamat' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-sm font-weight-bold">Preferensi Wilayah
                                            Magang</label>
                                        <p class="mb-0" data-profile="wilayah">
                                            {{ $wilayahData->nama_kota ?? 'Belum memilih preferensi wilayah' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Mode -->
                            <div id="profile-edit-mode" style="display: none;">
                                <form id="profile-form">
                                    @csrf
                                    <div class="row mb-4">
                                        <!-- Personal Info Column -->
                                        <div class="col-12 col-md-6 mb-3">
                                            <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Personal</h6>

                                            <!-- ✅ TAMBAHAN: Input Nama -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">Nama Lengkap</label>
                                                <input type="text" class="form-control" name="name" id="edit-name"
                                                    value="{{ $userData->name ?? '' }}" required>
                                            </div>

                                            <!-- ✅ TAMBAHAN: Input Email (readonly) -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">Email</label>
                                                <input type="email" class="form-control" name="email"
                                                    id="edit-email" value="{{ $userData->email ?? '' }}" readonly>
                                                <small class="text-muted">Email tidak dapat diubah</small>
                                            </div>

                                            <!-- ✅ TAMBAHAN: Input NIM (readonly) -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">NIM</label>
                                                <input type="text" class="form-control" name="nim" id="edit-nim"
                                                    value="{{ $mahasiswaData->nim ?? '' }}" readonly>
                                                <small class="text-muted">NIM tidak dapat diubah</small>
                                            </div>

                                            <!-- ✅ TAMBAHAN: Input No HP -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">No. HP/WhatsApp</label>
                                                <input type="tel" class="form-control" name="telp" id="edit-telp"
                                                    value="{{ $mahasiswaData->telp ?? '' }}"
                                                    placeholder="Contoh: 08123456789">
                                            </div>

                                            <!-- ✅ CV UPLOAD SECTION - MOVED HERE -->
                                            <div class="mb-3">
                                                <h6 class="text-uppercase text-sm text-muted mb-2">Curriculum Vitae (CV)
                                                </h6>
                                                <div class="cv-upload-section">
                                                    @if (isset($mahasiswaData->cv) && $mahasiswaData->cv)
                                                        <div class="current-cv mb-3">
                                                            <div class="p-3 border rounded bg-light">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="cv-icon me-3">
                                                                        <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                                                    </div>
                                                                    <div class="cv-info flex-grow-1">
                                                                        <h6 class="mb-1">CV Saat Ini</h6>
                                                                        <p class="text-muted mb-0 small">
                                                                            Terakhir diperbarui:
                                                                            {{ \Carbon\Carbon::parse($mahasiswaData->cv_updated_at)->format('d M Y H:i') }}
                                                                        </p>
                                                                    </div>
                                                                    <div class="cv-actions">
                                                                        <a href="{{ asset('storage/' . $mahasiswaData->cv) }}"
                                                                            class="btn btn-sm btn-primary me-2"
                                                                            target="_blank">
                                                                            <i class="fas fa-eye me-1"></i>Lihat
                                                                        </a>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-danger"
                                                                            onclick="deleteCV()">
                                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Input file biasa -->
                                                    <div class="cv-input-section">
                                                        <div class="p-3 border rounded bg-light">
                                                            <input type="file" id="cv-input" class="form-control"
                                                                accept=".pdf" onchange="handleCVChange(this)">
                                                            <small class="text-muted d-block mt-2">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                Format: PDF, Maksimal: 5MB
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6 mb-3">
                                            <h6 class="text-uppercase text-sm text-muted mb-2">Informasi Akademik</h6>

                                            <!-- ✅ TAMBAHAN: Input Kelas (readonly) -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">Kelas</label>
                                                <input type="text" class="form-control" name="kelas"
                                                    id="edit-kelas"
                                                    value="{{ $kelasData->nama_kelas ?? 'Belum ada data kelas' }}"
                                                    readonly>
                                                <small class="text-muted">Kelas diatur oleh admin</small>
                                            </div>

                                            <!-- ✅ TAMBAHAN: Input Program Studi (readonly) -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">Program Studi</label>
                                                <input type="text" class="form-control" name="prodi"
                                                    id="edit-prodi"
                                                    value="{{ $kelasData->kode_prodi ?? 'Belum ada data prodi' }}"
                                                    readonly>
                                                <small class="text-muted">Program studi diatur oleh admin</small>
                                            </div>

                                            <!-- ✅ TAMBAHAN: Input Tahun Masuk (readonly) -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">Tahun Masuk</label>
                                                <input type="text" class="form-control" name="tahun_masuk"
                                                    id="edit-tahun-masuk"
                                                    value="{{ $kelasData->tahun_masuk ?? 'Belum ada data tahun masuk' }}"
                                                    readonly>
                                            </div>

                                            <!-- ✅ TAMBAHAN: Input IPK -->
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">IPK</label>
                                                <input type="number" class="form-control" name="ipk" id="edit-ipk"
                                                    value="{{ $mahasiswaData->ipk ?? '' }}" step="0.01"
                                                    min="0" max="4" placeholder="Contoh: 3.75">
                                                <small class="text-muted">IPK saat ini (0.00 - 4.00)</small>
                                            </div>
                                        </div>

                                        <!-- Alamat & Wilayah Form -->
                                        <div class="col-12 mb-4">
                                            <h6 class="text-uppercase text-sm text-muted mb-2">Alamat & Lokasi</h6>
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">Alamat Lengkap</label>
                                                <textarea class="form-control" name="alamat" id="edit-alamat" rows="3"
                                                    placeholder="Masukkan alamat lengkap Anda">{{ $mahasiswaData->alamat ?? '' }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-sm font-weight-bold">Preferensi Wilayah
                                                    Magang</label>
                                                <select class="form-select" name="wilayah_id" id="wilayah-select">
                                                    <option value="">Pilih Wilayah</option>
                                                </select>
                                                <small class="text-muted">Pilih wilayah yang Anda inginkan untuk
                                                    magang</small>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end">
                                                <button type="button" id="cancel-edit-btn"
                                                    class="btn btn-outline-secondary me-2">
                                                    <i class="fas fa-times me-1"></i>Batal
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-save me-2"></i>
                                                        <span id="save-btn-text">Simpan Perubahan</span>
                                                        <div id="save-btn-loader"
                                                            class="spinner-border spinner-border-sm ms-2 d-none"
                                                            role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side Container -->
            <div class="col-12 col-lg-4">
                <!-- Skills Card -->
                <div class="card mb-4">
                    <div class="card-header p-3">
                        <!-- Skills Header Skeleton -->
                        <div id="skills-header-skeleton" class="skills-header-skeleton">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="skeleton-text-sm"></div>
                                </div>
                                <div class="col-auto">
                                    <div class="skeleton-edit-button"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Skills Header -->
                        <div id="skills-header-content" class="real-skills-header d-none">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0">Keahlian</h6>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-link" id="edit-skills-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <!-- Skills Content Skeleton -->
                        <div id="skills-content-skeleton" class="skills-content-skeleton">
                            <div class="d-flex flex-wrap gap-2">
                                @for ($i = 1; $i <= 4; $i++)
                                    <div class="skeleton-badge skeleton-badge-{{ $i <= 2 ? 'md' : 'sm' }}"></div>
                                @endfor
                            </div>
                        </div>

                        <!-- Real Skills Content -->
                        <div id="skills-content" class="real-skills-content d-none">
                            <div id="skills-view-mode">
                                <div id="skills-container" class="d-flex flex-wrap gap-2">
                                    @if (isset($skills) && count($skills) > 0)
                                        @foreach ($skills as $skill)
                                            <span class="badge bg-light-primary">{{ $skill->nama }}</span>
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">Belum ada keahlian yang ditambahkan</p>
                                    @endif
                                </div>
                            </div>

                            <div id="skills-edit-mode" style="display: none;">
                                <form id="skills-form">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih Keahlian</label>
                                        <select class="form-select" id="skill-select" multiple></select>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="cancel-skills-btn"
                                            class="btn btn-sm btn-outline-secondary me-2">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interests Card -->
                <div class="card mb-4">
                    <div class="card-header p-3">
                        <!-- Minat Header Skeleton -->
                        <div id="minat-header-skeleton" class="minat-header-skeleton">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="skeleton-text-sm"></div>
                                </div>
                                <div class="col-auto">
                                    <div class="skeleton-edit-button"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Minat Header -->
                        <div id="minat-header-content" class="real-minat-header d-none">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0">Minat</h6>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-sm btn-link" id="edit-minat-btn">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <!-- Minat Content Skeleton -->
                        <div id="minat-content-skeleton" class="minat-content-skeleton">
                            <div class="d-flex flex-wrap gap-2">
                                @for ($i = 1; $i <= 3; $i++)
                                    <div class="skeleton-badge skeleton-badge-md"></div>
                                @endfor
                            </div>
                        </div>

                        <!-- Real Minat Content -->
                        <div id="minat-content" class="real-minat-content d-none">
                            <div id="minat-view-mode">
                                <div id="minat-container" class="d-flex flex-wrap gap-2">
                                    @if (isset($minat) && count($minat) > 0)
                                        @foreach ($minat as $item)
                                            <span class="badge bg-light-success">{{ $item->nama_minat }}</span>
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">Belum ada minat yang ditambahkan</p>
                                    @endif
                                </div>
                            </div>

                            <div id="minat-edit-mode" style="display: none;">
                                <form id="minat-form">
                                    <div class="mb-3">
                                        <label class="form-label">Pilih Minat</label>
                                        <select class="form-select" id="minat-select" multiple></select>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="cancel-minat-btn"
                                            class="btn btn-sm btn-outline-secondary me-2">Batal</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Card -->
                <div class="card">
                    <div class="card-header p-3">
                        <!-- Password Header Skeleton -->
                        <div id="password-header-skeleton" class="password-header-skeleton">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="skeleton-text-md"></div>
                                </div>
                                <div class="col-auto">
                                    <div class="skeleton-text-xs"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Real Password Header -->
                        <div id="password-header-content" class="real-password-header d-none">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="mb-0">
                                        <i class="fas fa-lock me-2"></i>Ubah Password
                                    </h6>
                                </div>
                                <div class="col-auto">
                                    <small class="text-muted">Keamanan Akun</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <!-- Password Form Skeleton -->
                        <div id="password-form-skeleton" class="password-form-skeleton">
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="mb-3">
                                    <div class="skeleton-label mb-2"></div>
                                    <div class="skeleton-input-group">
                                        <div class="skeleton-input"></div>
                                        <div class="skeleton-toggle-button"></div>
                                    </div>
                                    @if ($i == 2)
                                        <div class="skeleton-progress-section mt-2">
                                            <div class="skeleton-progress-bar mb-2"></div>
                                            <div class="skeleton-text-xs"></div>
                                        </div>
                                    @endif
                                </div>
                            @endfor

                            <div class="mb-4">
                                <div class="skeleton-button-full"></div>
                            </div>

                            <div class="skeleton-requirements">
                                <div class="skeleton-text-sm mb-2"></div>
                                @for ($i = 1; $i <= 4; $i++)
                                    <div class="skeleton-requirement-item mb-1"></div>
                                @endfor
                            </div>
                        </div>

                        <!-- Real Password Form -->
                        <div id="password-form-content" class="real-password-form d-none">
                            <form id="password-form">
                                <div class="mb-3">
                                    <label class="form-label text-sm font-weight-bold">Password Saat Ini</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current-password"
                                            name="current_password" placeholder="Masukkan password saat ini" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button"
                                            data-target="current-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-sm font-weight-bold">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new-password" name="password"
                                            placeholder="Masukkan password baru" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button"
                                            data-target="new-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="password-strength mt-2" id="password-strength">
                                        <div class="progress" style="height: 5px;">
                                            <div id="password-strength-bar" class="progress-bar" role="progressbar"
                                                style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small id="password-strength-text" class="form-text text-muted mt-1">
                                            Minimal 8 karakter dengan kombinasi huruf dan angka
                                        </small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label text-sm font-weight-bold">Konfirmasi Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm-password"
                                            name="password_confirmation" placeholder="Konfirmasi password baru" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button"
                                            data-target="confirm-password">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div id="password-match-message" class="mt-1"></div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-key me-2"></i>
                                            <span id="password-btn-text">Update Password</span>
                                            <div id="password-btn-loader"
                                                class="spinner-border spinner-border-sm ms-2 d-none" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            </form>

                            <!-- Password Requirements -->
                            <div class="mt-3 p-3 bg-light rounded">
                                <h6 class="text-sm font-weight-bold mb-2">
                                    <i class="fas fa-info-circle me-2 text-info"></i>Persyaratan Password
                                </h6>
                                <ul class="list-unstyled mb-0 text-sm">
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Minimal 8 karakter
                                    </li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Mengandung huruf
                                        besar dan kecil</li>
                                    <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Mengandung angka</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Mengandung karakter khusus (opsional)
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast Notifications -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="success-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Berhasil</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
                <div class="toast-body" id="success-message">Perubahan telah disimpan</div>
            </div>
        </div>

        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="error-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-danger text-white">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong class="me-auto">Error</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
                <div class="toast-body" id="error-message">Terjadi kesalahan</div>
            </div>
        </div>
    </div>

    <!-- Add this after the Wilayah section, before the closing div.col-12 -->
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/Mahasiswa/profile.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    @push('js')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // ✅ GLOBAL VARIABLES
            const api = axios.create({
                baseURL: '/api',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                withCredentials: true
            });

            // ✅ API interceptors
            api.interceptors.request.use(
                config => {
                    console.log('📤 API Request:', {
                        method: config.method?.toUpperCase(),
                        url: config.url,
                        data: config.data,
                        headers: config.headers
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
                        data: response.data
                    });
                    return response;
                },
                error => {
                    console.error('❌ Response Error:', {
                        status: error.response?.status,
                        url: error.config?.url,
                        data: error.response?.data,
                        message: error.message
                    });
                    return Promise.reject(error);
                }
            );

            // ✅ DOCUMENT READY HANDLER
            document.addEventListener('DOMContentLoaded', function() {
                console.log('🚀 === PROFILE PAGE LOADED ===');

                // Start progressive loading simulation
                simulateProfileLoading();

                // Initialize all functionalities after loading completes
                setTimeout(() => {
                    initPasswordToggles();
                    initPasswordStrengthMeter();
                    initProfileEdit();
                    initSkillsEdit();
                    initMinatEdit();
                    initAvatarUpload();
                    initFormSubmissions();
                    loadWilayahData();
                    initCVUpload();
                }, 3000);
            });

            // ✅ PROGRESSIVE LOADING SYSTEM
            function simulateProfileLoading() {
                console.log('⏳ Starting progressive profile loading...');

                setTimeout(() => loadHeaderSection(), 800);
                setTimeout(() => loadProfileInfoSection(), 1500);
                setTimeout(() => loadSkillsSection(), 2200);
                setTimeout(() => loadMinatSection(), 2500);
                setTimeout(() => loadPasswordSection(), 3000);
            }

            function loadHeaderSection() {
                console.log('👤 Loading header section...');
                transitionSection('header-skeleton', 'header-content', 500);
            }

            function loadProfileInfoSection() {
                console.log('📋 Loading profile info section...');

                transitionSection('info-header-skeleton', 'info-header-content', 400);

                setTimeout(() => {
                    transitionSection('profile-info-skeleton', 'profile-info-content', 500);
                }, 200);
            }

            function loadSkillsSection() {
                console.log('🎯 Loading skills section...');
                loadSectionWithStagger('skills-header-skeleton', 'skills-header-content', 'skills-content-skeleton',
                    'skills-content');
            }

            function loadMinatSection() {
                console.log('❤️ Loading minat section...');
                loadSectionWithStagger('minat-header-skeleton', 'minat-header-content', 'minat-content-skeleton',
                    'minat-content');
            }

            function loadPasswordSection() {
                console.log('🔒 Loading password section...');
                loadSectionWithStagger('password-header-skeleton', 'password-header-content', 'password-form-skeleton',
                    'password-form-content');
            }

            function loadSectionWithStagger(headerSkeletonId, headerContentId, contentSkeletonId, contentRealId) {
                transitionSection(headerSkeletonId, headerContentId, 400);

                setTimeout(() => {
                    transitionSection(contentSkeletonId, contentRealId, 500);
                }, 200);
            }

            function transitionSection(skeletonId, contentId, duration) {
                const skeleton = document.getElementById(skeletonId);
                const content = document.getElementById(contentId);

                if (!skeleton || !content) return;

                skeleton.style.transition = `opacity ${duration}ms ease`;
                skeleton.style.opacity = '0';

                setTimeout(() => {
                    skeleton.classList.add('d-none');
                    content.classList.remove('d-none');

                    setTimeout(() => {
                        content.classList.add('show');
                    }, 50);
                }, duration);
            }

            // ✅ INITIALIZE PASSWORD TOGGLES
            function initPasswordToggles() {
                document.querySelectorAll('.toggle-password').forEach(button => {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const input = document.getElementById(targetId);
                        const icon = this.querySelector('i');

                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });
                });
            }

            // ✅ INITIALIZE PASSWORD STRENGTH METER
            function initPasswordStrengthMeter() {
                const passwordInput = document.getElementById('new-password');
                const confirmInput = document.getElementById('confirm-password');
                const strengthBar = document.getElementById('password-strength-bar');
                const strengthText = document.getElementById('password-strength-text');
                const matchMessage = document.getElementById('password-match-message');

                if (!passwordInput) return;

                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    if (password.length >= 8) strength += 25;
                    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
                    if (/[0-9]/.test(password)) strength += 25;
                    if (/[^A-Za-z0-9]/.test(password)) strength += 25;

                    strengthBar.style.width = strength + '%';

                    if (strength === 0) {
                        strengthBar.className = 'progress-bar';
                        strengthText.textContent = 'Minimal 8 karakter dengan kombinasi huruf dan angka';
                        strengthText.className = 'form-text text-muted mt-1';
                    } else if (strength < 50) {
                        strengthBar.className = 'progress-bar bg-danger';
                        strengthText.textContent = 'Password lemah - perlu perbaikan';
                        strengthText.className = 'form-text text-danger mt-1';
                    } else if (strength < 75) {
                        strengthBar.className = 'progress-bar bg-warning';
                        strengthText.textContent = 'Password sedang - cukup baik';
                        strengthText.className = 'form-text text-warning mt-1';
                    } else {
                        strengthBar.className = 'progress-bar bg-success';
                        strengthText.textContent = 'Password kuat - sangat baik!';
                        strengthText.className = 'form-text text-success mt-1';
                    }

                    checkPasswordMatch();
                });

                if (confirmInput) {
                    confirmInput.addEventListener('input', checkPasswordMatch);
                }

                function checkPasswordMatch() {
                    const password = passwordInput.value;
                    const confirmPassword = confirmInput.value;

                    if (confirmPassword === '') {
                        matchMessage.innerHTML = '';
                        return;
                    }

                    if (password === confirmPassword) {
                        matchMessage.innerHTML =
                            '<small class="password-match-success"><i class="fas fa-check me-1"></i>Password cocok</small>';
                    } else {
                        matchMessage.innerHTML =
                            '<small class="password-match-error"><i class="fas fa-times me-1"></i>Password tidak cocok</small>';
                    }
                }
            }

            // ✅ INITIALIZE PROFILE EDIT
            function initProfileEdit() {
                const editBtn = document.getElementById('edit-profile-btn');
                const cancelBtn = document.getElementById('cancel-edit-btn');
                const viewMode = document.getElementById('profile-view-mode');
                const editMode = document.getElementById('profile-edit-mode');
                const form = document.getElementById('profile-form');

                if (editBtn) {
                    editBtn.addEventListener('click', () => {
                        viewMode.style.display = 'none';
                        editMode.style.display = 'block';
                        loadWilayahData();
                    });
                }

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => {
                        editMode.style.display = 'none';
                        viewMode.style.display = 'block';
                        form.reset();
                    });
                }

                if (form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();

                        const formData = {
                            name: document.getElementById('edit-name')?.value,
                            telp: document.getElementById('edit-telp')?.value,
                            ipk: document.getElementById('edit-ipk')?.value,
                            alamat: document.getElementById('edit-alamat')?.value,
                            wilayah_id: document.getElementById('wilayah-select')?.value || null
                        };

                        // Convert wilayah_id to number if exists
                        if (formData.wilayah_id) {
                            formData.wilayah_id = parseInt(formData.wilayah_id);
                        }

                        const saveBtn = document.getElementById('save-btn-text');
                        const saveLoader = document.getElementById('save-btn-loader');
                        const submitButton = form.querySelector('button[type="submit"]');

                        if (submitButton) submitButton.disabled = true;
                        if (saveBtn) saveBtn.style.opacity = '0.5';
                        if (saveLoader) saveLoader.classList.remove('d-none');

                        api.post('/mahasiswa/profile/update', formData)
                            .then(response => {
                                if (response.data?.success) {
                                    document.getElementById('profile-edit-mode').style.display = 'none';
                                    updateProfileViewData(response.data.user, response.data.mahasiswa);
                                    document.getElementById('profile-view-mode').style.display = 'block';

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Profil berhasil diperbarui',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error updating profile:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: error.response?.data?.message || 'Gagal memperbarui profil'
                                });
                            })
                            .finally(() => {
                                if (submitButton) submitButton.disabled = false;
                                if (saveBtn) saveBtn.style.opacity = '1';
                                if (saveLoader) saveLoader.classList.add('d-none');
                            });
                    });
                }
            }

            // ✅ INITIALIZE SKILLS EDIT
            function initSkillsEdit() {
                const editBtn = document.getElementById('edit-skills-btn');
                const cancelBtn = document.getElementById('cancel-skills-btn');
                const viewMode = document.getElementById('skills-view-mode');
                const editMode = document.getElementById('skills-edit-mode');

                if (editBtn) {
                    editBtn.addEventListener('click', () => {
                        console.log('🎯 Skills edit button clicked');
                        loadSkillsData();
                        viewMode.style.display = 'none';
                        editMode.style.display = 'block';
                    });
                }

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => {
                        editMode.style.display = 'none';
                        viewMode.style.display = 'block';
                        $('#skill-select').val(null).trigger('change');
                    });
                }

                // ✅ INITIALIZE SELECT2 FOR SKILLS
                $('#skill-select').select2({
                    placeholder: 'Cari dan pilih keahlian...',
                    multiple: true,
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Tidak ditemukan keahlian yang sesuai";
                        },
                        searching: function() {
                            return "Mencari keahlian...";
                        },
                        inputTooShort: function() {
                            return "Ketik minimal 1 karakter untuk mencari";
                        },
                        loadingMore: function() {
                            return "Memuat lebih banyak data...";
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    minimumInputLength: 0,
                    templateResult: function(data) {
                        if (data.loading) {
                            return 'Mencari keahlian...';
                        }

                        if (!data.id) {
                            return data.text;
                        }

                        return $('<span><i class="fas fa-tools me-2"></i>' + data.text + '</span>');
                    },
                    templateSelection: function(data) {
                        if (!data.id) {
                            return data.text;
                        }

                        return data.text;
                    }
                });
            }

            // ✅ INITIALIZE MINAT EDIT
            function initMinatEdit() {
                const editBtn = document.getElementById('edit-minat-btn');
                const cancelBtn = document.getElementById('cancel-minat-btn');
                const viewMode = document.getElementById('minat-view-mode');
                const editMode = document.getElementById('minat-edit-mode');

                if (editBtn) {
                    editBtn.addEventListener('click', () => {
                        console.log('❤️ Minat edit button clicked');
                        loadMinatData();
                        viewMode.style.display = 'none';
                        editMode.style.display = 'block';
                    });
                }

                if (cancelBtn) {
                    cancelBtn.addEventListener('click', () => {
                        editMode.style.display = 'none';
                        viewMode.style.display = 'block';
                        $('#minat-select').val(null).trigger('change');
                    });
                }

                // ✅ INITIALIZE SELECT2 FOR MINAT
                $('#minat-select').select2({
                    placeholder: 'Cari dan pilih minat...',
                    multiple: true,
                    allowClear: true,
                    width: '100%',
                    language: {
                        noResults: function() {
                            return "Tidak ditemukan minat yang sesuai";
                        },
                        searching: function() {
                            return "Mencari minat...";
                        },
                        inputTooShort: function() {
                            return "Ketik minimal 1 karakter untuk mencari";
                        },
                        loadingMore: function() {
                            return "Memuat lebih banyak data...";
                        }
                    },
                    escapeMarkup: function(markup) {
                        return markup;
                    },
                    minimumInputLength: 0,
                    templateResult: function(data) {
                        if (data.loading) {
                            return 'Mencari minat...';
                        }

                        if (!data.id) {
                            return data.text;
                        }

                        return $('<span><i class="fas fa-heart me-2"></i>' + data.text + '</span>');
                    },
                    templateSelection: function(data) {
                        if (!data.id) {
                            return data.text;
                        }

                        return data.text;
                    }
                });
            }

            // ✅ INITIALIZE AVATAR UPLOAD
            function initAvatarUpload() {
                const uploadTrigger = document.getElementById('upload-trigger');
                const avatarUpload = document.getElementById('avatar-upload');

                if (uploadTrigger && avatarUpload) {
                    uploadTrigger.addEventListener('click', () => avatarUpload.click());

                    avatarUpload.addEventListener('change', function(event) {
                        if (this.files && this.files[0]) {
                            const file = this.files[0];

                            if (!file.type.startsWith('image/')) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'File Invalid!',
                                    text: 'Silakan pilih file gambar (JPG, PNG, GIF, dll.)'
                                });
                                return;
                            }

                            if (file.size > 5 * 1024 * 1024) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'File Terlalu Besar!',
                                    text: 'Ukuran file maksimal 5MB'
                                });
                                return;
                            }

                            const formData = new FormData();
                            formData.append('foto', file);

                            Swal.fire({
                                title: 'Upload Foto Profil',
                                text: 'Sedang mengunggah foto...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                willOpen: () => Swal.showLoading()
                            });

                            api.post('/mahasiswa/profile/avatar', formData, {
                                    headers: {
                                        'Content-Type': 'multipart/form-data'
                                    }
                                })
                                .then(response => {
                                    console.log('✅ Avatar upload response:', response.data);

                                    if (response.data?.success) {
                                        const avatar = document.getElementById('current-avatar');
                                        if (avatar) {
                                            avatar.style.backgroundImage = `url(${response.data.foto_url})`;
                                            avatar.innerHTML = '';
                                        }

                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Foto profil berhasil diperbarui',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: response.data?.message || 'Gagal mengunggah foto profil'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('❌ Error uploading avatar:', error);

                                    let errorMessage = 'Gagal mengunggah foto profil';
                                    if (error.response?.data?.message) {
                                        errorMessage = error.response.data.message;
                                    }

                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: errorMessage
                                    });
                                })
                                .finally(() => {
                                    avatarUpload.value = '';
                                });
                        }
                    });
                }
            }

            // ✅ LOAD WILAYAH DATA
            function loadWilayahData() {
                const wilayahSelect = document.getElementById('wilayah-select');
                if (!wilayahSelect) return;

                wilayahSelect.disabled = true;
                const currentOption = wilayahSelect.innerHTML;
                wilayahSelect.innerHTML = '<option value="">Memuat data wilayah...</option>';

                api.get('/wilayah')
                    .then(response => {
                        console.log('✅ Wilayah data loaded:', response.data);

                        if (response.data?.data) {
                            const wilayah = response.data.data;
                            const currentWilayahId = '{{ $mahasiswaData->wilayah_id ?? '' }}';

                            wilayahSelect.innerHTML = '<option value="">Pilih Wilayah</option>';

                            wilayah.forEach(w => {
                                const option = document.createElement('option');
                                option.value = w.wilayah_id;
                                option.textContent = w.nama_kota;
                                if (currentWilayahId && w.wilayah_id == currentWilayahId) {
                                    option.selected = true;
                                }
                                wilayahSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error loading wilayah:', error);
                        wilayahSelect.innerHTML = currentOption;
                        showErrorToast('Gagal memuat data wilayah');
                    })
                    .finally(() => {
                        wilayahSelect.disabled = false;
                    });
            }

            // ✅ LOAD SKILLS DATA 
            function loadSkillsData() {
                const skillSelect = $('#skill-select');
                skillSelect.empty().append('<option value="">Memuat data keahlian...</option>');

                api.get('/mahasiswa/profile/skills')
                    .then(response => {
                        console.log('✅ Skills data loaded:', response.data);

                        if (response.data?.success) {
                            skillSelect.empty();

                            // Load all available skills
                            response.data.allSkills.forEach(skill => {
                                const option = new Option(skill.nama_skill, skill.skill_id, false, false);
                                skillSelect.append(option);
                            });

                            // Set selected skills
                            const selectedSkills = response.data.userSkills.map(skill => skill.skill_id.toString());
                            skillSelect.val(selectedSkills);
                            skillSelect.trigger('change');

                            console.log('✅ Skills loaded successfully:', {
                                total: response.data.allSkills.length,
                                selected: selectedSkills.length
                            });
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error loading skills:', error);
                        skillSelect.empty().append('<option value="">Gagal memuat data</option>');
                        showErrorToast('Gagal memuat data keahlian');
                    });
            }

            // ✅ LOAD MINAT DATA
            function loadMinatData() {
                const minatSelect = $('#minat-select');
                minatSelect.empty().append('<option value="">Memuat data minat...</option>');

                api.get('/mahasiswa/profile/minat')
                    .then(response => {
                        console.log('✅ Minat data loaded:', response.data);

                        if (response.data?.success) {
                            minatSelect.empty();

                            // Load all available minat
                            response.data.data.all_minat.forEach(minat => {
                                const option = new Option(minat.nama_minat, minat.minat_id, false, false);
                                minatSelect.append(option);
                            });

                            // Set selected minat
                            const selectedMinat = response.data.data.user_minat.map(minat => minat.minat_id.toString());
                            minatSelect.val(selectedMinat);
                            minatSelect.trigger('change');

                            console.log('✅ Minat loaded successfully:', {
                                total: response.data.data.all_minat.length,
                                selected: selectedMinat.length
                            });
                        }
                    })
                    .catch(error => {
                        console.error('❌ Error loading minat:', error);
                        minatSelect.empty().append('<option value="">Gagal memuat data</option>');
                        showErrorToast('Gagal memuat data minat');
                    });
            }

            // Tambahkan fungsi ini setelah loadWilayahData
            function updateProfileViewData(user, mahasiswa) {
                if (user) {
                    // Update informasi user
                    document.querySelectorAll('[data-profile="name"]').forEach(el => {
                        el.textContent = user.name || '-';
                    });
                    document.querySelectorAll('[data-profile="email"]').forEach(el => {
                        el.textContent = user.email || '-';
                    });

                    // Update header name dan email
                    const headerName = document.querySelector('.text-white.mb-1');
                    const headerEmail = document.querySelector('.text-white-50.mb-0');
                    if (headerName) headerName.textContent = user.name;
                    if (headerEmail) headerEmail.textContent = user.email;
                }

                if (mahasiswa) {
                    // Update informasi mahasiswa
                    document.querySelectorAll('[data-profile="nim"]').forEach(el => {
                        el.textContent = mahasiswa.nim || '-';
                    });
                    document.querySelectorAll('[data-profile="telp"]').forEach(el => {
                        el.textContent = mahasiswa.telp || '-';
                    });
                    document.querySelectorAll('[data-profile="ipk"]').forEach(el => {
                        el.textContent = mahasiswa.ipk || '-';
                    });
                    document.querySelectorAll('[data-profile="alamat"]').forEach(el => {
                        el.textContent = mahasiswa.alamat || 'Belum ada data alamat';
                    });

                    // Update wilayah jika ada
                    if (mahasiswa.wilayah_id) {
                        const wilayahSelect = document.getElementById('wilayah-select');
                        const selectedOption = wilayahSelect?.querySelector(`option[value="${mahasiswa.wilayah_id}"]`);
                        const wilayahName = selectedOption ? selectedOption.textContent : 'Belum memilih preferensi wilayah';

                        document.querySelectorAll('[data-profile="wilayah"]').forEach(el => {
                            el.textContent = wilayahName;
                        });
                    }
                }
            }

            // ✅ INITIALIZE FORM SUBMISSIONS
            function initFormSubmissions() {
                // Profile form submission
                const profileForm = document.getElementById('profile-form');
                if (profileForm) {
                    profileForm.addEventListener('submit', function(event) {
                        event.preventDefault();

                        const formData = {
                            name: document.getElementById('edit-name')?.value,
                            telp: document.getElementById('edit-telp')?.value,
                            ipk: document.getElementById('edit-ipk')?.value,
                            alamat: document.getElementById('edit-alamat')?.value,
                            wilayah_id: document.getElementById('wilayah-select')?.value || null
                        };

                        // Convert wilayah_id to number if exists
                        if (formData.wilayah_id) {
                            formData.wilayah_id = parseInt(formData.wilayah_id);
                        }

                        const saveBtn = document.getElementById('save-btn-text');
                        const saveLoader = document.getElementById('save-btn-loader');
                        const submitButton = profileForm.querySelector('button[type="submit"]');

                        if (submitButton) submitButton.disabled = true;
                        if (saveBtn) saveBtn.style.opacity = '0.5';
                        if (saveLoader) saveLoader.classList.remove('d-none');

                        api.post('/mahasiswa/profile/update', formData)
                            .then(response => {
                                if (response.data?.success) {
                                    document.getElementById('profile-edit-mode').style.display = 'none';
                                    updateProfileViewData(response.data.user, response.data.mahasiswa);
                                    document.getElementById('profile-view-mode').style.display = 'block';

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Profil berhasil diperbarui',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error updating profile:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: error.response?.data?.message || 'Gagal memperbarui profil'
                                });
                            })
                            .finally(() => {
                                if (submitButton) submitButton.disabled = false;
                                if (saveBtn) saveBtn.style.opacity = '1';
                                if (saveLoader) saveLoader.classList.add('d-none');
                            });
                    });
                }

                // Skills form submission
                const skillsForm = document.getElementById('skills-form');
                if (skillsForm) {
                    skillsForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const selectedSkills = $('#skill-select').val();

                        api.post('/mahasiswa/profile/skills', {
                                skills: selectedSkills
                            })
                            .then(response => {
                                if (response.data?.success) {
                                    document.getElementById('skills-edit-mode').style.display = 'none';
                                    updateSkillsView(response.data.skills);
                                    document.getElementById('skills-view-mode').style.display = 'block';
                                    showSuccessToast('Keahlian berhasil diperbarui');
                                }
                            })
                            .catch(error => {
                                console.error('Error updating skills:', error);
                                showErrorToast('Gagal memperbarui keahlian');
                            });
                    });
                }

                // Minat form submission
                const minatForm = document.getElementById('minat-form');
                if (minatForm) {
                    minatForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const selectedMinat = $('#minat-select').val();

                        api.post('/mahasiswa/profile/minat', {
                                minat: selectedMinat
                            })
                            .then(response => {
                                if (response.data?.success) {
                                    document.getElementById('minat-edit-mode').style.display = 'none';
                                    updateMinatView(response.data.data.minat);
                                    document.getElementById('minat-view-mode').style.display = 'block';
                                    showSuccessToast('Minat berhasil diperbarui');
                                }
                            })
                            .catch(error => {
                                console.error('Error updating minat:', error);
                                showErrorToast('Gagal memperbarui minat');
                            });
                    });
                }

                // Password form submission
                const passwordForm = document.getElementById('password-form');
                if (passwordForm) {
                    passwordForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const formData = new FormData(this);

                        api.post('/mahasiswa/profile/password', formData)
                            .then(response => {
                                if (response.data?.success) {
                                    this.reset();
                                    showSuccessToast('Password berhasil diperbarui');
                                }
                            })
                            .catch(error => {
                                console.error('Error updating password:', error);
                                showErrorToast(error.response?.data?.message || 'Gagal mengubah password');
                            });
                    });
                }
            }

            // ✅ Utility Functions
            function updateSkillsView(skills) {
                const container = document.getElementById('skills-container');
                if (!container) return;

                container.innerHTML = '';
                if (skills && skills.length > 0) {
                    skills.forEach(skill => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-light-primary';
                        badge.textContent = skill.nama_skill || skill.nama;
                        container.appendChild(badge);
                    });
                } else {
                    container.innerHTML = '<p class="text-muted mb-0">Belum ada keahlian yang ditambahkan</p>';
                }
            }

            function updateMinatView(minat) {
                const container = document.getElementById('minat-container');
                if (!container) return;

                container.innerHTML = '';
                if (minat && minat.length > 0) {
                    minat.forEach(item => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-light-success';
                        badge.textContent = item.nama_minat;
                        container.appendChild(badge);
                    });
                } else {
                    container.innerHTML = '<p class="text-muted mb-0">Belum ada minat yang ditambahkan</p>';
                }
            }

            // Toast notifications
            function showSuccessToast(message) {
                const toast = new bootstrap.Toast(document.getElementById('success-toast'));
                document.getElementById('success-message').textContent = message;
                toast.show();
            }

            function showErrorToast(message) {
                const toast = new bootstrap.Toast(document.getElementById('error-toast'));
                document.getElementById('error-message').textContent = message;
                toast.show();
            }

            // Tambahkan fungsi ini di bagian JavaScript profile.blade.php
            function initCVUpload() {
                console.log('🔄 Initializing CV Upload...');

                const uploadButton = document.getElementById('cv-upload-button');
                const uploadInput = document.getElementById('cv-upload-input');
                const cvStatus = document.getElementById('cv-status');

                if (!uploadButton || !uploadInput) {
                    console.warn('⚠️ CV upload elements not found');
                    return;
                }

                uploadButton.addEventListener('click', () => uploadInput.click());

                uploadInput.addEventListener('change', function(event) {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];

                        // Validate file type
                        if (file.type !== 'application/pdf') {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Tidak Valid',
                                text: 'Hanya file PDF yang diperbolehkan'
                            });
                            return;
                        }

                        // Validate file size (max 5MB)
                        if (file.size > 5 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File Terlalu Besar',
                                text: 'Ukuran file maksimal 5MB'
                            });
                            return;
                        }

                        // Create FormData
                        const formData = new FormData();
                        formData.append('file', file);

                        // Show loading state
                        Swal.fire({
                            title: 'Mengupload CV',
                            text: 'Mohon tunggu...',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Upload CV
                        api.post('/mahasiswa/profile/cv', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(response => {
                                if (response.data?.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'CV berhasil diupload',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // Update CV status
                                    if (cvStatus) {
                                        cvStatus.innerHTML = `
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <div>
                                        CV telah diupload - <a href="${response.data.cv_url}" target="_blank">Lihat CV</a>
                                    </div>
                                </div>
                            `;
                                    }

                                    // Trigger profile completion check
                                    if (typeof checkProfileCompletion === 'function') {
                                        checkProfileCompletion();
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error uploading CV:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Upload CV',
                                    text: error.response?.data?.message ||
                                        'Terjadi kesalahan saat mengupload CV'
                                });
                            });
                    }
                });

                // Delete CV button handler
                const deleteButton = document.getElementById('cv-delete-button');
                if (deleteButton) {
                    deleteButton.addEventListener('click', function() {
                        Swal.fire({
                            title: 'Hapus CV?',
                            text: 'Anda yakin ingin menghapus CV?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                api.delete('/mahasiswa/profile/cv')
                                    .then(response => {
                                        if (response.data?.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Berhasil!',
                                                text: 'CV berhasil dihapus',
                                                timer: 2000,
                                                showConfirmButton: false
                                            });

                                            // Update CV status
                                            if (cvStatus) {
                                                cvStatus.innerHTML = `
                                        <div class="alert alert-warning d-flex align-items-center" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <div>
                                                CV belum diupload
                                            </div>
                                        </div>
                                    `;
                                            }

                                            // Trigger profile completion check
                                            if (typeof checkProfileCompletion === 'function') {
                                                checkProfileCompletion();
                                            }
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error deleting CV:', error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal Menghapus CV',
                                            text: error.response?.data?.message ||
                                                'Terjadi kesalahan saat menghapus CV'
                                        });
                                    });
                            }
                        });
                    });
                }
            }

            // Add this function in your JavaScript section
function handleCVChange(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validate file type
        if (file.type !== 'application/pdf') {
            Swal.fire({
                icon: 'error',
                title: 'File Tidak Valid',
                text: 'Hanya file PDF yang diperbolehkan'
            });
            input.value = ''; // Clear the input
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 5MB'
            });
            input.value = ''; // Clear the input
            return;
        }

        // Create FormData
        const formData = new FormData();
        formData.append('cv', file);

        // Show loading state
        Swal.fire({
            title: 'Mengupload CV',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => Swal.showLoading()
        });

        // Upload CV
        api.post('/mahasiswa/profile/cv', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(response => {
            if (response.data?.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'CV berhasil diupload',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload page to update CV status
                    window.location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error uploading CV:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Upload CV',
                text: error.response?.data?.message || 'Terjadi kesalahan saat mengupload CV'
            });
            input.value = ''; // Clear the input
        });
    }
}
        </script>
    @endpush
