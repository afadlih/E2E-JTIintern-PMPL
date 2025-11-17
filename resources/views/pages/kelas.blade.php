<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\kelas.blade.php -->
@extends('layouts.app',  ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Kelas'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Daftar Kelas</h6>
                        <p class="text-sm text-secondary mb-0">
                            Manajemen data kelas untuk program studi
                        </p>
                    </div>
                    <button class="btn btn-sm btn-success" onclick="tambahKelas()">
                        <i class="fas fa-plus me-2"></i>Tambah Kelas
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Kelas</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Program Studi</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tahun Masuk</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="kelas-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Empty State -->
                <div id="empty-state" class="text-center py-5 d-none">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-building text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <h6 class="text-muted">Belum ada data kelas</h6>
                    <p class="text-xs text-secondary mb-3">
                        Silahkan tambahkan kelas baru
                    </p>
                    <button class="btn btn-sm btn-success" onclick="tambahKelas()">
                        <i class="fas fa-plus me-2"></i>Tambah Kelas
                    </button>
                </div>

                <!-- Error State -->
                <div id="error-state" class="text-center py-5 d-none">
                    <div class="error-state-icon mb-3">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-danger">Gagal memuat data</h6>
                    <p class="text-xs text-secondary mb-3" id="error-message">
                        Terjadi kesalahan saat memuat data kelas
                    </p>
                    <button class="btn btn-sm btn-primary" onclick="loadKelasData()">
                        <i class="fas fa-sync-alt me-2"></i>Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kelas -->
    <div class="modal fade" id="kelasModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kelasModalLabel">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="kelasForm" onsubmit="handleSubmitKelas(event)">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" required>
                            <div class="form-text">Contoh: TI-2A, SI-3B</div>
                        </div>
                        <div class="mb-3">
                            <label for="kode_prodi" class="form-label">Program Studi</label>
                            <select class="form-select" id="kode_prodi" required>
                                <option value="" disabled selected>Pilih Program Studi</option>
                                <!-- Options will be populated here -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                            <input type="number" class="form-control" id="tahun_masuk" min="2000" max="{{ date('Y') + 1 }}" required>
                            <div class="form-text">Tahun masuk angkatan</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Detail Modal -->
    <div class="modal fade" id="detailKelasModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="130">Nama Kelas</th>
                            <td><span id="detail-nama-kelas"></span></td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td><span id="detail-nama-prodi"></span></td>
                        </tr>
                        <tr>
                            <th>Kode Prodi</th>
                            <td><span id="detail-kode-prodi"></span></td>
                        </tr>
                        <tr>
                            <th>Tahun Masuk</th>
                            <td><span id="detail-tahun-masuk"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }
    
    .empty-state-icon, .error-state-icon {
        opacity: 0.5;
    }
    
    .table td, .table th {
        white-space: nowrap;
    }
