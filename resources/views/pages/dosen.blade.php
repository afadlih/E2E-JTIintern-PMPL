@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Data Dosen'])
    <div class="container-fluid py-4">
        <!-- Stats Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="icon-dosen icon-warning">
                            <i class="bi bi-exclamation-triangle fs-1"></i>
                        </div>
                    </div>
                    <div class="col">
                        <h1 class="mb-1 fw-bold" id="jumlah-dosen">0</h1>
                        <p class="text-muted mb-0">Dosen yang tersedia menjadi pembimbing</p>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" onclick="mulaiPlotting()">Mulai Plotting</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Dosen Card -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">Daftar Dosen</h5>
                    <div class="d-flex gap-2">
                        <button type="button" style="color: white; background: #02A232;" class="btn"
                            onclick="tambahDosen()">
                            <i class="fas fa-plus me-2"></i>Tambah Dosen
                        </button>
                        <button type="button" class="btn btn-primary" onclick="importCSV()">
                            <i class="fas fa-file-import me-2"></i>Import CSV
                        </button>
                        <button type="button" class="btn btn-primary" onclick="exportPDF()">
                            <i class="fas fa-file-pdf me-2"></i>Export PDF
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">NIP
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="dosen-table-body" class="border-top-0">
                            <!-- Data will be populated here -->
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <p class="text-muted mb-0">Menampilkan 1-4 dari 100 Dosen</p>
                    <nav aria-label="Page navigation">
                        <ul class="pagination mb-0">
                            <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item"><a class="page-link" href="#">...</a></li>
                            <li class="page-item"><a class="page-link" href="#">50</a></li>
                            <li class="page-item"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modal Tambah Dosen -->
    <div class="modal fade" id="tambahDosenModal" tabindex="-1" aria-labelledby="tambahDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="tambahDosenForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahDosenModalLabel">Tambah Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_dosen" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama_dosen" name="nama_dosen" required>
                        </div>
                        <div class="mb-3">
                            <label for="skills" class="form-label">Skills</label>
                            <select class="form-select" id="skills" name="skills[]" multiple size="4">
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>

                        <div class="mb-3">
                            <label for="minat" class="form-label">Minat</label>
                            <select class="form-select" id="minat" name="minat[]" multiple size="4">
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Dosen -->
    <div class="modal fade" id="editDosenModal" tabindex="-1" aria-labelledby="editDosenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editDosenForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDosenModalLabel">Edit Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editDosenId">
                        <div class="mb-3">
                            <label for="edit_nama_dosen" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="edit_nama_dosen" name="nama_dosen" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_skills" class="form-label">Skills</label>
                            <select class="form-select" id="edit_skills" name="skills[]" multiple size="4">
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>

                        <div class="mb-3">
                            <label for="edit_minat" class="form-label">Minat</label>
                            <select class="form-select" id="edit_minat" name="minat[]" multiple size="4">
                                <!-- Options will be loaded via JS -->
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (Windows) atau Command (Mac) untuk memilih lebih
                                dari satu</small>
                        </div>
                        <div class="mb-3">
                            <label for="edit_nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="edit_nip" name="nip" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/dosen.css') }}">
