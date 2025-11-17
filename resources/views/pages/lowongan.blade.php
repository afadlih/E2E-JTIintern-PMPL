@extends('layouts.app', ['class' => 'g-sidenav-show'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Lowongan'])
    <div class="container-fluid py-4">
        <div class="card pt-4">
            <div class="d-flex justify-content-between mb-3 px-4">
                <div class="d-flex gap-2">
                    <select id="perusahaanFilter" class="form-select form-select-sm" style="width: auto; height: 38px">
                        <option value="">Semua Perusahaan</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn" style="color: white; background: #02A232;" onclick="tambahLowongan()">
                        <i class="bi bi-plus-square-fill me-2"></i>Tambah Lowongan
                    </button>
                </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Judul
                                    Lowongan</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Perusahaan
                                </th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Kapasitas</th>
                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    Tanggal Dibuat</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="lowongan-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tambahLowonganModal" tabindex="-1" aria-labelledby="tambahLowonganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahLowonganModalLabel">Tambah Lowongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tambahLowonganForm">
                        <div class="mb-3">
                            <label for="judul_lowongan" class="form-label">Judul Lowongan</label>
                            <input type="text" class="form-control" id="judul_lowongan" name="judul_lowongan" required>
                        </div>
                        <div class="mb-3">
                            <label for="perusahaan_id" class="form-label">Perusahaan</label>
                            <select class="form-select" id="perusahaan_id" name="perusahaan_id" required>
                                <option value="">Pilih Perusahaan</option>
                                <!-- Perusahaan akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="periode_id" class="form-label">Periode</label>
                            <select class="form-select" id="periode_id" name="periode_id" required>
                                <option value="">Pilih Periode</option>
                                <!-- Periode akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="skill_id" class="form-label">Skill (pilih beberapa)</label>
                            <select class="form-select" id="skill_id" name="skill_id[]" multiple required>
                                <option value="" disabled>Pilih Skill</option>
                                <!-- Skill akan dimuat di sini -->
                            </select>
                            <div class="form-text">
                                <small>Tekan tombol Ctrl (Windows) atau Command (Mac) untuk memilih beberapa skill</small>
                            </div>
                        </div>
                        
                        <!-- ✅ TAMBAHKAN: Field Minat -->
                        <div class="mb-3">
                            <label for="minat_id" class="form-label">
                                <i class="fas fa-heart text-danger me-2"></i>Minat yang Dibutuhkan (pilih beberapa)
                            </label>
                            <select class="form-select" id="minat_id" name="minat_id[]" multiple required>
                                <option value="" disabled>Pilih Minat</option>
                                <!-- Minat akan dimuat di sini -->
                            </select>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tekan tombol Ctrl (Windows) atau Command (Mac) untuk memilih beberapa minat yang sesuai dengan lowongan ini
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_id" class="form-label">Jenis</label>
                            <select class="form-select" id="jenis_id" name="jenis_id" required>
                                <option value="">Pilih Jenis</option>
                                <!-- Jenis akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="kapasitas" name="kapasitas" required>
                        </div>
                        
                        <!-- ✅ TAMBAHKAN: Field Minimal IPK -->
                        <div class="mb-3">
                            <label for="min_ipk" class="form-label">
                                <i class="fas fa-star text-warning me-2"></i>Minimal IPK
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="min_ipk" 
                                   name="min_ipk" 
                                   step="0.01" 
                                   min="0" 
                                   max="4.00" 
                                   placeholder="Contoh: 3.00"
                                   required>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan minimal IPK yang dibutuhkan (0.00 - 4.00)
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Lowongan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="detailLowonganModal" tabindex="-1" aria-labelledby="detailLowonganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="detailLowonganModalLabel">
                        <i class="bi bi-info-circle me-2"></i> Detail Lowongan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Lowongan</label>
                                <p id="detailJudulLowongan" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Perusahaan</label>
                                <p id="detailPerusahaan" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Periode</label>
                                <p id="detailPeriode" class="form-control-plaintext text-secondary"></p>
                            </div>
                        </div>
                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kapasitas</label>
                                <div class="d-flex flex-column">
                                    ${lowongan.kapasitas_tersedia !== undefined ? `
                                        <div class="card border-0 shadow-sm mb-2">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="mb-0 fw-bold">Status Kapasitas</h6>
                                                    <span class="badge ${lowongan.kapasitas_tersedia === 0 ? 'bg-danger' : 'bg-success'} rounded-pill px-3">
                                                        ${lowongan.kapasitas_tersedia === 0 ? 'Penuh' : 'Tersedia'}
                                                    </span>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <div>
                                                        <span class="h4 mb-0 text-primary fw-bold">${lowongan.kapasitas_tersedia}</span>
                                                        <span class="text-secondary h5"> / ${lowongan.kapasitas_total || lowongan.kapasitas}</span>
                                                    </div>
                                                    <span class="text-dark fw-medium">slot tersedia</span>
                                                </div>
                                                
                                                <div class="progress" style="height: 10px; border-radius: 10px; background-color: #e9ecef;">
                                                    <div class="progress-bar ${lowongan.kapasitas_tersedia === 0 ? 'bg-danger' : 'bg-success'}" 
                                                         role="progressbar" 
                                                         style="width: ${Math.round(((lowongan.kapasitas_total - lowongan.kapasitas_tersedia) / lowongan.kapasitas_total) * 100)}%; border-radius: 10px;" 
                                                         aria-valuenow="${Math.round(((lowongan.kapasitas_total - lowongan.kapasitas_tersedia) / lowongan.kapasitas_total) * 100)}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between mt-2">
                                                    <small class="text-muted">0</small>
                                                    <small class="text-muted">${lowongan.kapasitas_total || lowongan.kapasitas}</small>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light py-2 px-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="small">
                                                        <i class="fas fa-info-circle text-primary me-1"></i>
                                                        <span class="fw-medium text-dark">${lowongan.kapasitas_total - lowongan.kapasitas_tersedia}</span> slot terisi dari total
                                                        <span class="fw-medium text-dark">${lowongan.kapasitas_total}</span> slot
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="syncCapacity(${lowongan.id_lowongan})">
                                                        <i class="fas fa-sync-alt me-1"></i>Refresh
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Total ${lowongan.kapasitas_total || lowongan.kapasitas} orang, 
                                            <span class="text-${lowongan.kapasitas_tersedia === 0 ? 'danger' : 'success'}">
                                                ${lowongan.kapasitas_tersedia} slot tersedia
                                            </span>, 
                                            ${lowongan.kapasitas_total - lowongan.kapasitas_tersedia} slot terisi.
                                        </div>
                                    ` : `
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="icon-circle bg-warning bg-opacity-25 text-warning me-3" 
                                                         style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-0">Data Kapasitas Tidak Tersedia</h6>
                                                        <p class="text-muted small mb-0">Total kapasitas: ${lowongan.kapasitas} orang</p>
                                                    </div>
                                                </div>
                                                
                                                <p class="text-muted mb-3">
                                                    Informasi kapasitas tersedia belum diketahui. Silahkan sinkronkan data kapasitas untuk melihat slot yang masih tersedia.
                                                </p>
                                                
                                                <button class="btn btn-warning w-100" onclick="syncCapacity(${lowongan.id_lowongan})">
                                                    <i class="fas fa-sync-alt me-2"></i>Sinkronkan Data Kapasitas
                                                </button>
                                            </div>
                                        </div>
                                    `}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <p id="detailDeskripsi" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Skill</label>
                                <p id="detailSkill" class="form-control-plaintext text-secondary"></p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis</label>
                                <p id="detailJenis" class="form-control-plaintext text-secondary"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editLowonganModal" tabindex="-1" aria-labelledby="editLowonganModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editLowonganModalLabel">
                        <i class="bi bi-pencil-square me-2"></i> Edit Lowongan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editLowonganForm">
                        <input type="hidden" id="editLowonganId" name="id_lowongan">
                        <div class="mb-3">
                            <label for="editJudulLowongan" class="form-label">Judul Lowongan</label>
                            <input type="text" class="form-control" id="editJudulLowongan" name="judul_lowongan" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPerusahaanId" class="form-label">Perusahaan</label>
                            <select class="form-select" id="editPerusahaanId" name="perusahaan_id" required>
                                <option value="">Pilih Perusahaan</option>
                                <!-- Perusahaan akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPeriodeId" class="form-label">Periode</label>
                            <select class="form-select" id="editPeriodeId" name="periode_id" required>
                                <option value="">Pilih Periode</option>
                                <!-- Periode akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editSkillId" class="form-label">Skill (pilih beberapa)</label>
                            <select class="form-select" id="editSkillId" name="skill_id[]" multiple required>
                                <option value="" disabled>Pilih Skill</option>
                                <!-- Skills akan dimuat di sini -->
                            </select>
                            <div class="form-text">
                                <small>Tekan tombol Ctrl (Windows) atau Command (Mac) untuk memilih beberapa skill</small>
                            </div>
                        </div>
                        
                        <!-- ✅ TAMBAHKAN: Field Edit Minat -->
                        <div class="mb-3">
                            <label for="editMinatId" class="form-label">
                                <i class="fas fa-heart text-danger me-2"></i>Minat yang Dibutuhkan (pilih beberapa)
                            </label>
                            <select class="form-select" id="editMinatId" name="minat_id[]" multiple required>
                                <option value="" disabled>Pilih Minat</option>
                                <!-- Minat akan dimuat di sini -->
                            </select>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tekan tombol Ctrl (Windows) atau Command (Mac) untuk memilih beberapa minat yang sesuai dengan lowongan ini
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="editJenisId" class="form-label">Jenis</label>
                            <select class="form-select" id="editJenisId" name="jenis_id" required>
                                <option value="">Pilih Jenis</option>
                                <!-- Jenis akan dimuat di sini -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editKapasitas" class="form-label">Kapasitas</label>
                            <input type="number" class="form-control" id="editKapasitas" name="kapasitas" required>
                        </div>
                        
                        <!-- ✅ TAMBAHKAN: Field Edit Minimal IPK -->
                        <div class="mb-3">
                            <label for="editMinIpk" class="form-label">
                                <i class="fas fa-star text-warning me-2"></i>Minimal IPK
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="editMinIpk" 
                                   name="min_ipk" 
                                   step="0.01" 
                                   min="0" 
                                   max="4.00" 
                                   placeholder="Contoh: 3.00"
                                   required>
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan minimal IPK yang dibutuhkan (0.00 - 4.00)
                                </small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editDeskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="editDeskripsi" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning text-white">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/lowongan.css') }}">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true
        });

        // Fungsi untuk memuat data perusahaan ke dropdown filter
        function loadFilterOptions() {
            api.get('/perusahaan')
                .then(function (response) {
                    console.log('API /perusahaan Response:', response.data); // Debugging

                    if (response.data.success && response.data.data) {
                        const perusahaanSelect = document.getElementById('perusahaan_id');
                        const editPerusahaanSelect = document.getElementById('editPerusahaanId');
                        const perusahaanFilter = document.getElementById('perusahaanFilter');

                        // Pastikan elemen ditemukan
                        if (!perusahaanSelect || !editPerusahaanSelect || !perusahaanFilter) {
                            console.error('One or more dropdown elements not found!');
                            return;
                        }

                        // Tambahkan opsi default
                        perusahaanSelect.innerHTML = '<option value="">Pilih Perusahaan</option>';
                        editPerusahaanSelect.innerHTML = '<option value="">Pilih Perusahaan</option>';
                        perusahaanFilter.innerHTML = '<option value="">Semua Perusahaan</option>';

                        // Tambahkan opsi perusahaan
                        response.data.data.forEach(function (perusahaan) {
                            const option = `<option value="${perusahaan.perusahaan_id}">${perusahaan.nama_perusahaan}</option>`;
                            perusahaanSelect.innerHTML += option;
                            editPerusahaanSelect.innerHTML += option;
                            perusahaanFilter.innerHTML += option; // Tambahkan ke filter
                        });
                    } else {
                        console.error('Invalid API response:', response.data);
                    }
                })
                .catch(function (error) {
                    console.error('Error loading perusahaan:', error);
                });
        }

        // Fungsi untuk memuat data periode ke dropdown
        function loadPeriodeOptions() {
            api.get('/periode')
                .then(function (response) {
                    console.log('API /periode Response:', response.data); // Debugging

                    if (response.data.success) {
                        const periodeSelect = document.getElementById('periode_id');
                        const editPeriodeSelect = document.getElementById('editPeriodeId');

                        periodeSelect.innerHTML = '<option value="">Pilih Periode</option>';
                        editPeriodeSelect.innerHTML = '<option value="">Pilih Periode</option>';

                        response.data.data.forEach(function (periode) {
                            const option = `<option value="${periode.periode_id}">${periode.waktu}</option>`;
                            periodeSelect.innerHTML += option;
                            editPeriodeSelect.innerHTML += option;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading periode:', error);
                });
        }

        function loadSkillOptions() {
            api.get('/skill')
                .then(function (response) {
                    if (response.data.success) {
                        const skillSelect = document.getElementById('skill_id');
                        skillSelect.innerHTML = '<option value="" disabled>Pilih Skill</option>';
                        response.data.data.forEach(function (skill) {
                            const option = `<option value="${skill.skill_id}">${skill.nama}</option>`;
                            skillSelect.innerHTML += option;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading skill:', error);
                });
        }

        function loadJenisOptions() {
            api.get('/jenis')
                .then(function (response) {
                    if (response.data.success) {
                        const jenisSelect = document.getElementById('jenis_id');
                        jenisSelect.innerHTML = '<option value="">Pilih Jenis</option>';
                        response.data.data.forEach(function (jenis) {
                            const option = `<option value="${jenis.jenis_id}">${jenis.nama_jenis}</option>`;
                            jenisSelect.innerHTML += option;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading jenis:', error);
                });
        }

        function loadEditSkillOptions(selectedIds = []) {
            api.get('/skill')
                .then(function (response) {
                    if (response.data.success) {
                        const skillSelect = document.getElementById('editSkillId');
                        skillSelect.innerHTML = '<option value="" disabled>Pilih Skill</option>';
                        response.data.data.forEach(function (skill) {
                            // Check if this skill is in the selectedIds array
                            const selected = selectedIds.includes(skill.skill_id) ? 'selected' : '';
                            skillSelect.innerHTML += `<option value="${skill.skill_id}" ${selected}>${skill.nama}</option>`;
                        });
                    }
                });
        }

        function loadEditJenisOptions(selectedId = null) {
            api.get('/jenis')
                .then(function (response) {
                    if (response.data.success) {
                        const jenisSelect = document.getElementById('editJenisId');
                        jenisSelect.innerHTML = '<option value="">Pilih Jenis</option>';
                        response.data.data.forEach(function (jenis) {
                            const selected = selectedId == jenis.jenis_id ? 'selected' : '';
                            jenisSelect.innerHTML += `<option value="${jenis.jenis_id}" ${selected}>${jenis.nama_jenis}</option>`;
                        });
                    }
                });
        }

        function loadEditPeriodeOptions(selectedId = null) {
            api.get('/periode')
                .then(function (response) {
                    if (response.data.success) {
                        const editPeriodeSelect = document.getElementById('editPeriodeId');
                        editPeriodeSelect.innerHTML = '<option value="">Pilih Periode</option>';
                        response.data.data.forEach(function (periode) {
                            const selected = selectedId == periode.periode_id ? 'selected' : '';
                            editPeriodeSelect.innerHTML += `<option value="${periode.periode_id}" ${selected}>${periode.waktu}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading periode:', error);
                });
        }

        // ✅ TAMBAHKAN: Function untuk load minat options
        function loadMinatOptions() {
            api.get('/minat')
                .then(function (response) {
                    if (response.data.success) {
                        const minatSelect = document.getElementById('minat_id');
                        minatSelect.innerHTML = '<option value="" disabled>Pilih Minat</option>';
                        response.data.data.forEach(function (minat) {
                            const option = `<option value="${minat.minat_id}">${minat.nama_minat}</option>`;
                            minatSelect.innerHTML += option;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading minat:', error);
                });
        }

        // ✅ TAMBAHKAN: Function untuk load edit minat options
        function loadEditMinatOptions(selectedIds = []) {
            api.get('/minat')
                .then(function (response) {
                    if (response.data.success) {
                        const minatSelect = document.getElementById('editMinatId');
                        minatSelect.innerHTML = '<option value="" disabled>Pilih Minat</option>';
                        response.data.data.forEach(function (minat) {
                            // Check if this minat is in the selectedIds array
                            const selected = selectedIds.includes(minat.minat_id) ? 'selected' : '';
                            minatSelect.innerHTML += `<option value="${minat.minat_id}" ${selected}>${minat.nama_minat}</option>`;
                        });
                    }
                });
        }

        function loadLowonganData(filters = {}) {
            // Show loading state
            const tableBody = document.getElementById('lowongan-table-body');
            tableBody.innerHTML = `
                                                    <tr>
                                                        <td colspan="5" class="text-center py-5">
                                                            <div class="spinner-border text-primary" role="status"></div>
                                                            <p class="mt-2 text-sm text-secondary">Memuat data lowongan...</p>
                                                        </td>
                                                    </tr>
                                                `;

            api.get('/lowongan', { params: filters })
                .then(function (response) {
                    tableBody.innerHTML = ''; // Kosongkan tabel sebelum memuat data baru

                    if (response.data.success && response.data.data.length > 0) {
                        response.data.data.forEach((lowongan, index) => {
                            const date = new Date(lowongan.created_at);
                            const formattedDate = date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });

                            // Format capacity display to show available/total
                            const capacityDisplay = lowongan.kapasitas_tersedia !== undefined ?
                                `${lowongan.kapasitas_tersedia}/${lowongan.kapasitas_total}` :
                                `${lowongan.kapasitas}`;

                            const row = document.createElement('tr');
                            row.style.animation = `fadeIn 0.3s ease forwards ${index * 0.05}s`;
                            row.innerHTML = `
                                                                    <td>
                                                                        <p class="text-sm font-weight-bold mb-0">${lowongan.judul_lowongan}</p>
                                                                    </td>
                                                                    <td>
                                                                        <div class="d-flex px-2 py-1">
                                                                            <div class="d-flex flex-column justify-content-center">
                                                                                <h6 class="mb-0 text-sm">${lowongan.perusahaan.nama_perusahaan}</h6>
                                                                                <p class="text-xs text-secondary mb-0">${lowongan.perusahaan.nama_kota}</p>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <span class="badge ${lowongan.kapasitas_tersedia === 0 ? 'bg-danger' : 'bg-success'} text-white">
                                                                            ${capacityDisplay}
                                                                        </span>
                                                                    </td>
                                                                    <td class="align-middle text-center">
                                                                        <span class="text-secondary text-xs font-weight-bold">${formattedDate}</span>
                                                                    </td>
                                                                <td class="align-middle">
                    <div class="action-buttons">  <!-- Ganti class dari "d-flex gap-1" menjadi "action-buttons" -->
                        <button class="btn btn-sm btn-info me-1" onclick="detailLowongan(${lowongan.id_lowongan})" title="Lihat Detail">
                            <i class="fas fa-eye me-1"></i>Detail
                        </button>
                        <button class="btn btn-sm btn-primary me-1" onclick="editLowongan(${lowongan.id_lowongan})" title="Edit Lowongan">
                            <i class="fas fa-edit me-1"></i>Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteLowongan(${lowongan.id_lowongan})" title="Hapus Lowongan">
                            <i class="fas fa-trash-alt me-1"></i>Hapus
                        </button>
                    </div>
                </td>
                                                                `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        tableBody.innerHTML = `
                                                                <tr>
                                                                    <td colspan="5">
                                                                        <div class="text-center py-5">
                                                                            <div class="empty-state-icon mb-3">
                                                                                <i class="bi bi-clipboard-x" style="font-size: 3rem; color: #8898aa;"></i>
                                                                            </div>
                                                                            <h6 class="text-muted">Tidak ada lowongan tersedia</h6>
                                                                            <p class="text-xs text-secondary mb-0">
                                                                                ${filters.perusahaan_id ? 'Belum ada lowongan untuk perusahaan ini' : 'Belum ada lowongan yang ditambahkan'}
                                                                            </p>
                                                                            <button class="btn btn-sm btn-outline-primary mt-3" onclick="tambahLowongan()">
                                                                                <i class="bi bi-plus-lg me-1"></i>Tambah Lowongan Baru
                                                                            </button>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            `;
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    tableBody.innerHTML = `
                                                            <tr>
                                                                <td colspan="5">
                                                                    <div class="alert alert-danger mx-3 my-4">
                                                                        <div class="d-flex">
                                                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                                                            <div>
                                                                                <h6 class="alert-heading mb-1">Gagal memuat data</h6>
                                                                                <p class="mb-0">Terjadi kesalahan saat memuat data lowongan. Silakan coba lagi.</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        `;
                });
        }

        // ✅ UPDATE function detailLowongan untuk menampilkan min_ipk
        function detailLowongan(id) {
            console.log('Fetching detail for Lowongan ID:', id);

            // Show modal with loading state
            const detailModal = document.getElementById('detailLowonganModal');
            const modalBody = detailModal.querySelector('.modal-body');

            modalBody.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <p class="text-muted">Memuat detail lowongan...</p>
                </div>
            `;

            // Show the modal while loading
            const modal = new bootstrap.Modal(detailModal);
            modal.show();

            api.get(`/lowongan/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const lowongan = response.data.data;
                        console.log('Lowongan Detail Data:', lowongan);

                        // Format skills for display
                        let skillsDisplay = '';
                        if (Array.isArray(lowongan.skills)) {
                            skillsDisplay = lowongan.skills.map(skill => skill.nama).join(', ');
                        } else if (lowongan.skill && lowongan.skill.nama) {
                            skillsDisplay = lowongan.skill.nama;
                        }

                        // ✅ TAMBAHKAN: Format minat for display
                        let minatDisplay = '';
                        if (Array.isArray(lowongan.minat)) {
                            minatDisplay = lowongan.minat.map(minat => minat.nama_minat).join(', ');
                        } else if (lowongan.minat && lowongan.minat.nama_minat) {
                            minatDisplay = lowongan.minat.nama_minat;
                        }
                        lowongan.minat_display = minatDisplay;

                        // Add animation to the content when it loads
                        modalBody.style.opacity = "0";
                        modalBody.innerHTML = `
    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-md-6">
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-briefcase text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Judul Lowongan</label>
                </div>
                <div class="bg-light rounded p-3">
                    <h5 class="mb-0">${lowongan.judul_lowongan}</h5>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-building text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Perusahaan</label>
                </div>
                <div class="bg-light rounded p-3">
                    <h6 class="mb-0">${lowongan.perusahaan.nama_perusahaan}</h6>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Periode</label>
                </div>
                <div class="bg-light rounded p-3">
                    <h6 class="mb-0">${lowongan.periode.waktu}</h6>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-tag text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Jenis</label>
                </div>
                <div class="bg-light rounded p-3">
                    <h6 class="mb-0">${lowongan.jenis.nama_jenis}</h6>
                </div>
            </div>

            <!-- ✅ TAMBAHKAN: Display Minimal IPK -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-star text-warning me-2"></i>
                    <label class="form-label fw-bold mb-0">Minimal IPK</label>
                </div>
                <div class="bg-light rounded p-3">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning text-dark fs-6 fw-bold px-3 py-2 me-2">
                            ${lowongan.min_ipk ? parseFloat(lowongan.min_ipk).toFixed(2) : '0.00'}
                        </span>
                        <small class="text-muted">dari skala 4.00</small>
                    </div>
                    ${lowongan.min_ipk ? `
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Mahasiswa harus memiliki IPK minimal ${parseFloat(lowongan.min_ipk).toFixed(2)} untuk dapat mendaftar
                            </small>
                        </div>
                    ` : `
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Tidak ada persyaratan IPK minimal
                            </small>
                        </div>
                    `}
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan -->
        <div class="col-md-6">
            <!-- Kapasitas Section -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-users text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Kapasitas</label>
                </div>
                <div class="d-flex flex-column">
                    ${lowongan.kapasitas_tersedia !== undefined ? `
                        <div class="card border-0 shadow-sm mb-2">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold">Status Kapasitas</h6>
                                    <span class="badge ${lowongan.kapasitas_tersedia === 0 ? 'bg-danger' : 'bg-success'} rounded-pill px-3">
                                        ${lowongan.kapasitas_tersedia === 0 ? 'Penuh' : 'Tersedia'}
                                    </span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <span class="h4 mb-0 text-primary fw-bold">${lowongan.kapasitas_tersedia}</span>
                                        <span class="text-secondary h5"> / ${lowongan.kapasitas_total || lowongan.kapasitas}</span>
                                    </div>
                                    <span class="text-dark fw-medium">slot tersedia</span>
                                </div>
                                
                                <div class="progress" style="height: 10px; border-radius: 10px; background-color: #e9ecef;">
                                    <div class="progress-bar ${lowongan.kapasitas_tersedia === 0 ? 'bg-danger' : 'bg-success'}" 
                                         role="progressbar" 
                                         style="width: ${Math.round(((lowongan.kapasitas_total - lowongan.kapasitas_tersedia) / lowongan.kapasitas_total) * 100)}%; border-radius: 10px;" 
                                         aria-valuenow="${Math.round(((lowongan.kapasitas_total - lowongan.kapasitas_tersedia) / lowongan.kapasitas_total) * 100)}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">0</small>
                                    <small class="text-muted">${lowongan.kapasitas_total || lowongan.kapasitas}</small>
                                </div>
                            </div>
                            <div class="card-footer bg-light py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small">
                                        <i class="fas fa-info-circle text-primary me-1"></i>
                                        <span class="fw-medium text-dark">${lowongan.kapasitas_total - lowongan.kapasitas_tersedia}</span> slot terisi dari total
                                        <span class="fw-medium text-dark">${lowongan.kapasitas_total}</span> slot
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="syncCapacity(${lowongan.id_lowongan})">
                                        <i class="fas fa-sync-alt me-1"></i>Refresh
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="small text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Total ${lowongan.kapasitas_total || lowongan.kapasitas} orang, 
                            <span class="text-${lowongan.kapasitas_tersedia === 0 ? 'danger' : 'success'}">
                                ${lowongan.kapasitas_tersedia} slot tersedia
                            </span>, 
                            ${lowongan.kapasitas_total - lowongan.kapasitas_tersedia} slot terisi.
                        </div>
                    ` : `
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle bg-warning bg-opacity-25 text-warning me-3" 
                                         style="width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-exclamation-triangle fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Data Kapasitas Tidak Tersedia</h6>
                                        <p class="text-muted small mb-0">Total kapasitas: ${lowongan.kapasitas} orang</p>
                                    </div>
                                </div>
                                
                                <p class="text-muted mb-3">
                                    Informasi kapasitas tersedia belum diketahui. Silahkan sinkronkan data kapasitas untuk melihat slot yang masih tersedia.
                                </p>
                                
                                <button class="btn btn-warning w-100" onclick="syncCapacity(${lowongan.id_lowongan})">
                                    <i class="fas fa-sync-alt me-2"></i>Sinkronkan Data Kapasitas
                                </button>
                            </div>
                        </div>
                    `}
                </div>
            </div>
            
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-code text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Skill yang Dibutuhkan</label>
                </div>
                <div class="bg-light rounded p-3">
                    <div class="d-flex flex-wrap gap-1">
                        ${skillsDisplay ? 
                            skillsDisplay.split(', ').map(skill => 
                                `<span class="badge bg-info bg-opacity-10 text-primary-emphasis fw-medium py-2 px-3">${skill}</span>`
                            ).join('') :
                            '<span class="text-muted">Tidak ada skill yang ditentukan</span>'
                        }
                    </div>
                </div>
            </div>

            <!-- ✅ TAMBAHKAN: Display Minat -->
            <div class="mb-4">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-heart text-danger me-2"></i>
                    <label class="form-label fw-bold mb-0">Minat yang Dibutuhkan</label>
                </div>
                <div class="bg-light rounded p-3">
                    <div class="d-flex flex-wrap gap-1">
                        ${lowongan.minat_display ? 
                            lowongan.minat_display.split(', ').map(minat => 
                                `<span class="badge bg-danger bg-opacity-10 text-danger-emphasis fw-medium py-2 px-3">${minat}</span>`
                            ).join('') :
                            '<span class="text-muted">Tidak ada minat yang ditentukan</span>'
                        }
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-12">
            <div class="mb-2">
                <div class="d-flex align-items-center mb-2">
                    <i class="fas fa-align-left text-primary me-2"></i>
                    <label class="form-label fw-bold mb-0">Deskripsi</label>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body bg-light p-3">
                        <div class="description-content" style="white-space: pre-line">
                            ${lowongan.deskripsi}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
`;


                        // Fade in animation
                        setTimeout(() => {
                            modalBody.style.transition = "opacity 0.3s ease";
                            modalBody.style.opacity = "1";
                        }, 150);

                    } else {
                        modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Gagal memuat detail lowongan.
                    </div>
                `;
                    }
                })
                .catch(function (error) {
                    console.error('Error fetching detail lowongan:', error);
                    modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Terjadi kesalahan saat memuat detail lowongan.
                </div>
            `;
                });
        }

        // Fungsi untuk membuka modal tambah lowongan
        function tambahLowongan() {
            loadFilterOptions();
            loadPeriodeOptions();
            loadSkillOptions();
            loadMinatOptions();  // ✅ TAMBAHKAN ini
            loadJenisOptions();

            const modal = new bootstrap.Modal(document.getElementById('tambahLowonganModal'));
            modal.show();
        }

        // ✅ PERBAIKI: Form submission handler dengan debugging yang lebih detail
        document.getElementById('tambahLowonganForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...`;

            // ✅ DEBUGGING: Check minat selection
            const minatSelect = document.getElementById('minat_id');
            const selectedMinat = [...minatSelect.selectedOptions].map(o => o.value);
            
            console.log('🔍 Debug form submission:');
            console.log('Minat select element:', minatSelect);
            console.log('Selected minat options:', minatSelect.selectedOptions);
            console.log('Selected minat values:', selectedMinat);
            console.log('Minat count:', selectedMinat.length);

            if (selectedMinat.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal satu minat untuk lowongan ini.',
                });
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                return;
            }

            // Handle multiple selects
            const formData = new FormData(this);
            
            // Convert to JSON to properly handle arrays
            const jsonData = {};
            formData.forEach((value, key) => {
                if (jsonData[key]) {
                    if (Array.isArray(jsonData[key])) {
                        jsonData[key].push(value);
                    } else {
                        jsonData[key] = [jsonData[key], value];
                    }
                } else {
                    jsonData[key] = value;
                }
            });

            // Special handling for multi-select fields
            const selectedSkills = [...document.getElementById('skill_id').selectedOptions].map(o => o.value);
            
            jsonData.skill_id = selectedSkills;
            jsonData.minat_id = selectedMinat;  // Use the verified array

            console.log('📤 Final data being sent:', jsonData);
            console.log('📤 Minat data specifically:', jsonData.minat_id);

            api.post('/lowongan', jsonData)
                .then(function (response) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    console.log('📥 Server response:', response.data);

                    if (response.data.success) {
                        // Show debug info if available
                        if (response.data.debug) {
                            console.log('🔍 Server debug info:', response.data.debug);
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Lowongan berhasil ditambahkan!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('tambahLowonganModal'));
                            modal.hide();
                            document.getElementById('tambahLowonganForm').reset();
                            loadLowonganData();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.data.message || 'Gagal menambahkan lowongan.',
                        });
                    }
                })
                .catch(function (error) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    console.error('❌ Error adding lowongan:', error);
                    console.error('❌ Error response:', error.response?.data);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.response?.data?.message || 'Terjadi kesalahan saat menambahkan lowongan.',
                    });
                });
        });

        // Fungsi untuk membuka modal edit lowongan
        function editLowongan(id) {
            api.get(`/lowongan/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const lowongan = response.data.data;

                        document.getElementById('editLowonganId').value = lowongan.id_lowongan;
                        document.getElementById('editJudulLowongan').value = lowongan.judul_lowongan;
                        document.getElementById('editPerusahaanId').value = lowongan.perusahaan.perusahaan_id;
                        loadEditPeriodeOptions(lowongan.periode.periode_id);
                        document.getElementById('editKapasitas').value = lowongan.kapasitas;
                        document.getElementById('editMinIpk').value = lowongan.min_ipk || '';
                        document.getElementById('editDeskripsi').value = lowongan.deskripsi;

                        // Extract skill IDs
                        let skillIds = [];
                        if (Array.isArray(lowongan.skills)) {
                            skillIds = lowongan.skills.map(skill => skill.skill_id);
                        } else if (lowongan.skill && lowongan.skill.skill_id) {
                            skillIds = [lowongan.skill.skill_id];
                        }
                        loadEditSkillOptions(skillIds);

                        // ✅ TAMBAHKAN: Extract minat IDs
                        let minatIds = [];
                        if (Array.isArray(lowongan.minat)) {
                            minatIds = lowongan.minat.map(minat => minat.minat_id);
                        } else if (lowongan.minat && lowongan.minat.minat_id) {
                            minatIds = [lowongan.minat.minat_id];
                        }
                        loadEditMinatOptions(minatIds);

                        loadEditJenisOptions(lowongan.jenis.jenis_id);

                        const modal = new bootstrap.Modal(document.getElementById('editLowonganModal'));
                        modal.show();
                    } else {
                        Swal.fire('Error', 'Gagal memuat data lowongan.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data lowongan.', 'error');
                });
        }

        document.getElementById('editLowonganForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('editLowonganId').value;
            const formData = new FormData(this);

            // Show loading state on button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...`;

            // Convert FormData to JSON to ensure proper handling of arrays
            const jsonData = {};
            formData.forEach((value, key) => {
                if (jsonData[key]) {
                    if (Array.isArray(jsonData[key])) {
                        jsonData[key].push(value);
                    } else {
                        jsonData[key] = [jsonData[key], value];
                    }
                } else {
                    jsonData[key] = value;
                }
            });

            // Special handling for multi-select fields
            const selectedSkills = [...document.getElementById('editSkillId').selectedOptions].map(o => o.value);
            const selectedMinat = [...document.getElementById('editMinatId').selectedOptions].map(o => o.value);
            
            jsonData.skill_id = selectedSkills;
            jsonData.minat_id = selectedMinat;  // ✅ TAMBAHKAN ini

            console.log("Submitting edit data:", jsonData); // For debugging

            api.put(`/lowongan/${id}`, jsonData)
                .then(function (response) {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Lowongan berhasil diperbarui!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editLowonganModal'));
                            modal.hide();
                            loadLowonganData();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.data.message || 'Gagal memperbarui lowongan.',
                        });
                    }
                })
                .catch(function (error) {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;

                    console.error('Error updating lowongan:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat memperbarui lowongan.',
                    });
                });
        });

        // Fungsi untuk menghapus lowongan
        function deleteLowongan(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Lowongan yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return api.delete(`/lowongan/${id}`)
                        .then(response => {
                            if (!response.data.success) {
                                throw new Error(response.data.message || 'Gagal menghapus lowongan');
                            }
                            return response.data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Terjadi kesalahan: ${error.response?.data?.message || error.message}`
                            );
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Lowongan berhasil dihapus.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        loadLowonganData();
                    });
                }
            });
        }

        // Add this function to your JavaScript
        function syncCapacity(id) {
            // Show loading state
            const syncBtn = event.currentTarget;
            const originalContent = syncBtn.innerHTML;
            syncBtn.disabled = true;
            syncBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Sinkronisasi...`;
            
            api.post(`/lowongan/${id}/sync-capacity`)
                .then(response => {
                    if (response.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data kapasitas berhasil disinkronkan',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            // Refresh the detail view
                            detailLowongan(id);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.data.message || 'Gagal menyinkronkan data kapasitas'
                        });
                        syncBtn.disabled = false;
                        syncBtn.innerHTML = originalContent;
                    }
                })
                .catch(error => {
                    console.error('Error syncing capacity:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyinkronkan data kapasitas'
                    });
                    syncBtn.disabled = false;
                    syncBtn.innerHTML = originalContent;
                });
        }

        // Load data saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadFilterOptions();
            loadLowonganData();
        });

        // Event listener untuk filter perusahaan
        document.getElementById('perusahaanFilter').addEventListener('change', function (e) {
            loadLowonganData({ perusahaan_id: e.target.value });
        });
    </script>
@endpush