{{-- filepath: d:\laragon\www\JTIintern\resources\views\pages\skill.blade.php --}}
@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Skill'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Daftar Skill</h6>
                                <p class="text-sm text-secondary mb-0">
                                    Manajemen data skill untuk program magang
                                </p>
                            </div>
                            <button class="btn btn-sm btn-success" onclick="tambahSkill()">
                                <i class="fas fa-plus me-2"></i>Tambah Skill
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Skill</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="skills-table-body">
                                    {{-- Data will be loaded here via JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Loading State -->
                        <div id="loading-state" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-sm text-secondary mt-3">Memuat data skill...</p>
                        </div>
                        
                        <!-- Empty State -->
                        <div id="empty-state" class="text-center py-5 d-none">
                            <div class="empty-state-icon mb-3">
                                <i class="bi bi-stars text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                            <h6 class="text-muted">Belum ada data skill</h6>
                            <p class="text-xs text-secondary mb-3">
                                Silahkan tambahkan skill baru
                            </p>
                            <button class="btn btn-sm btn-success" onclick="tambahSkill()">
                                <i class="fas fa-plus me-2"></i>Tambah Skill
                            </button>
                        </div>

                        <!-- Error State -->
                        <div id="error-state" class="text-center py-5 d-none">
                            <div class="error-state-icon mb-3">
                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            </div>
                            <h6 class="text-danger">Gagal memuat data</h6>
                            <p class="text-xs text-secondary mb-3" id="error-message">
                                Terjadi kesalahan saat memuat data skill
                            </p>
                            <button class="btn btn-sm btn-primary" onclick="loadSkills()">
                                <i class="fas fa-sync-alt me-2"></i>Coba Lagi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add/Edit Skill --}}
    <div class="modal fade" id="skillModal" tabindex="-1" role="dialog" aria-labelledby="skillModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="skillModalLabel">Tambah Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="skillForm">
                        <input type="hidden" id="skill_id">
                        <div class="form-group">
                            <label for="nama" class="form-control-label">Nama Skill</label>
                            <input class="form-control" type="text" id="nama" placeholder="Masukkan nama skill">
                            <div class="invalid-feedback" id="nama-error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSaveSkill" onclick="saveSkill()">Simpan</button>
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
                    <p>Apakah Anda yakin ingin menghapus skill <span id="delete-skill-name" class="fw-bold"></span>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="btnDeleteSkill" onclick="confirmDeleteSkill()">Hapus</button>
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
        let currentSkillId = null;
        
        // Initialize axios instance with CSRF protection
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        // Load skills data
        function loadSkills() {
            // Show loading state
            document.getElementById('skills-table-body').innerHTML = '';
            document.getElementById('loading-state').classList.remove('d-none');
            document.getElementById('empty-state').classList.add('d-none');
            document.getElementById('error-state').classList.add('d-none');
            
            api.get('/skills')
                .then(response => {
                    // Hide loading state
                    document.getElementById('loading-state').classList.add('d-none');
                    
                    if (response.data.success) {
                        const skills = response.data.data;
                        
                        if (skills.length === 0) {
                            // Show empty state
                            document.getElementById('empty-state').classList.remove('d-none');
                        } else {
                            // Populate table
                            const tableBody = document.getElementById('skills-table-body');
                            skills.forEach((skill, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td class="ps-4">
                                        <span class="text-secondary text-xs font-weight-bold">${index + 1}</span>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-sm">${skill.nama}</h6>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm mb-0" onclick="editSkill(${skill.skill_id}, '${skill.nama}')">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm mb-0" onclick="deleteSkill(${skill.skill_id}, '${skill.nama}')">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </td>
                                `;
                                tableBody.appendChild(row);
                            });
                        }
                    } else {
                        throw new Error(response.data.message || 'Failed to load skills data');
                    }
                })
                .catch(error => {
                    // Show error state
                    document.getElementById('loading-state').classList.add('d-none');
                    document.getElementById('error-state').classList.remove('d-none');
                    document.getElementById('error-message').textContent = error.message || 'Terjadi kesalahan saat memuat data skill';
                });
        }
        
        // Add new skill
        function tambahSkill() {
            // Reset form
            document.getElementById('skillForm').reset();
            document.getElementById('skill_id').value = '';
            document.getElementById('nama-error').textContent = '';
            document.getElementById('nama').classList.remove('is-invalid');
            
            // Update modal title
            document.getElementById('skillModalLabel').textContent = 'Tambah Skill';
            
            // Reset current skill id
            currentSkillId = null;
            
            // Open modal
            const modal = new bootstrap.Modal(document.getElementById('skillModal'));
            modal.show();
        }
        
        // Edit skill
        function editSkill(id, nama) {
            // Set form values
            document.getElementById('skill_id').value = id;
            document.getElementById('nama').value = nama;
            document.getElementById('nama-error').textContent = '';
            document.getElementById('nama').classList.remove('is-invalid');
            
            // Update modal title
            document.getElementById('skillModalLabel').textContent = 'Edit Skill';
            
            // Set current skill id
            currentSkillId = id;
            
            // Open modal
            const modal = new bootstrap.Modal(document.getElementById('skillModal'));
            modal.show();
        }
        
        // Save skill (create or update)
        function saveSkill() {
            const skillId = document.getElementById('skill_id').value;
            const nama = document.getElementById('nama').value;
            
            // Basic validation
            if (!nama) {
                document.getElementById('nama').classList.add('is-invalid');
                document.getElementById('nama-error').textContent = 'Nama skill tidak boleh kosong';
                return;
            }
            
            // Clear validation errors
            document.getElementById('nama').classList.remove('is-invalid');
            document.getElementById('nama-error').textContent = '';
            
            // Disable save button
            const saveButton = document.getElementById('btnSaveSkill');
            saveButton.disabled = true;
            saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            
            // Choose API endpoint based on action (create or update)
            const url = skillId ? `/skill/${skillId}` : '/skill';
            const method = skillId ? 'put' : 'post';
            
            api[method](url, { nama: nama })
                .then(response => {
                    if (response.data.success) {
                        // Close modal
                        bootstrap.Modal.getInstance(document.getElementById('skillModal')).hide();
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        
                        // Reload data
                        loadSkills();
                    } else {
                        throw new Error(response.data.message || 'Failed to save skill');
                    }
                })
                .catch(error => {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                    
                    // Handle validation errors
                    if (error.response && error.response.status === 422) {
                        const responseData = error.response.data;
                        if (responseData.message) {
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
        function deleteSkill(id, nama) {
            currentSkillId = id;
            document.getElementById('delete-skill-name').textContent = nama;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
        
        // Confirm delete skill
        function confirmDeleteSkill() {
            if (!currentSkillId) return;
            
            // Disable delete button
            const deleteButton = document.getElementById('btnDeleteSkill');
            deleteButton.disabled = true;
            deleteButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
            
            api.delete(`/skill/${currentSkillId}`)
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
                        loadSkills();
                    } else {
                        throw new Error(response.data.message || 'Failed to delete skill');
                    }
                })
                .catch(error => {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menghapus skill'
                    });
                })
                .finally(() => {
                    // Enable delete button
                    deleteButton.disabled = false;
                    deleteButton.innerHTML = 'Hapus';
                });
        }
        
        // Load data when page is ready
        document.addEventListener('DOMContentLoaded', function() {
            loadSkills();
        });
    </script>
@endpush