</style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize axios instance
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true
        });

        // Fungsi untuk memuat data kelas dari API
        function loadKelasData() {
            const tableBody = document.getElementById('kelas-table-body');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-3">
                        <i class="fas fa-circle-notch fa-spin me-2"></i>Memuat data...
                    </td>
                </tr>
            `;
            
            document.getElementById('empty-state').classList.add('d-none');
            document.getElementById('error-state').classList.add('d-none');

            api.get('/kelas')
                .then(response => {
                    if (response.data.success) {
                        tableBody.innerHTML = '';
                        
                        if (response.data.data.length === 0) {
                            document.getElementById('empty-state').classList.remove('d-none');
                            return;
                        }

                        response.data.data.forEach((kelas, index) => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>
                                    <div class="d-flex px-3 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">${kelas.nama_kelas}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-normal mb-0">${kelas.nama_prodi || kelas.kode_prodi}</p>
                                </td>
                                <td>
                                    <p class="text-sm font-weight-normal mb-0">${kelas.tahun_masuk}</p>
                                </td>
                                <td class="align-middle text-end pe-4">
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-info me-1" onclick="detailKelas('${kelas.id_kelas}')" title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" onclick="editKelas('${kelas.id_kelas}')" title="Edit Kelas">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteKelas('${kelas.id_kelas}')" title="Hapus Kelas">
                                            <i class="fas fa-trash me-1"></i>Hapus
                                        </button>
                                    </div>
                                </td>
                            `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        throw new Error(response.data.message || 'Failed to load data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('error-message').textContent = error.message || 'Terjadi kesalahan saat memuat data kelas';
                    document.getElementById('error-state').classList.remove('d-none');
                });
        }

        // Load data kelas saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadKelasData();
        });

        // Fungsi untuk memuat opsi prodi untuk dropdown
        function loadProdiOptions() {
            api.get('/prodi')
                .then(response => {
                    if (response.data.success) {
                        const kodeProdiSelect = document.getElementById('kode_prodi');
                        kodeProdiSelect.innerHTML = '<option value="" disabled selected>Pilih Program Studi</option>';
                        response.data.data.forEach(prodi => {
                            kodeProdiSelect.innerHTML += `<option value="${prodi.kode_prodi}">${prodi.nama_prodi}</option>`;
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Gagal memuat data program studi', 'error');
                });
        }

        // Fungsi untuk membuka modal tambah kelas
        function tambahKelas() {
            document.getElementById('kelasForm').reset();
            document.getElementById('kelasModalLabel').innerText = 'Tambah Kelas';
            loadProdiOptions();
            document.getElementById('kelasForm').removeAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('kelasModal'));
            modal.show();
        }

        // Fungsi untuk menangani submit form kelas
        function handleSubmitKelas(event) {
            event.preventDefault();

            const idKelas = document.getElementById('kelasForm').getAttribute('data-id');
            const namaKelas = document.getElementById('nama_kelas').value;
            const kodeProdi = document.getElementById('kode_prodi').value;
            const tahunMasuk = document.getElementById('tahun_masuk').value;

            if (!namaKelas || !kodeProdi || !tahunMasuk) {
                Swal.fire('Peringatan', 'Semua field harus diisi', 'warning');
                return;
            }

            const formData = {
                nama_kelas: namaKelas,
                kode_prodi: kodeProdi,
                tahun_masuk: tahunMasuk
            };

            // Jika idKelas ada, berarti mode edit, jika tidak berarti tambah
            const method = idKelas ? 'put' : 'post';
            const url = idKelas ? `/kelas/${idKelas}` : '/kelas';
            
            const submitBtn = document.querySelector('#kelasForm button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

            api[method](url, formData)
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Berhasil', response.data.message || 'Kelas berhasil disimpan!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('kelasModal')).hide();
                        loadKelasData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal menyimpan kelas.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMsg = 'Terjadi kesalahan saat menyimpan kelas.';
                    
                    if (error.response && error.response.data && error.response.data.message) {
                        errorMsg = error.response.data.message;
                    }
                    
                    Swal.fire('Gagal', errorMsg, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        // Fungsi untuk menampilkan detail kelas
        function detailKelas(id) {
            api.get(`/kelas/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const kelas = response.data.data;
                        document.getElementById('detail-nama-kelas').textContent = kelas.nama_kelas;
                        document.getElementById('detail-kode-prodi').textContent = kelas.kode_prodi;
                        document.getElementById('detail-tahun-masuk').textContent = kelas.tahun_masuk;
                        
                        // Fetch prodi name
                        api.get('/prodi')
                            .then(res => {
                                if (res.data.success) {
                                    const prodi = res.data.data.find(p => p.kode_prodi === kelas.kode_prodi);
                                    document.getElementById('detail-nama-prodi').textContent = prodi ? prodi.nama_prodi : '-';
                                }
                            });
                        
                        const modal = new bootstrap.Modal(document.getElementById('detailKelasModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data kelas tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data kelas.', 'error');
                });
        }

        // Fungsi untuk membuka modal edit kelas
        function editKelas(id) {
            api.get(`/kelas/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const kelas = response.data.data;
                        document.getElementById('kelasModalLabel').innerText = 'Edit Kelas';
                        document.getElementById('nama_kelas').value = kelas.nama_kelas;
                        document.getElementById('tahun_masuk').value = kelas.tahun_masuk;
                        
                        // Load prodi options lalu set value
                        loadProdiOptions();
                        setTimeout(() => {
                            document.getElementById('kode_prodi').value = kelas.kode_prodi;
                        }, 300);
                        
                        // Simpan id_kelas di atribut data
                        document.getElementById('kelasForm').setAttribute('data-id', kelas.id_kelas);
                        const modal = new bootstrap.Modal(document.getElementById('kelasModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data kelas tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data kelas.', 'error');
                });
        }

        // Fungsi untuk menghapus kelas
        function deleteKelas(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus kelas ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.delete(`/kelas/${id}`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Berhasil', response.data.message || 'Kelas berhasil dihapus!', 'success');
                                loadKelasData();
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal menghapus kelas.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            let errorMsg = 'Terjadi kesalahan saat menghapus kelas.';
                            
                            if (error.response && error.response.data && error.response.data.message) {
                                errorMsg = error.response.data.message;
                            }
                            
                            Swal.fire('Gagal', errorMsg, 'error');
                        });
                }
            });
        }
    </script>
@endpush