@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Permintaan Magang'])
    <div class="card">
        <div class="card-header px-4 py-3">
            <div class="search_card">
                <div class="search-filter d-flex gap-3">
                    <!-- Komponen Pencarian -->
                    <div class="search-box">
                        <input type="text" class="form-control search-input" placeholder="Cari Lowongan">
                        <i class="fas fa-search search-icon"></i>
                    </div>

                    <!-- Filter Perusahaan (terpisah dari search-box) -->
                    <div class="dropdown">
                        <button class="btn filter-btn dropdown-toggle" type="button" id="dropdownPerusahaan"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-building"></i>
                            <span>Perusahaan</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" id="perusahaanDropdown"
                            aria-labelledby="dropdownPerusahaan">
                            <li><a class="dropdown-item active" href="#" data-perusahaan-id="all">Semua Perusahaan</a>
                            </li>
                            <!-- Daftar perusahaan akan dimuat di sini secara dinamis -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body px-4">
            <div class="permintaan-list">
                <!-- Data permintaan akan dimuat di sini melalui JavaScript -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Permintaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Data detail akan dimuat di sini melalui JavaScript -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/css/permintaan.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variabel global untuk menyimpan semua permintaan
        let allPermintaanData = [];

        // Modifikasi loadPermintaanData untuk menyimpan data dan memuat dropdown perusahaan
        function loadPermintaanData() {
            showLoadingState(); // Tampilkan state loading sebelum memuat data
            fetch('/api/magang', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    if (response.success) {
                        // Simpan data untuk filtering
                        allPermintaanData = response.data;

                        // Render daftar permintaan
                        renderPermintaanList(allPermintaanData);

                        // Muat opsi perusahaan untuk filter
                        loadPerusahaanOptions(allPermintaanData);
                    } else {
                        Swal.fire(
                            'Gagal Memuat Data',
                            response.message || 'Terjadi kesalahan saat memuat data permintaan magang.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat memuat data permintaan magang.',
                        'error'
                    );
                });
        }

        function showLoadingState() {
            const permintaanList = document.querySelector('.permintaan-list');
            if (permintaanList) {
                permintaanList.innerHTML = `
                                                        <div class="text-center py-5">
                                                            <div class="spinner-border text-primary mb-3" role="status"></div>
                                                            <p class="text-muted mb-0">Memuat data permintaan magang...</p>
                                                        </div>
                                                    `;
            }
        }


        // Fungsi untuk menampilkan daftar permintaan
        // Fungsi untuk menampilkan daftar permintaan dengan animasi
        // ✅ PERBAIKAN: Function renderPermintaanList - pastikan status ditolak ditampilkan dengan benar
        function renderPermintaanList(items) {
            const permintaanList = document.querySelector('.permintaan-list');
            if (!permintaanList) {
                console.error('Element .permintaan-list tidak ditemukan di halaman.');
                return;
            }

            permintaanList.innerHTML = '';

            if (items.length === 0) {
                permintaanList.innerHTML = `
                    <div class="empty-state text-center py-5">
                        <div class="empty-state-icon mb-3">
                            <i class="fas fa-clipboard-list text-muted" style="font-size: 60px; opacity: 0.2;"></i>
                        </div>
                        <h5 class="mb-1">Tidak ada data permintaan</h5>
                        <p class="text-muted mb-0">Tidak ada data permintaan yang sesuai dengan filter Anda.</p>
                    </div>`;
                return;
            }

            // Tambahkan item dengan animasi fade-in bertahap
            items.forEach((permintaan, index) => {
                const item = document.createElement('div');
                item.className = 'permintaan-item';
                item.style.opacity = '0';
                item.style.transform = 'translateY(10px)';
                item.style.animation = `fadeInUp 0.3s ease-out ${index * 0.05}s forwards`;

                // ✅ PERBAIKAN: Status badge class yang lebih akurat
                let statusBadgeClass = '';
                let statusText = '';

                switch (permintaan.auth.toLowerCase()) {
                    case 'diterima':
                        statusBadgeClass = 'diterima';
                        statusText = 'Diterima';
                        break;
                    case 'ditolak':
                        statusBadgeClass = 'ditolak';
                        statusText = 'Ditolak';
                        break;
                    case 'menunggu':
                    default:
                        statusBadgeClass = 'menunggu';
                        statusText = 'Menunggu';
                        break;
                }

                // ✅ PERBAIKAN: Action buttons yang berbeda berdasarkan status
                let actionButtons = `
                        <button class="btn btn-sm btn-info me-1" onclick="showDetail(${permintaan.id})" title="Lihat Detail"> 
                            <i class="fas fa-eye me-md-1"></i><span class="d-none d-md-inline">Detail</span>
                        </button>
                    `;

                // ✅ SIMPLE: Hanya tampilkan tombol terima/tolak untuk status "menunggu"
                if (permintaan.auth.toLowerCase() === 'menunggu') {
                    actionButtons += `
                    <button class="btn btn-sm btn-success me-1" onclick="acceptRequest(${permintaan.id})" title="Terima Permintaan">
                        <i class="fas fa-check me-md-1"></i><span class="d-none d-md-inline">Terima</span>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="rejectRequest(${permintaan.id})" title="Tolak Permintaan">
                        <i class="fas fa-times me-md-1"></i><span class="d-none d-md-inline">Tolak</span>
                    </button>
                `;
                }

                item.innerHTML = `
                <div class="mahasiswa-info">
                    <h6 class="nama">${permintaan.mahasiswa.name}</h6>
                    <p class="nim">NIM: ${permintaan.mahasiswa.nim}</p>
                </div>

                <div class="posisi">
                    <span class="job-title font-weight-bold">${permintaan.judul_lowongan}</span>
                </div>

                <div class="perusahaan">
                    <span class="company-badge font-weight-bold">
                        ${permintaan.perusahaan.nama_perusahaan}
                    </span>
                </div>

                <div class="status">
                    <span class="status-badge ${statusBadgeClass}">
                        ${statusText}
                    </span>
                </div>

                <div class="action">
                    <div class="hover-actions">
                        ${actionButtons}
                    </div>
                </div>
            `;

                permintaanList.appendChild(item);
            });

            // Tambahkan keyframes untuk animasi jika belum ada
            if (!document.getElementById('fadeInUp-animation')) {
                const style = document.createElement('style');
                style.id = 'fadeInUp-animation';
                style.textContent = `
                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(10px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `;
                document.head.appendChild(style);
            }
        }

        // Fungsi untuk memuat opsi perusahaan di dropdown
        function loadPerusahaanOptions(data) {
            // Kumpulkan nama perusahaan unik
            const uniquePerusahaan = [];
            data.forEach(item => {
                const perusahaanName = item.perusahaan.nama_perusahaan;
                if (perusahaanName && !uniquePerusahaan.some(p => p === perusahaanName)) {
                    uniquePerusahaan.push(perusahaanName);
                }
            });

            // Urutkan perusahaan berdasarkan abjad
            uniquePerusahaan.sort();

            // Dapatkan elemen dropdown
            const dropdownMenu = document.getElementById('perusahaanDropdown');
            if (!dropdownMenu) return;

            // Kosongkan dropdown kecuali opsi "Semua Perusahaan"
            while (dropdownMenu.children.length > 1) {
                dropdownMenu.removeChild(dropdownMenu.lastChild);
            }

            // Tambahkan setiap perusahaan ke dropdown
            uniquePerusahaan.forEach(perusahaan => {
                const li = document.createElement('li');
                const a = document.createElement('a');
                a.classList.add('dropdown-item');
                a.href = '#';
                a.dataset.perusahaanName = perusahaan;
                a.textContent = perusahaan;

                a.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Perbarui teks pada tombol dropdown
                    document.querySelector('#dropdownPerusahaan span').textContent = perusahaan;

                    // Tandai item ini sebagai aktif
                    document.querySelectorAll('#perusahaanDropdown .dropdown-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Filter data berdasarkan perusahaan yang dipilih
                    applyFilters();
                });

                li.appendChild(a);
                dropdownMenu.appendChild(li);
            });

            // Tambahkan event listener untuk opsi "Semua Perusahaan"
            const allOption = dropdownMenu.querySelector('[data-perusahaan-id="all"]');
            if (allOption) {
                allOption.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Reset teks tombol dropdown
                    document.querySelector('#dropdownPerusahaan span').textContent = 'Perusahaan';

                    // Tandai item ini sebagai aktif
                    document.querySelectorAll('#perusahaanDropdown .dropdown-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Tampilkan semua data
                    applyFilters();
                });
            }
        }

        // Fungsi untuk menerapkan semua filter yang aktif
        function applyFilters() {
            if (!Array.isArray(allPermintaanData) || allPermintaanData.length === 0) {
                console.warn("Data belum dimuat - tidak dapat melakukan pencarian");
                return;
            }
            // Ambil nilai pencarian
            const searchTerm = document.querySelector('.search-input').value.toLowerCase().trim();

            // Ambil filter perusahaan yang aktif
            const selectedPerusahaan = document.querySelector('#dropdownPerusahaan span').textContent;
            const isPerusahaanFilterActive = selectedPerusahaan !== 'Perusahaan';

            // Filter data berdasarkan kedua kriteria
            const filteredData = allPermintaanData.filter(permintaan => {
                // Filter berdasarkan pencarian
                const matchesSearch = !searchTerm ||
                    (permintaan.judul_lowongan?.toLowerCase() || '').includes(searchTerm) ||
                    (permintaan.perusahaan?.nama_perusahaan?.toLowerCase() || '').includes(searchTerm) ||
                    (permintaan.mahasiswa?.name?.toLowerCase() || '').includes(searchTerm) ||
                    (String(permintaan.mahasiswa?.nim || '')).toLowerCase().includes(searchTerm);

                // Filter berdasarkan perusahaan
                const matchesPerusahaan = !isPerusahaanFilterActive ||
                    permintaan.perusahaan.nama_perusahaan === selectedPerusahaan;

                // Item harus memenuhi kedua kondisi
                return matchesSearch && matchesPerusahaan;
            });

            // Tampilkan hasil filter
            renderPermintaanList(filteredData);
        }

        // Tambahkan event listener setelah DOM di-load
        document.addEventListener('DOMContentLoaded', function() {
            // Load data permintaan
            loadPermintaanData();

            // Tambahkan event listener untuk input pencarian
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {

                // Pastikan event listener ditambahkan hanya sekali
                searchInput.removeEventListener('input', handleSearchInput);
                searchInput.addEventListener('input', handleSearchInput);

                // Gunakan fungsi bernama agar dapat dihapus
                function handleSearchInput() {
                    clearTimeout(this.debounceTimer);

                    // Tambahkan indikator pencarian
                    const searchIcon = document.querySelector('.search-icon');
                    if (searchIcon) {
                        searchIcon.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
                    }

                    this.debounceTimer = setTimeout(() => {
                        applyFilters();

                        // Kembalikan ikon pencarian
                        if (searchIcon) {
                            searchIcon.innerHTML = '<i class="bi bi-search"></i>';
                        }
                    }, 300);
                }
            } else {
                console.error("Search input tidak ditemukan!");
            }

            const searchIcon = document.querySelector('.search-icon');
            if (searchIcon) {
                searchIcon.addEventListener('click', applyFilters);
            }
        });

        function getMahasiswaCV(id_mahasiswa) {
            return fetch(`/api/mahasiswa/${id_mahasiswa}/cv`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        return data.cv_data;
                    } else {
                        throw new Error(data.message || 'CV tidak ditemukan');
                    }
                });
        }



        function showDetail(id) {
            const detailModalBody = document.querySelector('#detailModal .modal-body');
            detailModalBody.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary mb-2" role="status"></div>
            <p class="mb-0">Memuat detail permintaan...</p>
        </div>
    `;

            // Tampilkan modal dengan loading state
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();

            fetch(`/api/magang/${id}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(response => {
                    if (response.success) {
                        const data = response.data;
                        if (data.dokumen && data.dokumen.length > 0) {
                            renderDetailModalContent(data);
                        } else {
                            const dokumenHTML = `
                                <div class="text-center py-3">
                                    <i class="fas fa-file-excel text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                    <p class="text-muted mb-0">Mahasiswa belum mengupload CV</p>
                                </div>
                            `;
                            data.dokumenHTML = dokumenHTML;
                            renderDetailModalContent(data);
                        }
                    } else {
                        detailModalBody.innerHTML = `
                    <div class="alert alert-danger">
                        Gagal memuat detail: ${response.message || 'Terjadi kesalahan.'}
                    </div>
                `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    detailModalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Gagal memuat detail permintaan magang. 
                    <br><small class="text-muted">Error: ${error.message}</small>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="showDetail(${id})">
                            <i class="fas fa-redo me-1"></i>Coba Lagi
                        </button>
                    </div>
                </div>
            `;
                });
        }

        // Fungsi untuk merender konten modal detail
        function renderDetailModalContent(data) {
            const detailModalBody = document.querySelector('#detailModal .modal-body');

            // Perbaiki title modal
            document.querySelector('#detailModal .modal-title').innerText =
                `Detail Permintaan - ${data.lowongan?.judul_lowongan || 'Lowongan'}`;

            // Tambahkan informasi mahasiswa, perusahaan dan lowongan di bagian atas
            const headerInfoHTML = `
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Informasi Mahasiswa</h6>
                        <h5 class="mb-1">${data.mahasiswa.name || 'Nama tidak tersedia'}</h5>
                        <p class="mb-1"><strong>NIM:</strong> ${data.mahasiswa.nim || 'NIM tidak tersedia'}</p>
                        <p class="mb-1"><strong>Email:</strong> ${data.mahasiswa.email || 'Email tidak tersedia'}</p>
                        <p class="mb-0"><strong>Program Studi:</strong> ${data.mahasiswa.prodi || 'Teknologi Informasi'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Informasi Lowongan</h6>
                        <h5 class="mb-1">${data.lowongan.judul_lowongan || 'Judul lowongan tidak tersedia'}</h5>
                        <p class="mb-1"><strong>Perusahaan:</strong> ${data.perusahaan.nama_perusahaan || 'Nama perusahaan tidak tersedia'}</p>
                        <p class="mb-1"><strong>Tanggal Lamaran:</strong> ${data.tanggal_lamaran || 'Tanggal tidak tersedia'}</p>
                        <p class="mb-0">
                            <span class="badge ${data.auth === 'menunggu' ? 'bg-warning' : 
                                (data.auth === 'diterima' ? 'bg-success' : 'bg-danger')}">
                                ${data.status || 'Status tidak tersedia'}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Render dokumen HTML (dari dokumen yang sudah diambil di controller)
            let dokumenHTML = '';

            if (data.dokumen && data.dokumen.length > 0) {
                // Jika ada dokumen, buat HTML untuk setiap dokumen
                dokumenHTML = data.dokumen.map(doc => `
                    <div class="document-item border rounded p-3 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="document-info flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <strong class="document-type">${doc.description}</strong>
                                </div>
                                <p class="file-name mb-1">${doc.file_name}</p>
                                <div class="file-meta mt-1">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>${doc.upload_date}
                                    </small>
                                    ${doc.file_size ? `
                                                                            <small class="text-muted ms-3">
                                                                                <i class="fas fa-weight me-1"></i>${doc.file_size}
                                                                            </small>
                                                                        ` : ''}
                                </div>
                            </div>
                            <div class="document-actions">
                                <a href="${doc.file_url}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Lihat Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else if (data.dokumenHTML) {
                // Jika tidak ada dokumen tapi ada dokumenHTML yang telah disiapkan
                dokumenHTML = data.dokumenHTML;
            } else {
                // Jika tidak ada dokumen sama sekali
                dokumenHTML = `
                    <div class="text-center py-3">
                        <i class="fas fa-file-excel text-muted mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                        <p class="text-muted mb-0">Mahasiswa belum mengupload CV</p>
                    </div>
                `;
            }

            // Tampilkan header info dan dokumen
            detailModalBody.innerHTML = `
        ${headerInfoHTML}
        <div class="row">
            <div class="col-12 mb-4">
                <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                    <i class="fas fa-file-alt me-2"></i>Dokumen Lamaran
                </h6>
                ${dokumenHTML}
            </div>
        </div>
    `;

            // ✅ UPDATE: Tambahkan sisa konten dari kode asli Anda
            // (Menambahkan kembali bagian yang sudah ada di kode Anda sebelumnya)

            // Tambahkan bagian skills mahasiswa jika ada
            if (data.mahasiswa.skills && data.mahasiswa.skills.length > 0) {
                const skillsHTML = data.mahasiswa.skills.map(skill =>
                    `<span class="badge bg-primary text-white me-1 mb-1" title="Pengalaman: ${skill.lama_skill}">
                ${skill.nama_skill}
            </span>`
                ).join('');

                detailModalBody.innerHTML += `
            <div class="row">
                <div class="col-12 mb-4">
                    <h6 class="text-uppercase text-muted mb-3 border-bottom pb-2">
                        <i class="fas fa-code me-2"></i>Keahlian
                    </h6>
                    <div class="skills-container">
                        ${skillsHTML}
                    </div>
                </div>
            </div>
        `;
            }

            // Tambahkan tombol aksi jika status menunggu
            if (data.auth === 'menunggu') {
                detailModalBody.innerHTML += `
            <div class="text-end mt-4 pt-3 border-top">
                <button type="button" class="btn btn-danger me-2" onclick="rejectRequest(${data.id})">
                    <i class="fas fa-times me-2"></i>Tolak Lamaran
                </button>
                <button type="button" class="btn btn-success" onclick="acceptRequest(${data.id})">
                    <i class="fas fa-check me-2"></i>Terima Lamaran
                </button>
            </div>
        `;
            }
        }

        // Fungsi acceptRequest (tetap sama)
        function acceptRequest(id) {
            fetch(`/api/magang/${id}/check-dosen`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(response => {
                    if (response.has_dosen) {
                        proceedWithAcceptance(id);
                    } else {
                        Swal.fire({
                            title: 'Tidak Dapat Menerima!',
                            text: 'Magang tidak dapat diterima karena belum memiliki dosen pembimbing.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ke Plotting Dosen',
                            cancelButtonText: 'Tutup'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '/plotting';
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan saat memeriksa data dosen pembimbing.', 'error');
                });
        }

        // Perbaikan fungsi proceedWithAcceptance
        function proceedWithAcceptance(id) {
            Swal.fire({
                title: 'Tentukan Jadwal Magang',
                html: `
            <div class="text-start">
                <p class="mb-3">Silakan tentukan periode magang:</p>
                
                <div class="mb-3">
                    <label for="tgl_mulai" class="form-label fw-bold">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" required>
                    <small class="text-muted">Tanggal mahasiswa mulai magang</small>
                </div>

                <div class="mb-3">
                    <label for="tgl_selesai" class="form-label fw-bold">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai" required>
                    <small class="text-muted">Tanggal selesai magang (3-6 bulan)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Durasi Magang</label>
                    <p class="duration-info mb-0 text-primary">-</p>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        - Minimal durasi magang 3 bulan (90 hari)<br>
                        - Maksimal durasi magang 6 bulan (180 hari)
                    </small>
                </div>
            </div>
        `,
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Konfirmasi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6c757d',
                width: '500px',
                didOpen: () => {
                    // Set default dates
                    const today = new Date();
                    const nextWeek = new Date(today);
                    nextWeek.setDate(today.getDate() + 7);

                    const threeMonths = new Date(nextWeek);
                    threeMonths.setMonth(nextWeek.getMonth() + 3);

                    // Format dates to YYYY-MM-DD
                    const formatDate = (date) => date.toISOString().split('T')[0];

                    // Set initial values
                    const tglMulai = document.getElementById('tgl_mulai');
                    const tglSelesai = document.getElementById('tgl_selesai');

                    tglMulai.value = formatDate(nextWeek);
                    tglSelesai.value = formatDate(threeMonths);
                    tglMulai.min = formatDate(today);

                    // Update duration info
                    const updateDuration = () => {
                        const start = new Date(tglMulai.value);
                        const end = new Date(tglSelesai.value);
                        const diffTime = Math.abs(end - start);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        const months = Math.floor(diffDays / 30);

                        document.querySelector('.duration-info').innerHTML =
                            `${diffDays} hari (± ${months} bulan)`;

                        // Validate duration
                        if (diffDays < 90 || diffDays > 180) {
                            document.querySelector('.duration-info').classList.add('text-danger');
                            document.querySelector('.duration-info').classList.remove('text-primary');
                        } else {
                            document.querySelector('.duration-info').classList.add('text-primary');
                            document.querySelector('.duration-info').classList.remove('text-danger');
                        }
                    };

                    // Add event listeners
                    tglMulai.addEventListener('change', function() {
                        const start = new Date(this.value);
                        const minEnd = new Date(start);
                        minEnd.setDate(start.getDate() + 1);

                        tglSelesai.min = formatDate(minEnd);
                        updateDuration();
                    });

                    tglSelesai.addEventListener('change', updateDuration);

                    // Initial duration calculation
                    updateDuration();
                },
                preConfirm: () => {
                    const tglMulai = document.getElementById('tgl_mulai').value;
                    const tglSelesai = document.getElementById('tgl_selesai').value;

                    if (!tglMulai || !tglSelesai) {
                        Swal.showValidationMessage('Harap isi kedua tanggal');
                        return false;
                    }

                    const start = new Date(tglMulai);
                    const end = new Date(tglSelesai);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    if (diffDays < 90) {
                        Swal.showValidationMessage('Durasi magang minimal 3 bulan (90 hari)');
                        return false;
                    }

                    if (diffDays > 180) {
                        Swal.showValidationMessage('Durasi magang maksimal 6 bulan (180 hari)');
                        return false;
                    }

                    return {
                        tglMulai,
                        tglSelesai,
                        diffDays
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        tglMulai,
                        tglSelesai,
                        diffDays
                    } = result.value;

                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Konfirmasi Jadwal',
                        html: `
                    <div class="text-start">
                        <p class="mb-3">Pastikan jadwal magang berikut sudah benar:</p>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <strong>Mulai:</strong><br>
                                        <span class="text-primary">${new Date(tglMulai).toLocaleDateString('id-ID', { 
                                            weekday:'long', 
                                            year:'numeric', 
                                            month:'long', 
                                            day:'numeric'
                                        })}</span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Selesai:</strong><br>
                                        <span class="text-primary">${new Date(tglSelesai).toLocaleDateString('id-ID', {
                                            weekday:'long', 
                                            year:'numeric', 
                                            month:'long', 
                                            day:'numeric'
                                        })}</span>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <strong>Durasi: ${diffDays} hari</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Proses',
                        cancelButtonText: 'Ubah Jadwal',
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6c757d'
                    }).then((finalResult) => {
                        if (finalResult.isConfirmed) {
                            processAcceptanceWithDates(id, tglMulai, tglSelesai);
                        } else if (finalResult.dismiss === Swal.DismissReason.cancel) {
                            proceedWithAcceptance(id); // Kembali ke form tanggal
                        }
                    });
                }
            });
        }

        // Fungsi processAcceptanceWithDates
        function processAcceptanceWithDates(id, tglMulai, tglSelesai) {
            // Show loading state
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memproses permintaan dan menjadwalkan magang...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send request to server
            fetch(`/api/magang/${id}/accept`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id_lowongan: id,
                        status: 'aktif',
                        tgl_mulai: tglMulai,
                        tgl_selesai: tglSelesai
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    console.log('Respons dari server:', response);
                    if (response.success) {
                        // Close any open modals first
                        const detailModal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                        if (detailModal) {
                            detailModal.hide();
                        }

                        // Show success message
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                            <div class="text-start">
                                <p class="mb-3">✅ Permintaan magang telah diterima</p>
                                <p class="mb-3">📅 Jadwal magang telah ditetapkan</p>
                                <p class="mb-0">📨 Notifikasi telah dikirim ke mahasiswa</p>
                            </div>
                        `,
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            loadPermintaanData(); // Refresh data
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: response.message || 'Terjadi kesalahan saat menerima permintaan.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memproses permintaan: ' + error.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        }

        // Fungsi rejectRequest juga perlu dipastikan tersedia
        function rejectRequest(id) {
            // Dialog konfirmasi yang konsisten
            Swal.fire({
                title: 'Tolak Permintaan Magang?',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Apakah Anda yakin ingin menolak permintaan magang ini?</p>

                        <div class="mb-3">
                            <label for="catatan_penolakan" class="form-label fw-bold">Catatan Penolakan (Opsional)</label>
                            <textarea class="form-control" id="catatan_penolakan" name="catatan_penolakan" rows="3" 
                                    placeholder="Berikan alasan penolakan untuk memberikan feedback kepada mahasiswa..."></textarea>
                            <small class="text-muted">Catatan ini akan dikirim kepada mahasiswa sebagai feedback</small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <small><strong>Catatan:</strong> Status permintaan akan diubah menjadi "Ditolak" dan mahasiswa akan menerima notifikasi penolakan.</small>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Tolak Permintaan',
                cancelButtonText: 'Batal',
                width: '500px',
                customClass: {
                    htmlContainer: 'text-start'
                },
                preConfirm: () => {
                    const catatan = document.getElementById('catatan_penolakan').value.trim();
                    return {
                        catatan_penolakan: catatan || null
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        catatan_penolakan
                    } = result.value;

                    // Tampilkan loading
                    Swal.fire({
                        title: 'Memproses Penolakan...',
                        text: 'Sedang mengubah status permintaan menjadi ditolak...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim request untuk update status
                    fetch(`/api/magang/${id}/reject`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute(
                                        'content')
                            },
                            body: JSON.stringify({
                                auth: 'ditolak',
                                catatan: catatan_penolakan,
                                tanggal_ditolak: new Date().toISOString().split('T')[0]
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(response => {
                            console.log('Respons dari server:', response);
                            if (response.success) {
                                Swal.fire({
                                    title: 'Permintaan Ditolak!',
                                    html: `
                                        <div class="text-start">
                                            <p class="mb-3">✅ Status permintaan telah diubah menjadi "Ditolak"</p>
                                            <p class="mb-3">📨 Notifikasi penolakan telah dikirim ke mahasiswa</p>
                                            ${catatan_penolakan ? `<p class="mb-0">📝 Catatan penolakan: "${catatan_penolakan}"</p>` : ''}
                                        </div>
                                    `,
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadPermintaanData
                                        (); // Refresh data untuk menampilkan status baru
                                });
                            } else {
                                Swal.fire('Gagal!', response.message ||
                                    'Terjadi kesalahan saat menolak permintaan.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat memproses permintaan: ' +
                                error.message,
                                'error');
                        });
                }
            });
        }

        // Fungsi reactivateRequest
        function reactivateRequest(id) {
            // ...kode yang sudah ada...
        }
    </script>
@endpush