@push('js')
    <script src="{{ asset('assets/js/dosen.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>

        // Panggil fungsi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadDosenData();
        });

        function loadDosenData() {
            // Tampilkan loading state
            const tableBody = document.getElementById('dosen-table-body');
            tableBody.innerHTML = `
                                                                                                                            <tr>
                                                                                                            <td colspan="4" class="text-center">
                                                                                                                <div class="py-5">
                                                                                                                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                                                                                                                    <div class="mt-3">
                                                                                                                        <h6 class="text-primary mb-1">Memuat data dosen</h6>
                                                                                                                        <p class="text-xs text-secondary">Mohon tunggu sebentar...</p>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    `;

            // Fetch data dari API
            axios.get('/api/dosen')
                .then(function (response) {
                    // Clear loading state
                    tableBody.innerHTML = '';

                    if (response.data.success && Array.isArray(response.data.data)) {
                        // Update jumlah dosen
                        document.getElementById('jumlah-dosen').innerText = response.data.data.length;

                        if (response.data.data.length > 0) {
                            response.data.data.forEach(function (dosen, index) {
                                // Create row with animation delay
                                const row = document.createElement('tr');
                                row.style.opacity = '0';
                                row.style.animation = `fadeIn 0.3s ease-out ${index * 0.05}s forwards`;

                                row.innerHTML = `
                                                                                                                                                   <td>
                                                                                                                <div class="d-flex">
                                                                                                                    <div class="avatar avatar-sm bg-gradient-primary rounded-circle text-white me-3 d-flex align-items-center justify-content-center">
                                                                                                                        ${dosen.nama_dosen.charAt(0).toUpperCase() || '?'}
                                                                                                                    </div>
                                                                                                                    <div class="d-flex flex-column justify-content-center">
                                                                                                                        <h6 class="mb-0 text-sm">${dosen.nama_dosen || '-'}</h6>
                                                                                                                        <p class="text-xs text-secondary mb-0">${dosen.email || '-'}</p>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <span class="text-sm font-weight-normal">${dosen.nip || '-'}</span>
                                                                                                            </td>
                                                                                                        <td class="text-end">
                                                    <div class="action-buttons">
                                                        <button class="btn btn-sm btn-info me-1" onclick="viewDosen('${dosen.id_dosen}')" title="Detail Dosen">
                                                            <i class="fas fa-eye me-1"></i>Detail
                                                        </button>
                                                        <button class="btn btn-sm btn-primary me-1" onclick="editDosen('${dosen.id_dosen}')" title="Edit Dosen">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="hapusDosen('${dosen.id_dosen}')" title="Hapus Dosen">
                                                            <i class="fas fa-trash me-1"></i>Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                                                                                        `;
                                tableBody.appendChild(row);
                            });

                            // Add animation keyframes if not already added
                            if (!document.getElementById('fade-in-animation')) {
                                const style = document.createElement('style');
                                style.id = 'fade-in-animation';
                                style.textContent = `
                                                                                                                                                @keyframes fadeIn {
                                                                                                                                                    from { opacity: 0; transform: translateY(10px); }
                                                                                                                                                    to { opacity: 1; transform: translateY(0); }
                                                                                                                                                }
                                                                                                                                            `;
                                document.head.appendChild(style);
                            }

                        } else {
                            showEmptyState(tableBody);
                        }
                    } else {
                        document.getElementById('jumlah-dosen').innerText = '0';
                        showEmptyState(tableBody);
                    }
                })
                .catch(function (error) {
                    document.getElementById('jumlah-dosen').innerText = '0';
                    console.error('Error loading dosen:', error);
                    showErrorState(tableBody);
                });
        }

        // Load skills options
        function loadSkillsOptions() {
            axios.get('/api/skills')
                .then(function (response) {
                    if (response.data.success) {
                        const skillsSelect = document.getElementById('skills');
                        skillsSelect.innerHTML = '';
                        response.data.data.forEach(function (skill) {
                            skillsSelect.innerHTML += `<option value="${skill.skill_id}">${skill.nama}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading skills:', error);
                });
        }

        // Load edit skills options and select current skills
        function loadEditSkillsOptions(selectedSkills = []) {
            axios.get('/api/skills')
                .then(function (response) {
                    if (response.data.success) {
                        const skillsSelect = document.getElementById('edit_skills');
                        skillsSelect.innerHTML = '';
                        response.data.data.forEach(function (skill) {
                            const isSelected = selectedSkills.includes(skill.skill_id) ? 'selected' : '';
                            skillsSelect.innerHTML += `<option value="${skill.skill_id}" ${isSelected}>${skill.nama}</option>`;
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Error loading skills:', error);
                });
        }

        // Load minat options
        function loadMinatOptions() {
            axios.get('/api/minat')
                .then(function (response) {
                    console.log('Minat response:', response.data); // Debug
                    const minatSelect = document.getElementById('minat');
                    minatSelect.innerHTML = '';
                    if (response.data && Array.isArray(response.data.data)) {
                        response.data.data.forEach(function (minat) {
                            minatSelect.innerHTML += `<option value="${minat.minat_id}">${minat.nama_minat}</option>`;
                        });
                    } else {
                        minatSelect.innerHTML = '<option disabled>Tidak ada data minat</option>';
                    }
                })
                .catch(function (error) {
                    console.error('Error loading minat:', error);
                });
        }

        // Load edit minat options and select current minat
        function loadEditMinatOptions(selectedMinat = []) {
            axios.get('/api/minat')
                .then(function (response) {
                    const minatSelect = document.getElementById('edit_minat');
                    minatSelect.innerHTML = '';
                    // Gunakan response.data.data, bukan response.data
                    if (response.data && Array.isArray(response.data.data)) {
                        response.data.data.forEach(function (minat) {
                            const isSelected = selectedMinat.includes(minat.minat_id) ? 'selected' : '';
                            minatSelect.innerHTML += `<option value="${minat.minat_id}" ${isSelected}>${minat.nama_minat}</option>`;
                        });
                    } else {
                        minatSelect.innerHTML = '<option disabled>Tidak ada data minat</option>';
                    }
                })
                .catch(function (error) {
                    console.error('Error loading minat:', error);
                });
        }

        // Helper function untuk menampilkan empty state
        function showEmptyState(tableBody) {
            tableBody.innerHTML = `
                                                                                                        <tr>
                                                                                                            <td colspan="4">
                                                                                                                <div class="empty-state">
                                                                                                                    <div class="empty-state-icon">
                                                                                                                        <i class="fas fa-user-graduate" style="font-size: 3.5rem;"></i>
                                                                                                                    </div>
                                                                                                                    <h6 class="text-muted">Tidak ada data dosen</h6>
                                                                                                                    <p class="text-sm text-secondary mb-3">Belum ada dosen yang tersedia. Silakan tambahkan dosen baru.</p>
                                                                                                                    <button class="btn btn-sm btn-success" onclick="tambahDosen()">
                                                                                                                        <i class="fas fa-plus me-1"></i>Tambah Dosen
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                    `;
        }

        // Helper function untuk menampilkan error state
        function showErrorState(tableBody) {
            tableBody.innerHTML = `
                                                                                                    <tr>
                                                                                                        <td colspan="4">
                                                                                                            <div class="error-state">
                                                                                                                <div class="error-state-icon">
                                                                                                                    <i class="fas fa-exclamation-circle" style="font-size: 3.5rem;"></i>
                                                                                                                </div>
                                                                                                                <h6 class="text-danger">Gagal memuat data</h6>
                                                                                                                <p class="text-sm mb-3">Terjadi kesalahan saat memuat data dosen. Silakan coba lagi nanti.</p>
                                                                                                                <button class="btn btn-sm btn-primary" onclick="loadDosenData()">
                                                                                                                    <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                `;
        }

        function tambahDosen() {
            loadSkillsOptions();
            loadMinatOptions();
            document.getElementById('tambahDosenForm').reset();
            const modal = new bootstrap.Modal(document.getElementById('tambahDosenModal'));
            modal.show();
        }

        // For adding a new dosen
        document.getElementById('tambahDosenForm').addEventListener('submit', function (e) {
            e.preventDefault();

            // Get selected skills
            const skillsSelect = document.getElementById('skills');
            const selectedSkills = Array.from(skillsSelect.selectedOptions).map(option => option.value);

            // Get selected minat
            const minatSelect = document.getElementById('minat');
            const selectedMinat = Array.from(minatSelect.selectedOptions).map(option => option.value);

            const formData = {
                nama_dosen: document.getElementById('nama_dosen').value,
                nip: document.getElementById('nip').value,
                skills: selectedSkills,
                minat: selectedMinat
            };


            axios.post('/api/dosen', formData)
                .then(function (response) {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Dosen berhasil ditambahkan!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('tambahDosenModal')).hide();
                        loadDosenData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal menambah dosen.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat menambah dosen.', 'error');
                });
        });

        function editDosen(id) {
            axios.get(`/api/dosen/${id}`)
                .then(function (response) {
                    if (response.data.success) {
                        const dosen = response.data.data;
                        document.getElementById('editDosenId').value = dosen.id_dosen;
                        document.getElementById('edit_nama_dosen').value = dosen.nama_dosen;
                        document.getElementById('edit_nip').value = dosen.nip;


                        // For skills
                        const selectedSkills = Array.isArray(dosen.skills)
                            ? dosen.skills.map(skill => skill.skill_id)
                            : [];
                        loadEditSkillsOptions(selectedSkills);

                        // For minat
                        const selectedMinat = Array.isArray(dosen.minat)
                            ? dosen.minat.map(minat => minat.minat_id)
                            : [];
                        loadEditMinatOptions(selectedMinat);

                        const modal = new bootstrap.Modal(document.getElementById('editDosenModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data dosen tidak ditemukan.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data dosen.', 'error');
                });
        }

        // For editing an existing dosen
        document.getElementById('editDosenForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const id = document.getElementById('editDosenId').value;

            // Get selected skills
            const skillsSelect = document.getElementById('edit_skills');
            const selectedSkills = Array.from(skillsSelect.selectedOptions).map(option => option.value);

            // Get selected minat
            const minatSelect = document.getElementById('edit_minat');
            const selectedMinat = Array.from(minatSelect.selectedOptions).map(option => option.value);

            const formData = {
                nama_dosen: document.getElementById('edit_nama_dosen').value,
                nip: document.getElementById('edit_nip').value,
                skills: selectedSkills,
                minat: selectedMinat
            };

            axios.put(`/api/dosen/${id}`, formData)
                .then(function (response) {
                    if (response.data.success) {
                        Swal.fire('Berhasil', 'Dosen berhasil diperbarui!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('editDosenModal')).hide();
                        loadDosenData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal memperbarui dosen.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.fire('Gagal', 'Terjadi kesalahan saat memperbarui dosen.', 'error');
                });
        });

        function hapusDosen(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus dosen ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/dosen/${id}`)
                        .then(function (response) {
                            if (response.data.success) {
                                Swal.fire('Berhasil', 'Dosen berhasil dihapus!', 'success');
                                loadDosenData();
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal menghapus dosen.', 'error');
                            }
                        })
                        .catch(function (error) {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus dosen.', 'error');
                        });
                }
            });
        }

        function mulaiPlotting() {
            // Redirect ke halaman plotting
            window.location.href = '/plotting';
        }

        function viewDosen(id) {
            // Show loading state
            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data dosen',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            axios.get(`/api/dosen/${id}`)
                .then(function (response) {
                    Swal.close();

                    if (response.data.success) {
                        const dosen = response.data.data;

                        // Format skills as badges if available
                        let skillsHtml = '<span class="text-muted">Tidak ada skill</span>';
                        if (Array.isArray(dosen.skills) && dosen.skills.length > 0) {
                            skillsHtml = dosen.skills.map(skill =>
                                `<span class="badge bg-primary me-1 mb-1">${skill.nama}</span>`
                            ).join('');
                        }

                        // Format minat as badges if available
                        let minatHtml = '<span class="text-muted">Tidak ada minat</span>';
                        if (Array.isArray(dosen.minat) && dosen.minat.length > 0) {
                            minatHtml = dosen.minat.map(minat =>
                                `<span class="badge bg-info me-1 mb-1">${minat.nama_minat}</span>`
                            ).join('');
                        }

                        // Display detailed information in a modal
                        Swal.fire({
                            title: `Detail Dosen: ${dosen.nama_dosen || 'Tidak Diketahui'}`,
                            html: `
                                                                    <div class="text-start">
                                                                        <div class="row mb-3">
                                                                            <div class="col-12 mb-3">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="avatar avatar-lg bg-gradient-primary rounded-circle text-white me-3 d-flex align-items-center justify-content-center">
                                                                                        ${dosen.nama_dosen.charAt(0).toUpperCase() || '?'}
                                                                                    </div>
                                                                                    <div>
                                                                                        <h5 class="mb-0">${dosen.nama_dosen || '-'}</h5>
                                                                                        <p class="text-muted mb-0">${dosen.email || '-'}</p>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label class="fw-bold d-block">NIP:</label>
                                                                                    <span>${dosen.nip || '-'}</span>
                                                                                </div>
                                                                            <div class="col-md-6">
                                                                                <div class="mb-3">
                                                                                    <label class="fw-bold d-block">Skills:</label>
                                                                                    <div class="mt-1">${skillsHtml}</div>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="fw-bold d-block">Minat:</label>
                                                                                    <div class="mt-1">${minatHtml}</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                `,
                            width: '600px',
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#5e72e4',
                        });
                    } else {
                        Swal.fire('Gagal', 'Data dosen tidak ditemukan.', 'error');
                    }
                })
                .catch(function (error) {
                    Swal.close();
                    console.error('Error viewing dosen details:', error);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data dosen.', 'error');
                });
        }

        function importCSV() {
            Swal.fire({
                title: 'Import Data Dosen',
                html: `
                                                                                                <div class="alert alert-info mb-3">
                                                                                                    <i class="fas fa-info-circle me-2"></i>
                                                                                                    <strong>Format CSV:</strong> File CSV harus memiliki kolom berikut:
                                                                                                    <ul class="mb-0 mt-1 text-start">
                                                                                                        <li><strong>nama_dosen</strong> (wajib)</li>
                                                                                                        <li><strong>nip</strong> (wajib)</li>
                                                                                                        <li><strong>email</strong> (opsional - akan digenerate otomatis jika kosong)</li>
                                                                                                    </ul>
                                                                                                </div>
                                                                                                <div class="mb-3">
                                                                                                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3" onclick="downloadTemplate()">
                                                                                                        <i class="fas fa-download me-1"></i>Download Template
                                                                                                    </button>
                                                                                                    <div class="custom-file">
                                                                                                        <input type="file" class="form-control" id="csvFile" accept=".csv">
                                                                                                    </div>
                                                                                                    <div class="form-text text-muted">
                                                                                                        <i class="bi bi-info-circle me-1"></i>
                                                                                                        Tips: Pastikan file CSV menggunakan koma (,) sebagai pemisah.
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="alert alert-warning small mb-0">
                                                                                                    <strong>Catatan Penting:</strong>
                                                                                                    <ul class="mb-0 mt-1 text-start">
                                                                                                        <li>Setiap dosen akan otomatis dibuatkan akun dengan password acak</li>
                                                                                                    </ul>
                                                                                                </div>
                                                                                            `,
                showCancelButton: true,
                confirmButtonText: 'Import',
                cancelButtonText: 'Batal',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const fileInput = document.getElementById('csvFile');
                    const formData = new FormData();

                    if (!fileInput.files[0]) {
                        Swal.showValidationMessage('Silakan pilih file CSV terlebih dahulu');
                        return false;
                    }

                    formData.append('csv_file', fileInput.files[0]);

                    return axios.post('/api/dosen/import', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            if (!response.data.success) {
                                throw new Error(response.data.message);
                            }
                            return response.data;
                        })
                        .catch(error => {
                            throw new Error(error.response?.data?.message || 'Terjadi kesalahan saat mengimpor data');
                        });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.errors && result.value.errors.length > 0) {
                        // Show warning if there are errors but some data was imported
                        let errorList = '';
                        result.value.errors.forEach(err => {
                            errorList += `<li class="text-start small">${err}</li>`;
                        });

                        Swal.fire({
                            title: 'Import Sebagian Berhasil',
                            html: `
                                                                                                            <p>${result.value.message}</p>
                                                                                                            <div class="alert alert-warning">
                                                                                                                <strong>Beberapa data tidak dapat diimpor:</strong>
                                                                                                                <ul class="mb-0 mt-1">
                                                                                                                    ${errorList}
                                                                                                                </ul>
                                                                                                            </div>
                                                                                                        `,
                            icon: 'warning'
                        });
                    } else {
                        // All data imported successfully
                        Swal.fire('Berhasil!', result.value.message, 'success');
                    }
                    loadDosenData(); // Refresh the data table
                }
            });
        }

        function downloadTemplate() {
            // Show loading state
            Swal.fire({
                title: 'Membuat Template',
                text: 'Sedang menyiapkan template CSV...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch wilayah for template
            axios.get('/api/wilayah')
                .then(function (response) {
                    if (response.data.success) {
                        const wilayah = response.data.data;

                        // CSV headers - adjusted for both m_user and m_dosen tables
                        const headers = [
                            'nama_dosen',  // This will go into m_user.name
                            'nip',         // This will go into m_dosen.nip
                            'email',       // This will go into m_user.email
                            // This will be converted to wilayah_id for m_dosen.wilayah_id
                            'password'     // Optional - will be auto-generated if not provided
                        ];

                        // Example data
                        const exampleData = [
                            // Example row 1
                            [
                                'Dr. Ahmad Fauzi',
                                '198601012019031001',
                                'ahmad.fauzi@example.com',
                                wilayah[0]?.nama_kota || 'Jember',
                                '' // Leave blank for auto-generated password
                            ],
                            // Example row 2
                            [
                                'Ir. Budi Santoso M.Kom',
                                '197505202005011002',
                                'budi.santoso@example.com',
                                wilayah.length > 1 ? wilayah[1].nama_kota : 'Surabaya',
                                'custom123' // Example of custom password
                            ]
                        ];

                        // Create CSV content with UTF-8 BOM for Excel compatibility
                        let csvContent = '\uFEFF' + headers.join(',') + '\r\n';

                        // Add example rows
                        exampleData.forEach(row => {
                            // Properly escape values that contain commas or quotes
                            const escapedRow = row.map(value => {
                                if (value && (value.includes(',') || value.includes('"') || value.includes('\n'))) {
                                    return `"${value.replace(/"/g, '""')}"`;
                                }
                                return value || '';
                            });
                            csvContent += escapedRow.join(',') + '\r\n';
                        });

                        // Create blob and download
                        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8' });
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'template_dosen.csv';
                        document.body.appendChild(link);
                        link.click();
                        URL.revokeObjectURL(url);
                        document.body.removeChild(link);

                        Swal.close();

                        // Show wilayah reference and field explanations
                        let wilayahRows = '';
                        wilayah.slice(0, 5).forEach(w => {
                            wilayahRows += `<tr><td>${w.nama_kota}</td><td>${w.wilayah_id}</td></tr>`;
                        });

                        Swal.fire({
                            icon: 'success',
                            title: 'Template CSV Berhasil Diunduh',
                            html: `
                                                                                                        <p>Template berhasil diunduh. Silakan isi dengan data dosen Anda.</p>

                                                                                                        <div class="alert alert-info mt-3">
                                                                                                            <strong>Penjelasan Kolom:</strong>
                                                                                                            <ul class="text-start mb-0 mt-2">
                                                                                                                <li><strong>nama_dosen</strong>: Nama lengkap dosen (wajib)</li>
                                                                                                                <li><strong>nip</strong>: Nomor Induk Pegawai (wajib)</li>
                                                                                                                <li><strong>email</strong>: Alamat email untuk login (opsional, akan digenerate otomatis jika kosong)</li>
                                                                                                                <li><strong>wilayah</strong>: Nama wilayah atau ID wilayah (wajib)</li>
                                                                                                                <li><strong>password</strong>: Password untuk login (opsional, akan digenerate otomatis jika kosong)</li>
                                                                                                            </ul>
                                                                                                        </div>

                                                                                                        <p class="mb-2 mt-3"><strong>Referensi Wilayah:</strong></p>
                                                                                                        <div class="table-responsive">
                                                                                                            <table class="table table-sm table-bordered">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th>Nama Wilayah</th>
                                                                                                                        <th>ID Wilayah</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    ${wilayahRows}
                                                                                                                    ${wilayah.length > 5 ? '<tr><td colspan="2" class="text-center">Dan lainnya...</td></tr>' : ''}
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                        </div>
                                                                                                    `,
                            confirmButtonText: 'Mengerti',
                            width: '600px'
                        });
                    } else {
                        Swal.fire('Gagal', 'Tidak dapat membuat template, gagal memuat data wilayah', 'error');
                    }
                })
                .catch(function (error) {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Terjadi kesalahan saat membuat template CSV', 'error');
                });
        }

        // Add this function to your JavaScript code
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

            // Make request to export endpoint
            fetch('/api/dosen/export/pdf')
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.blob();
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `data_dosen_${new Date().getTime()}.pdf`;
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