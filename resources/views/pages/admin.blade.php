<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\admin.blade.php -->
@extends('layouts.app',  ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Admin'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Daftar Admin</h6>
                        <p class="text-sm text-secondary mb-0">Kelola pengguna dengan hak akses administrator</p>
                    </div>
                    <button class="btn btn-sm btn-success" onclick="tambahAdmin()">
                        <i class="fas fa-plus-circle me-1"></i>Tambah Admin
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="admin-table-body">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Empty State -->
                <div id="empty-state" class="text-center py-5 d-none">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-user-shield text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <h6 class="text-muted">Belum ada data Admin</h6>
                    <p class="text-xs text-secondary mb-3">
                        Silahkan tambahkan admin baru untuk mengelola sistem
                    </p>
                    <button class="btn btn-sm btn-success" onclick="tambahAdmin()">
                        <i class="fas fa-plus-circle me-1"></i>Tambah Admin
                    </button>
                </div>

                <!-- Error State -->
                <div id="error-state" class="text-center py-5 d-none">
                    <div class="error-state-icon mb-3">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-danger">Gagal memuat data</h6>
                    <p class="text-xs text-secondary mb-3" id="error-message">
                        Terjadi kesalahan saat memuat data admin
                    </p>
                    <button class="btn btn-sm btn-primary" onclick="loadAdminData()">
                        <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-labelledby="modalTambahAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formTambahAdmin" onsubmit="submitTambahAdmin(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahAdminLabel">Tambah Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_admin" class="form-label">Nama Admin</label>
                            <input type="text" id="nama_admin" name="nama_admin" class="form-control" required>
                            <div class="form-text">Masukkan nama lengkap admin</div>
                        </div>
                        <div class="mb-3">
                            <label for="email_admin" class="form-label">Email</label>
                            <input type="email" id="email_admin" name="email_admin" class="form-control" required>
                            <div class="form-text">Email akan digunakan untuk login</div>
                        </div>
                        <div class="mb-3">
                            <label for="password_admin" class="form-label">Password</label>
                            <input type="password" id="password_admin" name="password_admin" class="form-control" required>
                            <div class="form-text">Minimal 6 karakter</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-tambah">
                            <i class="fas fa-save me-1"></i>Tambah
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Admin -->
    <div class="modal fade" id="modalEditAdmin" tabindex="-1" aria-labelledby="modalEditAdminLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditAdmin" onsubmit="submitEditAdmin(event)">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditAdminLabel">Edit Admin</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_admin" name="id_admin">
                        <div class="mb-3">
                            <label for="edit_nama_admin" class="form-label">Nama Admin</label>
                            <input type="text" id="edit_nama_admin" name="nama_admin" class="form-control" required>
                            <div class="form-text">Masukkan nama lengkap admin</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email_admin" class="form-label">Email</label>
                            <input type="email" id="edit_email_admin" name="email_admin" class="form-control" required>
                            <div class="form-text">Email akan digunakan untuk login</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password_admin" class="form-label">Password <span class="text-muted">(opsional)</span></label>
                            <input type="password" id="edit_password_admin" name="password_admin" class="form-control">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btn-update">
                            <i class="fas fa-save me-1"></i>Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="avatar avatar-xl bg-gradient-primary mx-auto mb-3">
                            <span id="detail-initial" class="text-white" style="font-size: 1.5rem;">A</span>
                        </div>
                        <h5 id="detail-name" class="mb-0">Nama Admin</h5>
                        <p id="detail-email" class="text-muted">admin@example.com</p>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <table class="table table-sm">
                            <tr>
                                <th width="130">Terdaftar pada</th>
                                <td><span id="detail-created"></span></td>
                            </tr>
                            <tr>
                                <th>Diperbarui pada</th>
                                <td><span id="detail-updated"></span></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this anywhere in your layout for superadmin pages -->
    @if(Auth::check() && Auth::user()->role === 'superadmin')
        <div data-class="bg-transparent" style="display:none;"></div>
    @endif
@endsection

@push('css')
<style>
    .action-buttons {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }
    
    .avatar {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    
    .avatar-sm {
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(310deg, #5e72e4, #825ee4);
    }
    
    .empty-state-icon, .error-state-icon {
        opacity: 0.5;
    }
    
    .table td, .table th {
        white-space: nowrap;
    }
    
    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .fade-in {
        animation: fadeIn 0.6s ease-in-out;
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

        // Format tanggal
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric'
            });
        }

        // Get initials from name
        function getInitials(name) {
            if (!name) return '?';
            return name.split(' ')[0][0].toUpperCase();
        }

        function loadAdminData() {
            const tableBody = document.getElementById('admin-table-body');
            tableBody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center py-3">
                        <i class="fas fa-circle-notch fa-spin me-2"></i>Memuat data admin...
                    </td>
                </tr>
            `;
            
            document.getElementById('empty-state').classList.add('d-none');
            document.getElementById('error-state').classList.add('d-none');

            api.get('/superadmin/admin')
                .then(response => {
                    if (response.data.success) {
                        tableBody.innerHTML = '';
                        
                        if (response.data.data.length === 0) {
                            document.getElementById('empty-state').classList.remove('d-none');
                            return;
                        }

                        response.data.data.forEach((admin, index) => {
                            const row = document.createElement('tr');
                            row.style.opacity = '0';
                            row.style.animation = `fadeIn 0.5s ease-out forwards ${index * 0.1}s`;
                            
                            row.innerHTML = `
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="avatar-sm bg-gradient-primary me-3 text-white rounded-circle d-flex align-items-center justify-content-center">
                                            ${getInitials(admin.name)}
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">${admin.name}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-sm font-weight-normal">${admin.email}</span>
                                </td>
                                <td class="align-middle text-end pe-4">
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-info me-1" onclick="detailAdmin(${admin.id_user})" title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </button>
                                        <button class="btn btn-sm btn-primary me-1" onclick="editAdmin(${admin.id_user})" title="Edit Admin">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteAdmin(${admin.id_user})" title="Hapus Admin">
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
                    document.getElementById('error-message').textContent = error.message || 'Terjadi kesalahan saat memuat data admin';
                    document.getElementById('error-state').classList.remove('d-none');
                });
        }

        // Load data when page loads
        document.addEventListener('DOMContentLoaded', function () {
            loadAdminData();
        });

        function tambahAdmin() {
            document.getElementById('formTambahAdmin').reset();
            const modal = new bootstrap.Modal(document.getElementById('modalTambahAdmin'));
            modal.show();
        }

        function detailAdmin(id) {
            api.get(`/superadmin/admin/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const admin = response.data.data;
                        document.getElementById('detail-initial').textContent = getInitials(admin.name);
                        document.getElementById('detail-name').textContent = admin.name || '-';
                        document.getElementById('detail-email').textContent = admin.email || '-';
                        document.getElementById('detail-created').textContent = formatDate(admin.created_at);
                        document.getElementById('detail-updated').textContent = formatDate(admin.updated_at);
                        
                        const modal = new bootstrap.Modal(document.getElementById('detailAdminModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data admin tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil detail admin.', 'error');
                });
        }

        function editAdmin(id) {
            const loadingBtn = `<i class="fas fa-circle-notch fa-spin me-1"></i>Memuat...`;
            const modal = document.getElementById('modalEditAdmin');
            const submitBtn = modal.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = loadingBtn;

            api.get(`/superadmin/admin/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const admin = response.data.data;
                        document.getElementById('edit_id_admin').value = admin.id_user;
                        document.getElementById('edit_nama_admin').value = admin.name;
                        document.getElementById('edit_email_admin').value = admin.email;
                        document.getElementById('edit_password_admin').value = '';
                        
                        const modal = new bootstrap.Modal(document.getElementById('modalEditAdmin'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memuat data admin', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Error', 'Terjadi kesalahan saat memuat data admin', 'error');
                })
                .finally(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                });
        }

        function submitEditAdmin(event) {
            event.preventDefault();
            const form = event.target;
            const id = document.getElementById('edit_id_admin').value;
            const data = {
                name: form.nama_admin.value,
                email: form.email_admin.value,
                password: form.password_admin.value
            };

            // If password is empty, remove it from the data object
            if (!data.password) {
                delete data.password;
            }
            
            const submitBtn = document.querySelector('#btn-update');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i>Menyimpan...';

            api.put(`/superadmin/admin/${id}`, data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.data.message || 'Data admin berhasil diperbarui!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditAdmin'));
                        modal.hide();
                        form.reset();
                        loadAdminData();
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal memperbarui data admin', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat memperbarui data admin.';
                    
                    if (err.response && err.response.data && err.response.data.message) {
                        if (err.response.data.validation_errors) {
                            // Handle validation errors
                            const errors = err.response.data.message;
                            const errorMessages = Object.values(errors).flat();
                            msg = errorMessages.join('<br>');
                        } else {
                            msg = err.response.data.message;
                        }
                    }
                    
                    Swal.fire('Error', msg, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        function deleteAdmin(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data admin ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Send DELETE request
                    api.delete(`/superadmin/admin/${id}`)
                        .then(res => {
                            if (res.data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.data.message || 'Data admin berhasil dihapus.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                
                                loadAdminData();
                            } else {
                                Swal.fire('Gagal', res.data.message || 'Gagal menghapus data admin', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            let errorMsg = 'Terjadi kesalahan saat menghapus data admin.';
                            
                            if (err.response && err.response.data && err.response.data.message) {
                                errorMsg = err.response.data.message;
                            }
                            
                            Swal.fire('Error', errorMsg, 'error');
                        });
                }
            });
        }

        function submitTambahAdmin(event) {
            event.preventDefault();
            const form = event.target;
            const data = {
                name: form.nama_admin.value,
                email: form.email_admin.value,
                password: form.password_admin.value
            };
            
            const submitBtn = document.querySelector('#btn-tambah');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-1"></i>Menambahkan...';

            api.post('/superadmin/admin', data)
                .then(res => {
                    if (res.data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.data.message || 'Admin berhasil ditambahkan!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahAdmin'));
                        modal.hide();
                        form.reset();
                        loadAdminData();
                    } else {
                        Swal.fire('Gagal', res.data.message || 'Gagal menambahkan admin', 'error');
                    }
                })
                .catch(err => {
                    let msg = 'Terjadi kesalahan saat menambahkan admin.';
                    
                    if (err.response && err.response.data && err.response.data.message) {
                        if (err.response.data.validation_errors) {
                            // Handle validation errors
                            const errors = err.response.data.message;
                            const errorMessages = Object.values(errors).flat();
                            msg = errorMessages.join('<br>');
                        } else {
                            msg = err.response.data.message;
                        }
                    }
                    
                    Swal.fire('Error', msg, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }
    </script>
@endpush