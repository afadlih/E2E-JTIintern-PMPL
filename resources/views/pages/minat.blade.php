{{-- ✅ UPDATE: minat.blade.php - Konsisten dengan struktur skill --}}
@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Minat'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Daftar Minat</h6>
                                <p class="text-sm text-secondary mb-0">
                                    Manajemen data minat untuk program magang
                                </p>
                            </div>
                            <button class="btn btn-sm btn-success" onclick="tambahMinat()">
                                <i class="fas fa-plus me-2"></i>Tambah Minat
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Minat</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Deskripsi</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="minat-table-body">
                                    {{-- Data will be loaded here via JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Loading State -->
                        <div id="loading-state" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-sm text-secondary mt-3">Memuat data minat...</p>
                        </div>
                        
                        <!-- Empty State -->
                        <div id="empty-state" class="text-center py-5 d-none">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-heart text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                            <h6 class="text-muted">Belum ada data minat</h6>
                            <p class="text-xs text-secondary mb-3">
                                Silahkan tambahkan minat baru
                            </p>
                            <button class="btn btn-sm btn-success" onclick="tambahMinat()">
                                <i class="fas fa-plus me-2"></i>Tambah Minat
                            </button>
                        </div>

                        <!-- Error State -->
                        <div id="error-state" class="text-center py-5 d-none">
                            <div class="error-state-icon mb-3">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="text-danger">Gagal memuat data</h6>
                            <p class="text-xs text-secondary mb-3" id="error-message">
                                Terjadi kesalahan saat memuat data minat
                            </p>
                            <button class="btn btn-sm btn-primary" onclick="loadMinat()">
                                <i class="fas fa-sync-alt me-2"></i>Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add/Edit Minat --}}
    <div class="modal fade" id="minatModal" tabindex="-1" role="dialog" aria-labelledby="minatModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="minatModalLabel">Tambah Minat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="minatForm">
                        <input type="hidden" id="minat_id">
                        <div class="form-group mb-3">
                            <label for="nama_minat" class="form-control-label">Nama Minat</label>
                            <input class="form-control" type="text" id="nama_minat" placeholder="Masukkan nama minat">
                            <div class="invalid-feedback" id="nama-minat-error"></div>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi" class="form-control-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" rows="3" placeholder="Masukkan deskripsi minat (opsional)"></textarea>
                            <div class="invalid-feedback" id="deskripsi-error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveMinat" onclick="saveMinat()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Delete Confirmation --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus minat <span id="delete-minat-name" class="fw-bold"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnDeleteMinat" onclick="confirmDeleteMinat()">Hapus</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('assets/css/data-mahasiswa.css') }}" rel="stylesheet" />
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global variables
        let currentMinatId = null;
        
        // Initialize axios instance with CSRF protection
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        // Load minat data
        function loadMinat() {
            // Show loading state
            document.getElementById('minat-table-body').innerHTML = '';
            document.getElementById('loading-state').classList.remove('d-none');
            document.getElementById('empty-state').classList.add('d-none');
            document.getElementById('error-state').classList.add('d-none');
            
            api.get('/minat')
                .then(response => {
                    // Hide loading state
                    document.getElementById('loading-state').classList.add('d-none');
                    
                    if (response.data.success) {
                        const minat = response.data.data;
                        
                        if (minat.length === 0) {
                            // Show empty state
                            document.getElementById('empty-state').classList.remove('d-none');
                        } else {
                            // Populate table
                            const tableBody = document.getElementById('minat-table-body');
                            minat.forEach((item, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td class="ps-4">
                                        <span class="text-secondary text-xs font-weight-bold">${index + 1}</span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-sm">${escapeHtml(item.nama_minat)}</h6>
                                    </td>
                                    <td>
                                        <p class="td-deskripsi">${escapeHtml(item.deskripsi || '-')}</p>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm mb-0 me-1" onclick="editMinat(${item.minat_id}, '${escapeHtml(item.nama_minat)}', '${escapeHtml(item.deskripsi || '')}')">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm mb-0" onclick="deleteMinat(${item.minat_id}, '${escapeHtml(item.nama_minat)}')">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </td>
                                `;
                                tableBody.appendChild(row);
                            });
                        }
                    } else {
                        throw new Error(response.data.message || 'Failed to load minat data');
                    }
                })
                .catch(error => {
                    // Show error state
                    document.getElementById('loading-state').classList.add('d-none');
                    document.getElementById('error-state').classList.remove('d-none');
                    
                    let errorMessage = 'Terjadi kesalahan saat memuat data minat';
                    
                    if (error.response) {
                        switch (error.response.status) {
                            case 401:
                                errorMessage = 'Unauthorized - Silakan login kembali';
                                break;
                            case 403:
                                errorMessage = 'Forbidden - Tidak memiliki akses';
                                break;
                            case 404:
                                errorMessage = 'Endpoint tidak ditemukan';
                                break;
                            case 500:
                                errorMessage = 'Server error - Silakan coba lagi';
                                break;
                        }
                    }
                    
                    document.getElementById('error-message').textContent = errorMessage;
                });
        }
        
        // Add new minat
        function tambahMinat() {
            // Reset form
            document.getElementById('minatForm').reset();
            document.getElementById('minat_id').value = '';
            document.getElementById('nama-minat-error').textContent = '';
            document.getElementById('deskripsi-error').textContent = '';
            document.getElementById('nama_minat').classList.remove('is-invalid');
            document.getElementById('deskripsi').classList.remove('is-invalid');
            
            // Update modal title
            document.getElementById('minatModalLabel').textContent = 'Tambah Minat';
            
            // Reset current minat id
            currentMinatId = null;
            
            // Open modal
            const modal = new bootstrap.Modal(document.getElementById('minatModal'));
            modal.show();
        }
        
        // Edit minat
        function editMinat(id, nama, deskripsi) {
            // Set form values
            document.getElementById('minat_id').value = id;
            document.getElementById('nama_minat').value = nama;
            document.getElementById('deskripsi').value = deskripsi;
            document.getElementById('nama-minat-error').textContent = '';
            document.getElementById('deskripsi-error').textContent = '';
            document.getElementById('nama_minat').classList.remove('is-invalid');
            document.getElementById('deskripsi').classList.remove('is-invalid');
            
            // Update modal title
            document.getElementById('minatModalLabel').textContent = 'Edit Minat';
            
            // Set current minat id
            currentMinatId = id;
            
            // Open modal
            const modal = new bootstrap.Modal(document.getElementById('minatModal'));
            modal.show();
        }
        
        // Save minat (create or update)
        function saveMinat() {
            const minatId = document.getElementById('minat_id').value;
            const namaMinat = document.getElementById('nama_minat').value.trim();
            const deskripsi = document.getElementById('deskripsi').value.trim();
            
            // Basic validation
            if (!namaMinat) {
                document.getElementById('nama_minat').classList.add('is-invalid');
                document.getElementById('nama-minat-error').textContent = 'Nama minat tidak boleh kosong';
                return;
            }
            
            // Clear validation errors
            document.getElementById('nama_minat').classList.remove('is-invalid');
            document.getElementById('deskripsi').classList.remove('is-invalid');
            document.getElementById('nama-minat-error').textContent = '';
            document.getElementById('deskripsi-error').textContent = '';
            
            // Disable save button
            const saveButton = document.getElementById('btnSaveMinat');
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // Choose API endpoint based on action (create or update)
            const url = minatId ? `/minat/${minatId}` : '/minat';
            const method = minatId ? 'put' : 'post';
            
            const data = {
                nama_minat: namaMinat,
                deskripsi: deskripsi
            };
            
            api[method](url, data)
                .then(response => {
                    if (response.data.success) {
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('minatModal')).hide();
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        // Reload data
                        loadMinat();
                    } else {
                        throw new Error(response.data.message || 'Failed to save minat');
                    }
                })
                .catch(error => {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    
                    // Handle validation errors
                    if (error.response && error.response.status === 422) {
                        const responseData = error.response.data;
                        
                        if (responseData.errors) {
                            // Handle field-specific errors
                            if (responseData.errors.nama_minat) {
                                document.getElementById('nama_minat').classList.add('is-invalid');
                                document.getElementById('nama-minat-error').textContent = responseData.errors.nama_minat[0];
                            }
                            if (responseData.errors.deskripsi) {
                                document.getElementById('deskripsi').classList.add('is-invalid');
                                document.getElementById('deskripsi-error').textContent = responseData.errors.deskripsi[0];
                            }
                            return; // Don't show general error message
                        } else if (responseData.message) {
                            errorMessage = responseData.message;
                        }
                    }
                    
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: errorMessage
                    });
                })
                .finally(() => {
                    // Enable save button
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Simpan';
                });
        }
        
        // Show delete confirmation
        function deleteMinat(id, nama) {
            currentMinatId = id;
            document.getElementById('delete-minat-name').textContent = nama;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
        
        // Confirm delete minat
        function confirmDeleteMinat() {
            if (!currentMinatId) return;
            
            // Disable delete button
            const deleteButton = document.getElementById('btnDeleteMinat');
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
            
            api.delete(`/minat/${currentMinatId}`)
                .then(response => {
                    if (response.data.success) {
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        // Reload data
                        loadMinat();
                    } else {
                        throw new Error(response.data.message || 'Failed to delete minat');
                    }
                })
                .catch(error => {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menghapus minat'
                    });
                })
                .finally(() => {
                    // Enable delete button
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = 'Hapus';
                    currentMinatId = null;
                });
        }
        
        // Utility function to escape HTML
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Load data when page is ready
        document.addEventListener('DOMContentLoaded', function() {
            loadMinat();
        });
    </script>
@endpush