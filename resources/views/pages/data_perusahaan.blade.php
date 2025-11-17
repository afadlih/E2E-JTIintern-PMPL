<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\data_perusahaan.blade.php -->
@extends('layouts.app',  ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Perusahaan'])
    <div class="container-fluid py-4">
        <div class="search-header mb-4">
            <div class="card p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="search-filters d-flex gap-3">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Cari Perusahaan" id="searchPerusahaan">
                            <i class="bi bi-search"></i>
                        </div>
                        <div class="dropdown">
                            <button class="filter-btn dropdown-toggle" id="filterWilayah" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-geo-alt"></i>
                                <span>Wilayah</span>
                            </button>
                            <ul class="dropdown-menu" id="wilayahDropdown">
                                <li><a class="dropdown-item" href="#" data-wilayah-id="">Semua Wilayah</a></li>
                                <!-- Wilayah akan dimuat di sini -->
                            </ul>
                        </div>
                    </div>
                    <div class="action-buttons d-flex gap-3">
                        <button type="button" class="btn" style="color: white; background: #02A232;"
                            onclick="tambahPerusahaan()">
                            <i class="bi bi-plus-square-fill me-2"></i>Tambah Perusahaan
                        </button>
                        <button type="button" class="btn" style="color: white; background: #5988FF;" onclick="importCSV()">
                            <i class="bi bi-file-earmark-arrow-up me-2"></i>Import CSV
                        </button>
                        <button type="button" class="btn" style="color: white; background: #5988FF;" onclick="exportPDF()">
                            <i class="bi bi-file-pdf me-2"></i>Export PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="text-center py-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mt-3 text-primary">Memuat Data Perusahaan</h5>
            <p class="text-muted">Mohon tunggu sebentar...</p>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5 d-none">
            <div class="empty-state-icon mb-4">
                <i class="bi bi-building" style="font-size: 3rem; opacity: 0.5;"></i>
            </div>
            <h5 class="mb-2">Tidak ada data perusahaan</h5>
            <p class="text-muted mb-4">Tambahkan perusahaan baru untuk mulai mengelola data</p>
            <button type="button" class="btn" style="color: white; background: #02A232;" onclick="tambahPerusahaan()">
                <i class="bi bi-plus-square-fill me-2"></i>Tambah Perusahaan
            </button>
        </div>

        <!-- Error State -->
        <div id="errorState" class="text-center py-5 d-none">
            <div class="error-state-icon mb-4">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
            </div>
            <h5 class="text-danger mb-2">Gagal Memuat Data</h5>
            <p class="text-muted mb-4" id="errorMessage">Terjadi kesalahan saat memuat data perusahaan</p>
            <button class="btn btn-primary" onclick="loadPerusahaanData()">
                <i class="bi bi-arrow-clockwise me-2"></i>Coba Lagi
            </button>
        </div>

        <!-- Data Container -->
        <div class="perusahaan-grid" id="dataContainer">
            <div class="row g-4" id="perusahaanContainer">
                <!-- Data will be loaded here -->
            </div>
        </div>

        <!-- Modal Tambah Perusahaan -->
        <div class="modal fade" id="tambahPerusahaanModal" tabindex="-1" aria-labelledby="tambahPerusahaanModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahPerusahaanModalLabel">Tambah Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="tambahPerusahaanForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_perusahaan" class="form-label">Nama Perusahaan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="wilayah_id" class="form-label">Wilayah <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="wilayah_id" name="wilayah_id" required>
                                        <option value="">Pilih Wilayah</option>
                                        <!-- Wilayah akan dimuat di sini -->
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat_perusahaan" class="form-label">Alamat Perusahaan</label>
                                <input type="text" class="form-control" id="alamat_perusahaan" name="alamat_perusahaan">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_person" class="form-label">Contact Person <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="contact_person" name="contact_person"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="instagram" name="instagram"
                                            placeholder="username">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="website" class="form-label">Website</label>
                                    <input type="text" class="form-control" id="website" name="website"
                                        placeholder="https://example.com">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Perusahaan</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="gmaps" class="form-label">Link Google Maps</label>
                                <input type="text" class="form-control" id="gmaps" name="gmaps"
                                    placeholder="https://goo.gl/maps/...">
                            </div>

                            <div class="mb-4">
                                <label for="logo" class="form-label">Logo Perusahaan</label>
                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                <small class="form-text text-muted">Format: JPG, PNG, SVG. Maks: 2MB</small>
                                <div id="logoPreview" class="mt-2 d-none">
                                    <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
                                </div>
                            </div>

                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" id="btnSimpan">
                                    <i class="bi bi-save me-1"></i>Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Perusahaan -->
        <div class="modal fade" id="editPerusahaanModal" tabindex="-1" aria-labelledby="editPerusahaanModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPerusahaanModalLabel">Edit Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPerusahaanForm" enctype="multipart/form-data">
                            <input type="hidden" id="edit_perusahaan_id" name="perusahaan_id">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_nama_perusahaan" class="form-label">Nama Perusahaan <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_nama_perusahaan" name="nama_perusahaan"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_wilayah_id" class="form-label">Wilayah <span
                                            class="text-danger">*</span></label>
                                    <select class="form-control" id="edit_wilayah_id" name="wilayah_id" required>
                                        <option value="">Pilih Wilayah</option>
                                        <!-- Wilayah akan dimuat di sini -->
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_alamat_perusahaan" class="form-label">Alamat Perusahaan</label>
                                <input type="text" class="form-control" id="edit_alamat_perusahaan"
                                    name="alamat_perusahaan">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_contact_person" class="form-label">Contact Person <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_contact_person" name="contact_person"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_email" class="form-label">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edit_instagram" class="form-label">Instagram</label>
                                    <div class="input-group">
                                        <span class="input-group-text">@</span>
                                        <input type="text" class="form-control" id="edit_instagram" name="instagram"
                                            placeholder="username">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="edit_website" class="form-label">Website</label>
                                    <input type="text" class="form-control" id="edit_website" name="website"
                                        placeholder="https://example.com">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="edit_deskripsi" class="form-label">Deskripsi Perusahaan</label>
                                <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="edit_gmaps" class="form-label">Link Google Maps</label>
                                <input type="text" class="form-control" id="edit_gmaps" name="gmaps"
                                    placeholder="https://goo.gl/maps/...">
                            </div>

                            <div class="mb-4">
                                <label for="edit_logo" class="form-label">Logo Perusahaan</label>
                                <div class="d-flex align-items-center mb-2">
                                    <div id="currentLogoPreview" class="me-3">
                                        <!-- Current logo preview will be displayed here -->
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*">
                                    </div>
                                </div>
                                <small class="form-text text-muted">Format: JPG, PNG, SVG. Maks: 2MB. Kosongkan jika tidak
                                    ingin mengubah logo.</small>
                                <div id="editLogoPreview" class="mt-2 d-none">
                                    <img src="" alt="Preview" class="img-thumbnail" style="height: 100px;">
                                </div>
                            </div>

                            <div class="modal-footer px-0 pb-0">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary" id="btnUpdate">
                                    <i class="bi bi-save me-1"></i>Perbarui
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Import CSV -->
        <div class="modal fade" id="importCSVModal" tabindex="-1" aria-labelledby="importCSVModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form id="importCSVForm" enctype="multipart/form-data">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="importCSVModalLabel">Import Data Perusahaan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Format File:</strong> Silakan gunakan file dengan format berikut:
                                <ul class="mb-0 mt-2">
                                    <li><strong>Kolom Wajib:</strong> nama_perusahaan, contact_person, email, dan
                                        wilayah/wilayah_id</li>
                                    <li><strong>Kolom Opsional:</strong> alamat_perusahaan, instagram, website, deskripsi,
                                        gmaps</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-primary btn-sm mb-3"
                                    onclick="downloadTemplate()">
                                    <i class="bi bi-download me-1"></i>Download Template
                                </button>
                                <label for="csvFile" class="form-label">Pilih File (CSV, Excel)</label>
                                <input type="file" id="csvFile" name="csv_file" class="form-control"
                                    accept=".csv,.xls,.xlsx" required>
                                <div class="form-text text-muted">
                                    Format yang didukung: CSV, Excel (.xls, .xlsx). Maksimal 2MB.
                                </div>
                            </div>

                            <div class="alert alert-warning small mb-0">
                                <strong>Catatan untuk kolom Wilayah:</strong>
                                <ul class="mb-0 mt-1">
                                    <li>Gunakan nama wilayah atau ID wilayah yang sudah ada di sistem</li>
                                    <li>Perusahaan tidak akan diimpor jika wilayah tidak valid</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload me-1"></i>Import
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection

    @push('css')
        <link rel="stylesheet" href="{{ asset('assets/css/data_perusahaan.css') }}">
        <style>
            .company-card {
                transition: all 0.3s ease;
                border-radius: 10px;
            }

            .company-card:hover {
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                transform: translateY(-5px);
            }

            .company-logo {
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px;
                overflow: hidden;
                background-color: #f8f9fa;
            }

            .company-actions {
                display: flex;
                gap: 5px;
                position: absolute;
                top: 10px;
                right: 10px;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .company-card:hover .company-actions {
                opacity: 1;
            }

            .action-btn {
                width: 32px;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.9);
                border: 1px solid #dee2e6;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .action-btn:hover {
                background: #fff;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            }

            .action-btn.edit:hover {
                color: #0d6efd;
            }

            .action-btn.delete:hover {
                color: #dc3545;
            }

            /* Animation */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .fade-in {
                animation: fadeIn 0.5s ease-out forwards;
            }

            /* Loading spinner */
            .spinner-border {
                animation: spinner-border 1s linear infinite;
            }

            /* Make image previews consistent */
            #logoPreview img,
            #editLogoPreview img,
            #currentLogoPreview img {
                object-fit: contain;
                background-color: #f8f9fa;
                border: 1px solid #dee2e6;
            }

            #currentLogoPreview {
                width: 80px;
                height: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border-radius: 6px;
                border: 1px dashed #dee2e6;
                background-color: #f8f9fa;
            }

            #currentLogoPreview img {
                max-width: 100%;
                max-height: 100%;
            }
        </style>
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Variable global untuk menyimpan data perusahaan
            let perusahaanData = [];

            // Load data when page loads
            document.addEventListener('DOMContentLoaded', function () {
                loadWilayahOptions();
                loadPerusahaanData();

                // Tambahkan event listener untuk search box
                document.getElementById('searchPerusahaan').addEventListener('input', filterPerusahaan);

                // Set filter wilayah aktif untuk tracking
                window.activeFilters = {
                    wilayah: '',
                    search: ''
                };

                // Preview logo saat file dipilih untuk tambah perusahaan
                document.getElementById('logo').addEventListener('change', function (e) {
                    previewImage(this, 'logoPreview');
                });

                // Preview logo saat file dipilih untuk edit perusahaan
                document.getElementById('edit_logo').addEventListener('change', function (e) {
                    previewImage(this, 'editLogoPreview');
                });
            });

            // Fungsi untuk preview gambar
            function previewImage(input, previewId) {
                const preview = document.getElementById(previewId);
                const previewImg = preview.querySelector('img');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        previewImg.src = e.target.result;
                        preview.classList.remove('d-none');
                    }

                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.classList.add('d-none');
                }
            }

            function loadWilayahOptions() {
                fetch('/api/wilayah')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Dropdown di form tambah perusahaan
                            const wilayahSelect = document.getElementById('wilayah_id');
                            // Dropdown di form edit perusahaan
                            const editWilayahSelect = document.getElementById('edit_wilayah_id');
                            // Dropdown di tombol filter wilayah
                            const wilayahDropdown = document.getElementById('wilayahDropdown');

                            // Kosongkan dropdown sebelum menambahkan data baru
                            wilayahSelect.innerHTML = '<option value="">Pilih Wilayah</option>';
                            editWilayahSelect.innerHTML = '<option value="">Pilih Wilayah</option>';
                            wilayahDropdown.innerHTML = '<li><a class="dropdown-item active" href="#" data-wilayah-id="">Semua Wilayah</a></li>';

                            // Tambahkan data wilayah ke kedua dropdown
                            data.data.forEach(wilayah => {
                                // Tambahkan ke dropdown form tambah
                                const option = document.createElement('option');
                                option.value = wilayah.wilayah_id;
                                option.textContent = wilayah.nama_kota;
                                wilayahSelect.appendChild(option);

                                // Tambahkan ke dropdown form edit
                                const editOption = document.createElement('option');
                                editOption.value = wilayah.wilayah_id;
                                editOption.textContent = wilayah.nama_kota;
                                editWilayahSelect.appendChild(editOption);

                                // Tambahkan ke dropdown filter wilayah
                                const li = document.createElement('li');
                                li.innerHTML = `<a class="dropdown-item" href="#" data-wilayah-id="${wilayah.wilayah_id}">${wilayah.nama_kota}</a>`;
                                wilayahDropdown.appendChild(li);
                            });

                            // Tambahkan event listener untuk setiap item di dropdown filter wilayah
                            wilayahDropdown.querySelectorAll('.dropdown-item').forEach(item => {
                                item.addEventListener('click', function (e) {
                                    e.preventDefault();

                                    // Update tampilan UI (aktifkan item yang dipilih)
                                    wilayahDropdown.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active'));
                                    this.classList.add('active');

                                    // Update text tombol filter
                                    const wilayahId = this.getAttribute('data-wilayah-id');
                                    const wilayahName = this.textContent.trim();
                                    document.querySelector('#filterWilayah span').textContent =
                                        wilayahId ? wilayahName : 'Wilayah';

                                    // Simpan filter aktif
                                    window.activeFilters.wilayah = wilayahId;

                                    // Filter data
                                    applyFilters();
                                });
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorMessage('Gagal memuat data wilayah');
                    });
            }

            function applyFilters() {
                let filteredData = [...perusahaanData]; // Salin array asli

                // Filter berdasarkan wilayah jika ada
                if (window.activeFilters.wilayah) {
                    filteredData = filteredData.filter(p => p.wilayah_id == window.activeFilters.wilayah);
                }

                // Filter berdasarkan pencarian jika ada
                const searchInput = window.activeFilters.search.toLowerCase();
                if (searchInput) {
                    filteredData = filteredData.filter(p =>
                        p.nama_perusahaan.toLowerCase().includes(searchInput) ||
                        p.wilayah.toLowerCase().includes(searchInput)
                    );
                }

                // Update grid dengan data terfilter
                updatePerusahaanGrid(filteredData);
            }

            // Update fungsi filterPerusahaan untuk menggunakan sistem filter terpadu
            function filterPerusahaan() {
                window.activeFilters.search = document.getElementById('searchPerusahaan').value;
                applyFilters();
            }

            function loadPerusahaanData() {
                // Tampilkan loading state
                document.getElementById('loadingState').classList.remove('d-none');
                document.getElementById('dataContainer').classList.add('d-none');
                document.getElementById('emptyState').classList.add('d-none');
                document.getElementById('errorState').classList.add('d-none');

                fetch('/api/perusahaan')
                    .then(response => response.json())
                    .then(data => {
                        // Sembunyikan loading state
                        document.getElementById('loadingState').classList.add('d-none');

                        if (data.success) {
                            perusahaanData = data.data; // Simpan data perusahaan ke variabel global

                            if (perusahaanData.length === 0) {
                                // Tampilkan empty state jika tidak ada data
                                document.getElementById('emptyState').classList.remove('d-none');
                            } else {
                                // Tampilkan container data dan update grid
                                document.getElementById('dataContainer').classList.remove('d-none');
                                updatePerusahaanGrid(perusahaanData); // Tampilkan semua data perusahaan
                            }
                        } else {
                            showErrorMessage(data.message || 'Gagal memuat data perusahaan');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showErrorMessage('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
                    });
            }

            function showErrorMessage(message) {
                document.getElementById('loadingState').classList.add('d-none');
                document.getElementById('dataContainer').classList.add('d-none');
                document.getElementById('emptyState').classList.add('d-none');
                document.getElementById('errorState').classList.remove('d-none');
                document.getElementById('errorMessage').textContent = message;
            }

            function updatePerusahaanGrid(perusahaan) {
                const grid = document.getElementById('perusahaanContainer');

                if (!perusahaan.length) {
                    grid.innerHTML = `
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Tidak ditemukan data perusahaan yang sesuai dengan filter yang dipilih.
                                        </div>
                                    </div>
                                `;
                    return;
                }

                grid.innerHTML = '';

                perusahaan.forEach((p, index) => {
                    const col = document.createElement('div');
                    col.className = 'col-md-4 mb-4';
                    col.style.opacity = '0';
                    col.style.animation = `fadeIn 0.5s ease-out forwards ${index * 0.1}s`;

                    col.innerHTML = `
                                    <div class="card company-card position-relative">
                                        <div class="company-actions">
                                            <button class="action-btn edit" title="Edit Perusahaan" onclick="event.stopPropagation(); editPerusahaan(${p.perusahaan_id})">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="action-btn delete" title="Hapus Perusahaan" onclick="event.stopPropagation(); deletePerusahaan(${p.perusahaan_id})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="card-body" onclick="goToDetail(${p.perusahaan_id})" style="cursor: pointer;">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="company-logo me-3">
                                                    ${p.logo
                            ? `<img src="/storage/${p.logo}" alt="${p.nama_perusahaan}" class="img-fluid" style="width: 50px; height: 50px; object-fit: contain;">`
                            : `<i class="bi bi-building" style="font-size: 2rem;"></i>`
                        }
                                                </div>
                                                <div>
                                                    <h6 class="company-name mb-1">${p.nama_perusahaan}</h6>
                                                    <div class="company-location">
                                                        <i class="bi bi-geo-alt"></i>
                                                        <span>${p.wilayah}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="vacancy-info">
                                                <p class="text-muted mb-1">Lowongan Terbuka</p>
                                                <div class="vacancy-count">
                                                    <i class="bi bi-briefcase"></i>
                                                    <span>${p.lowongan_count || 0} Lowongan</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;

                    grid.appendChild(col);
                });
            }

            function tambahPerusahaan() {
                // Reset form
                document.getElementById('tambahPerusahaanForm').reset();
                document.getElementById('logoPreview').classList.add('d-none');

                const modal = new bootstrap.Modal(document.getElementById('tambahPerusahaanModal'));
                modal.show();
            }

            function editPerusahaan(id) {
                // Reset form and preview
                document.getElementById('editPerusahaanForm').reset();
                document.getElementById('editLogoPreview').classList.add('d-none');

                // Tampilkan loading pada button
                const btnUpdate = document.getElementById('btnUpdate');
                const originalBtnText = btnUpdate.innerHTML;
                btnUpdate.disabled = true;
                btnUpdate.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memuat Data...';

                // Ambil data perusahaan untuk edit
                fetch(`/api/perusahaan/${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const perusahaan = data.data;

                            // Isi form dengan data perusahaan
                            document.getElementById('edit_perusahaan_id').value = perusahaan.perusahaan_id;
                            document.getElementById('edit_nama_perusahaan').value = perusahaan.nama_perusahaan;
                            document.getElementById('edit_alamat_perusahaan').value = perusahaan.alamat_perusahaan || '';
                            document.getElementById('edit_wilayah_id').value = perusahaan.wilayah_id;
                            document.getElementById('edit_contact_person').value = perusahaan.contact_person || '';
                            document.getElementById('edit_email').value = perusahaan.email || '';
                            document.getElementById('edit_instagram').value = perusahaan.instagram || '';
                            document.getElementById('edit_website').value = perusahaan.website || '';
                            document.getElementById('edit_deskripsi').value = perusahaan.deskripsi || '';
                            document.getElementById('edit_gmaps').value = perusahaan.gmaps || '';

                            // Tampilkan preview logo jika ada
                            const currentLogoPreview = document.getElementById('currentLogoPreview');
                            if (perusahaan.logo) {
                                currentLogoPreview.innerHTML = `<img src="/storage/${perusahaan.logo}" alt="Logo" style="max-width: 100%; max-height: 100%;">`;
                            } else {
                                currentLogoPreview.innerHTML = `<i class="bi bi-building" style="font-size: 2rem; color: #adb5bd;"></i>`;
                            }

                            // Tampilkan modal edit
                            const modal = new bootstrap.Modal(document.getElementById('editPerusahaanModal'));
                            modal.show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal memuat data perusahaan'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat memuat data perusahaan'
                        });
                    })
                    .finally(() => {
                        // Kembalikan button ke state semula
                        btnUpdate.disabled = false;
                        btnUpdate.innerHTML = originalBtnText;
                    });
            }

            function deletePerusahaan(id) {
                Swal.fire({
                    title: 'Hapus Perusahaan?',
                    text: "Data perusahaan dan semua lowongan terkait akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan loading
                        Swal.fire({
                            title: 'Menghapus...',
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Kirim request hapus
                        fetch(`/api/perusahaan/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: data.message || 'Perusahaan berhasil dihapus',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // Reload data perusahaan
                                    loadPerusahaanData();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: data.message || 'Gagal menghapus perusahaan'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Terjadi kesalahan saat menghapus perusahaan'
                                });
                            });
                    }
                });
            }

            // Fungsi untuk handle submit tambah perusahaan
            document.getElementById('tambahPerusahaanForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // Tampilkan loading di button
                const btnSimpan = document.getElementById('btnSimpan');
                const originalBtnText = btnSimpan.innerHTML;
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Menyimpan...';

                // Membuat FormData untuk menangani file upload
                const formData = new FormData(this);

                // Kirim data ke server
                fetch('/api/perusahaan', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message || 'Perusahaan berhasil ditambahkan',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Tutup modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('tambahPerusahaanModal'));
                            modal.hide();

                            // Reset form
                            this.reset();
                            document.getElementById('logoPreview').classList.add('d-none');

                            // Reload data perusahaan
                            loadPerusahaanData();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal menambahkan perusahaan'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan. Silakan coba lagi.'
                        });
                    })
                    .finally(() => {
                        // Kembalikan button ke state semula
                        btnSimpan.disabled = false;
                        btnSimpan.innerHTML = originalBtnText;
                    });
            });

            // Fungsi untuk handle submit edit perusahaan
            document.getElementById('editPerusahaanForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // Tampilkan loading di button
                const btnUpdate = document.getElementById('btnUpdate');
                const originalBtnText = btnUpdate.innerHTML;
                btnUpdate.disabled = true;
                btnUpdate.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Memperbarui...';

                // Ambil ID perusahaan
                const id = document.getElementById('edit_perusahaan_id').value;

                // Membuat FormData untuk menangani file upload
                const formData = new FormData(this);
                formData.append('_method', 'PUT'); // Untuk method spoofing di Laravel

                // Kirim data ke server
                fetch(`/api/perusahaan/${id}`, {
                    method: 'POST', // Tetap gunakan POST untuk FormData dengan method spoofing
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message || 'Perusahaan berhasil diperbarui',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            // Tutup modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editPerusahaanModal'));
                            modal.hide();

                            // Reload data perusahaan
                            loadPerusahaanData();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal memperbarui perusahaan'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan. Silakan coba lagi.'
                        });
                    })
                    .finally(() => {
                        // Kembalikan button ke state semula
                        btnUpdate.disabled = false;
                        btnUpdate.innerHTML = originalBtnText;
                    });
            });

            function importCSV() {
                const modal = new bootstrap.Modal(document.getElementById('importCSVModal'));
                modal.show();
            }

            function goToDetail(id) {
                window.location.href = `/detail-perusahaan/${id}`;
            }

            function downloadTemplate() {
                // Show loading state
                Swal.fire({
                    title: 'Membuat Template',
                    text: 'Sedang menyiapkan template...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch('/api/wilayah')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Create headers and data for CSV
                            const headers = [
                                'nama_perusahaan',
                                'alamat_perusahaan',
                                'wilayah_id',
                                'contact_person',
                                'email',
                                'instagram',
                                'website',
                                'deskripsi',
                                'gmaps'
                            ];

                            const row1 = [
                                'PT Contoh Perusahaan',
                                'Jl. Contoh No. 123, Jember',
                                data.data[0]?.wilayah_id || '1',
                                'John Doe',
                                'contact@example.com',
                                'company_instagram',
                                'https://www.example.com',
                                'Deskripsi singkat tentang perusahaan ini',
                                'https://goo.gl/maps/example'
                            ];

                            const row2 = [
                                'CV Perusahaan Lain',
                                'Alamat Perusahaan Lain',
                                data.data.length > 1 ? data.data[1].wilayah_id : '2',
                                'Jane Smith',
                                'contact@anotherdomain.com',
                                'another_company',
                                'https://www.anotherdomain.com',
                                'Deskripsi untuk perusahaan lain',
                                'https://goo.gl/maps/another'
                            ];

                            // Create CSV content with commas (not semicolons)
                            let csvContent = headers.join(',') + '\n';
                            csvContent += row1.map(cell => `"${cell}"`).join(',') + '\n';
                            csvContent += row2.map(cell => `"${cell}"`).join(',') + '\n';

                            // Create and download the file
                            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                            const url = URL.createObjectURL(blob);
                            const link = document.createElement('a');
                            link.href = url;
                            link.download = 'template_perusahaan.csv';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                            URL.revokeObjectURL(url);

                            Swal.close();

                            // Show info about wilayah options
                            let wilayahHTML = '';
                            data.data.slice(0, 5).forEach(w => {
                                wilayahHTML += `<tr>
                                    <td>${w.nama_kota}</td>
                                    <td>${w.wilayah_id}</td>
                                </tr>`;
                            });

                            Swal.fire({
                                icon: 'success',
                                title: 'Template Berhasil Diunduh',
                                html: `
                                    <p>Template Excel berhasil diunduh. Gunakan template ini untuk persiapan data impor Anda.</p>
                                    <p class="mb-2"><strong>Beberapa contoh data wilayah:</strong></p>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nama Wilayah</th>
                                                    <th>ID Wilayah</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${wilayahHTML}
                                                ${data.data.length > 5 ? '<tr><td colspan="2" class="text-center">Dan lainnya...</td></tr>' : ''}
                                            </tbody>
                                        </table>
                                    </div>
                                    <p class="mt-2 small">Anda dapat menggunakan nama wilayah atau ID wilayah dalam file impor.</p>
                                `,
                                confirmButtonText: 'Mengerti'
                            });
                        } else {
                            Swal.fire('Gagal', 'Tidak dapat membuat template, gagal memuat data wilayah', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Terjadi kesalahan saat membuat template', 'error');
                    });
            }

            document.getElementById('importCSVForm').addEventListener('submit', function (e) {
                e.preventDefault();

                // Validate file
                const fileInput = document.getElementById('csvFile');
                if (!fileInput.files || fileInput.files.length === 0) {
                    Swal.fire('Error', 'Silakan pilih file terlebih dahulu', 'error');
                    return;
                }

                // Show loading in button
                const submitButton = this.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengimpor...';

                // Create form data
                const formData = new FormData(this);

                // Send request
                fetch('/api/perusahaan/import', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal and reset form
                            const modal = bootstrap.Modal.getInstance(document.getElementById('importCSVModal'));
                            modal.hide();
                            this.reset();

                            // If there are errors, show them in a detailed way
                            if (data.errors && data.errors.length > 0) {
                                let errorList = '';
                                data.errors.forEach(err => {
                                    errorList += `<li class="text-start">${err}</li>`;
                                });

                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Import Berhasil dengan Peringatan',
                                    html: `
                                <p>Berhasil mengimpor ${data.imported} data perusahaan.</p>
                                <p>Namun, terdapat ${data.errors.length} data yang tidak dapat diimpor:</p>
                                <ul class="ps-3">
                                    ${errorList}
                                </ul>
                            `,
                                    confirmButtonText: 'Mengerti'
                                });
                            } else {
                                // All successful
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Import Berhasil',
                                    text: `Berhasil mengimpor ${data.imported} data perusahaan.`,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            }

                            // Reload data
                            loadPerusahaanData();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Import Gagal',
                                text: data.message || 'Gagal mengimpor data'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat mengimpor data'
                        });
                    })
                    .finally(() => {
                        // Reset button state
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalText;
                    });
            });

            function exportPDF() {
                // Show loading state
                Swal.fire({
                    title: 'Generating PDF...',
                    text: 'Please wait while we generate your PDF',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Get current filters
                const params = new URLSearchParams();
                if (window.activeFilters?.wilayah) params.append('wilayah', window.activeFilters.wilayah);
                if (window.activeFilters?.search) params.append('search', window.activeFilters.search);

                // Make request to export endpoint
                fetch(`/api/perusahaan/export/pdf?${params.toString()}`)
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        const filename = response.headers.get('Content-Disposition')
                            ?.split('filename=')[1]
                            ?.replace(/"/g, '')
                            ?? `data_perusahaan_${new Date().getTime()}.pdf`;

                        return response.blob().then(blob => ({ blob, filename }));
                    })
                    .then(({ blob, filename }) => {
                        // Create download link
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                        Swal.close();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Export Failed',
                            text: error.message || 'Failed to generate PDF'
                        });
                    });
            }
        </script>
    @endpush