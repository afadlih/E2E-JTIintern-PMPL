@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')

    <div class="container-fluid py-4">
        <!-- Profile Card -->
        <div class="profile card mb-4">
            <div class="content-profile p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="avatar-container">
                            <div class="avatar-profile">
                                <i class="fas fa-user-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <h4 class="mb-1" style="color: #2d2d2d;" data-profile="name">{{ $userData->name ?? 'Nama Dosen' }}
                        </h4>
                        <p class="mb-0" style="color: #2d2d2d;" data-profile="email">
                            {{ $userData->email ?? 'dosen@example.com' }}
                        </p>
                    </div>
                    <div class="col-auto">
                        <button id="edit-profile-btn" class="btn btn-sm ">
                            <i class="fas fa-pencil-alt me-2"></i>Edit Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Completion Alert Container -->
        <div id="profile-completion-alert" style="display: none;">
            <div class="row mb-4">
                <div class="col-12">
                    <div class="profile-incomplete-card p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="warning-icon me-3">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">Profil Belum Lengkap</h6>
                                    <p class="mb-0 text-sm">
                                        Lengkapi profil untuk memaksimalkan sistem bimbingan.
                                        <span class="d-block mt-1">
                                            <small>
                                                <strong>Data yang belum lengkap: </strong>
                                                <span id="missing-fields-text"></span>
                                            </small>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Personal Information -->
            <div class="col-8">
                <div class="card mb-4">
                    <div class="card-header p-3">
                        <h6 class="px-2">Data Dosen</h6>
                    </div>

                    <div class="card-body p-4">
                        <div id="profile-view-mode">
                            <div class="row mb-4">
                                <div class="col-12 col-md-6 mb-3">
                                    <h6 class="text-uppercase text-sm text-muted mb-3">Informasi Pribadi</h6>
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">NIP</small>
                                        <p class="mb-0" data-profile="nip">{{ $dosenData->nip ?? '-' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">Nama Lengkap</small>
                                        <p class="mb-0" data-profile="name">{{ $userData->name ?? '-' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">Email</small>
                                        <p class="mb-0" data-profile="email">{{ $userData->email ?? '-' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">No. Telepon</small>
                                        <p class="mb-0" data-profile="no_hp">{{ $dosenData->no_hp ?? '-' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">Alamat</small>
                                        <p class="mb-0" data-profile="alamat">{{ $dosenData->alamat ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <h6 class="text-uppercase text-sm text-muted mb-3">Informasi Akademik</h6>
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">Bidang Keahlian</small>
                                        <div data-profile="bidang_keahlian" class="d-flex flex-wrap gap-2">
                                            @if (isset($skills) && count($skills) > 0)
                                                @foreach ($skills as $skill)
                                                    <span
                                                        class="badge bg-light-success me-2 mb-2">{{ $skill->nama }}</span>
                                                @endforeach
                                            @else
                                                <p class="text-muted mb-0">Belum ada bidang keahlian yang ditambahkan</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">Minat</small>
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
                                    <div class="mb-3">
                                        <small class="d-block text-uppercase text-xs text-muted">Jumlah Bimbingan</small>
                                        <p class="mb-0" data-profile="jumlah_bimbingan">{{ $jumlahBimbingan ?? '0' }}
                                            Mahasiswa</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Edit Mode -->
                        <div id="profile-edit-mode" style="display: none;">
                            <form id="profile-form" action="{{ route('dosen.profile.update') }}" method="POST">
                                @csrf
                                <div class="row mb-4">
                                    <div class="col-12 col-md-6 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-3">Informasi Pribadi</h6>
                                        <div class="mb-3">
                                            <label class="form-label text-xs text-uppercase text-muted">NIP</label>
                                            <input type="text" class="form-control" name="nip" id="edit-nip"
                                                readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs text-uppercase text-muted">Nama
                                                Lengkap</label>
                                            <input type="text" class="form-control" name="name" id="edit-name">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs text-uppercase text-muted">Email</label>
                                            <input type="email" class="form-control" name="email" id="edit-email"
                                                readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs text-uppercase text-muted">No. Telepon</label>
                                            <input type="text" class="form-control" name="no_hp" id="edit-no_hp">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <h6 class="text-uppercase text-sm text-muted mb-3">Informasi Akademik</h6>
                                        <div class="mb-3">
                                            <label class="form-label text-xs text-uppercase text-muted">Bidang
                                                Keahlian</label>
                                            <div id="skills-buttons-container" class="d-flex flex-wrap gap-2">
                                                <!-- Will be populated via JavaScript -->
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs text-uppercase text-muted">Minat</label>
                                            <div id="minat-buttons-container" class="d-flex flex-wrap gap-2">
                                                <!-- Will be populated via JavaScript -->
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-xs text-uppercase text-muted">Wilayah
                                                Preferensi</label>
                                            <select class="form-select" name="wilayah_id" id="wilayah-select">
                                                <option value="">Pilih Wilayah</option>
                                                <!-- Will be populated with AJAX -->
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <h6 class="text-uppercase text-sm text-muted mb-3">Alamat</h6>
                                        <textarea class="form-control" name="alamat" rows="3">{{ $dosenData->alamat ?? '' }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" id="cancel-edit-btn"
                                                class="btn btn-sm btn-outline-secondary me-2">
                                                <i class="fas fa-times me-1"></i>Batal
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="fas fa-save me-1"></i>Simpan Perubahan
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Card -->
            <div class="col-4">
                <div class="card">
                    <div class="card-header p-3">
                        <h6 class="mb-0">
                            <i class="fas fa-lock me-2"></i>Ubah Password
                        </h6>
                    </div>

                    <div class="card-body p-3">
                        <form id="password-form">
                            @csrf
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
                                            style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
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
                                        <div id="password-btn-loader" class="spinner-border spinner-border-sm ms-2 d-none"
                                            role="status">
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
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/profile.css') }}">
@endpush

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');
            loadProfileData(); // This should trigger first
            initProfileEdit();
            initMinatSelect();
            initPasswordFunctionality();
        });

        function loadProfileData() {
            fetch('/api/dosen/profile/data', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Profile data received:', data);
                    if (data.success) {
                        updateProfileView(data.data);
                    } else {
                        throw new Error(data.message || 'Failed to load profile data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load profile data'
                    });
                });
        }

        function updateProfileView(data) {
            // Update user data (from m_user table)
            if (data.userData) {
                const nameElements = document.querySelectorAll('[data-profile="name"]');
                const emailElements = document.querySelectorAll('[data-profile="email"]');

                nameElements.forEach(el => {
                    el.textContent = data.userData.name || '-';
                });
                emailElements.forEach(el => {
                    el.textContent = data.userData.email || '-';
                });
            }

            // Update dosen data - remove bidang_keahlian from here
            const profileElements = {
                'nip': data.dosenData?.nip || '-',
                'no_hp': data.dosenData?.no_hp || '-',
                'alamat': data.dosenData?.alamat || '-',
                'jumlah_bimbingan': data.jumlahBimbingan ? `${data.jumlahBimbingan} Mahasiswa` : '0 Mahasiswa'
            };

            // Update profile elements
            Object.entries(profileElements).forEach(([key, value]) => {
                const element = document.querySelector(`[data-profile="${key}"]`);
                if (element) {
                    element.textContent = value;
                }
            });

            // Update skills display in view mode
            const skillsViewContainer = document.querySelector('[data-profile="bidang_keahlian"]');
            if (skillsViewContainer && data.skills) {
                if (data.skills.length > 0) {
                    const skillBadges = data.skills.map(skill =>
                        `<span class="badge" style="background-color: #e3f2fd; color: #1565c0; border: 1px solid #1565c0; font-weight: 600; margin : 10px 0 0 0;">
                            ${skill.nama}
                        </span>`
                    ).join('');
                    skillsViewContainer.innerHTML = skillBadges;
                } else {
                    skillsViewContainer.innerHTML = 'Belum ada bidang keahlian yang ditambahkan';
                }
            }

            // Update minat container - Modified this part
            const minatContainer = document.getElementById('minat-container');
            if (minatContainer) {
                // Log for debugging
                console.log('Minat data:', data.minat);

                if (data.minat && data.minat.length > 0) {
                    const minatBadges = data.minat.map(item =>
                        `<span style = "background-color: #89f0b7; color: #28c76f; border: 1px solid #28c76f; margin : 10px 0 0 0" class="badge bg-light-success me-2 mb-2">${item.nama_minat}</span>`
                    ).join('');
                    console.log('Generated badges:', minatBadges);
                    minatContainer.innerHTML = minatBadges;
                } else {
                    minatContainer.innerHTML = '<p class="text-muted mb-0">Belum ada minat yang ditambahkan</p>';
                }
            } else {
                console.error('Minat container not found');
            }

            // Update skills container - Fixed this part
            const skillsContainer = document.getElementById('skills-container');
            if (skillsContainer && data.skills) {
                if (data.skills.length > 0) {
                    const skillBadges = data.skills.map(skill =>
                        `<span class="badge bg-light-primary me-2 mb-2">${skill.nama}</span>`
                    ).join('');
                    console.log('Generated skill badges:', skillBadges);
                    skillsContainer.innerHTML = skillBadges;
                } else {
                    skillsContainer.innerHTML = '<p class="text-muted mb-0">Belum ada bidang keahlian yang ditambahkan</p>';
                }
            } else {
                console.error('Skills container not found or no skills data');
            }

            // Handle Profile Completion Alert
            const alertContainer = document.getElementById('profile-completion-alert');
            const missingFieldsText = document.getElementById('missing-fields-text');

            if (alertContainer && missingFieldsText && data.profileCompletion) {
                if (!data.profileCompletion.is_complete) {
                    alertContainer.style.display = 'block';
                    missingFieldsText.textContent = data.profileCompletion.missing_fields.join(', ');
                } else {
                    alertContainer.style.display = 'none';
                }
            }
        }

        // Profile Edit Mode Toggle
        function initProfileEdit() {
            const editBtn = document.getElementById('edit-profile-btn');
            const cancelBtn = document.getElementById('cancel-edit-btn');
            const viewMode = document.getElementById('profile-view-mode');
            const editMode = document.getElementById('profile-edit-mode');

            if (editBtn) {
                editBtn.addEventListener('click', function() {
                    // Populate form fields with current data
                    document.getElementById('edit-nip').value = document.querySelector('[data-profile="nip"]')
                        .textContent.trim();
                    document.getElementById('edit-name').value = document.querySelector('[data-profile="name"]')
                        .textContent.trim();
                    document.getElementById('edit-email').value = document.querySelector('[data-profile="email"]')
                        .textContent.trim();
                    document.getElementById('edit-no_hp').value = document.querySelector('[data-profile="no_hp"]')
                        .textContent.trim() !== '-' ?
                        document.querySelector('[data-profile="no_hp"]').textContent.trim() :
                        '';

                    // Add this line to populate alamat
                    const alamatValue = document.querySelector('[data-profile="alamat"]').textContent.trim();
                    document.querySelector('textarea[name="alamat"]').value = alamatValue !== '-' ? alamatValue :
                        '';

                    // Show edit mode
                    viewMode.style.display = 'none';
                    editMode.style.display = 'block';

                    // Load other data
                    loadMinatData();
                    loadSkillsData();
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    editMode.style.display = 'none';
                    viewMode.style.display = 'block';
                });
            }
        }

        // Minat Select Functionality
        function initMinatSelect() {
            const minatSelect = document.getElementById('minat-select');
            const cancelMinatBtn = document.getElementById('cancel-minat-btn');

            // Style the native select
            minatSelect.classList.add('form-select-sm');
            minatSelect.style.height = 'auto';
            minatSelect.style.minHeight = '120px';

            // Handle form submission
            const profileForm = document.getElementById('profile-form');
            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    updateMinat();
                });
            }

            // Handle cancel button
            if (cancelMinatBtn) {
                cancelMinatBtn.addEventListener('click', function() {
                    Array.from(minatSelect.options).forEach(option => option.selected = false);
                });
            }
        }

        function loadMinatData() {
            const minatContainer = document.getElementById('minat-buttons-container');
            if (!minatContainer) return;

            minatContainer.innerHTML =
                '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            fetch('/api/dosen/profile/minat')
                .then(response => response.json())
                .then(data => {
                    minatContainer.innerHTML = '';

                    if (data.success) {
                        // Create buttons for all minat
                        data.all_minat.forEach(minat => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'minat-button';
                            button.dataset.minatId = minat.minat_id;
                            button.innerHTML = `
                                ${minat.nama_minat}
                                <button type="button" class="remove-minat">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;

                            // Check if this minat is selected
                            if (data.dosen_minat && data.dosen_minat.some(m => m.minat_id === minat.minat_id)) {
                                button.classList.add('active');
                            }

                            // Add click handler
                            button.addEventListener('click', function(e) {
                                if (e.target.closest('.remove-minat')) {
                                    // Handle remove button click
                                    this.classList.remove('active');
                                } else {
                                    // Handle main button click
                                    this.classList.toggle('active');
                                }
                            });

                            minatContainer.appendChild(button);
                        });
                    } else {
                        throw new Error(data.message || 'Gagal memuat data minat');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    minatContainer.innerHTML = '<p class="text-danger mb-0">Error memuat data minat</p>';

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Gagal memuat data minat'
                    });
                });
        }

        function updateMinat() {
            const selectedMinat = Array.from(document.querySelectorAll('.minat-button.active'))
                .map(button => button.dataset.minatId);

            fetch('/api/dosen/profile/minat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        minat_ids: selectedMinat
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide edit mode and show view mode
                        document.getElementById('profile-edit-mode').style.display = 'none';
                        document.getElementById('profile-view-mode').style.display = 'block';

                        // Reload profile data to update the view
                        loadProfileData();

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Minat berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update minat');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to update minat'
                    });
                });
        }

        // Add this after loadMinatData function
        function loadSkillsData() {
            const skillsContainer = document.getElementById('skills-buttons-container');
            if (!skillsContainer) return;

            skillsContainer.innerHTML =
                '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

            fetch('/api/dosen/profile/skills')
                .then(response => response.json())
                .then(data => {
                    skillsContainer.innerHTML = '';

                    if (data.success) {
                        data.all_skills.forEach(skill => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'skill-button';
                            button.dataset.skillId = skill.skill_id;
                            button.innerHTML = `
                                ${skill.nama}
                                <button type="button" class="remove-skill">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;

                            if (data.dosen_skills && data.dosen_skills.some(s => s.skill_id === skill
                                    .skill_id)) {
                                button.classList.add('active');
                            }

                            button.addEventListener('click', function(e) {
                                if (e.target.closest('.remove-skill')) {
                                    this.classList.remove('active');
                                } else {
                                    this.classList.toggle('active');
                                }
                            });

                            skillsContainer.appendChild(button);
                        });
                    } else {
                        throw new Error(data.message || 'Gagal memuat data skills');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    skillsContainer.innerHTML = '<p class="text-danger mb-0">Error memuat data skills</p>';

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Gagal memuat data skills'
                    });
                });
        }

        // Add updateSkills function
        function updateSkills() {
            const selectedSkills = Array.from(document.querySelectorAll('.skill-button.active'))
                .map(button => button.dataset.skillId);

            fetch('/api/dosen/profile/skills', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        skill_ids: selectedSkills
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadProfileData();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Bidang keahlian berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update skills');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to update skills'
                    });
                });
        }

        // Password Functionality
        function initPasswordFunctionality() {
            // Password toggle visibility
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

            // Password strength meter
            const newPassword = document.getElementById('new-password');
            const confirmPassword = document.getElementById('confirm-password');
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');
            const matchMessage = document.getElementById('password-match-message');

            if (newPassword) {
                newPassword.addEventListener('input', function() {
                    checkPasswordStrength(this.value);
                    checkPasswordMatch();
                });
            }

            if (confirmPassword) {
                confirmPassword.addEventListener('input', checkPasswordMatch);
            }

            function checkPasswordStrength(password) {
                let strength = 0;
                const checks = {
                    length: password.length >= 8,
                    letter: /[a-zA-Z]/.test(password),
                    digit: /[0-9]/.test(password),
                    special: /[^A-Za-z0-9]/.test(password)
                };

                strength += checks.length ? 25 : 0;
                strength += checks.letter ? 25 : 0;
                strength += checks.digit ? 25 : 0;
                strength += checks.special ? 25 : 0;

                strengthBar.style.width = strength + '%';

                if (strength < 25) {
                    strengthBar.className = 'progress-bar bg-danger';
                    strengthText.textContent = 'Password sangat lemah';
                } else if (strength < 50) {
                    strengthBar.className = 'progress-bar bg-warning';
                    strengthText.textContent = 'Password lemah';
                } else if (strength < 75) {
                    strengthBar.className = 'progress-bar bg-info';
                    strengthText.textContent = 'Password sedang';
                } else {
                    strengthBar.className = 'progress-bar bg-success';
                    strengthText.textContent = 'Password kuat';
                }
            }

            function checkPasswordMatch() {
                if (confirmPassword.value === '') {
                    matchMessage.innerHTML = '';
                    return;
                }

                if (newPassword.value === confirmPassword.value) {
                    matchMessage.innerHTML =
                        '<small class="text-success"><i class="fas fa-check me-1"></i>Password cocok</small>';
                } else {
                    matchMessage.innerHTML =
                        '<small class="text-danger"><i class="fas fa-times me-1"></i>Password tidak cocok</small>';
                }
            }

            // Password form submission
            const passwordForm = document.getElementById('password-form');
            if (passwordForm) {
                passwordForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    updatePassword(this);
                });
            }
        }

        function updatePassword(form) {
            const btnText = document.getElementById('password-btn-text');
            const btnLoader = document.getElementById('password-btn-loader');

            btnText.style.display = 'none';
            btnLoader.classList.remove('d-none');

            const formData = new FormData(form);

            fetch('/api/dosen/profile/password', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    btnText.style.display = 'block';
                    btnLoader.classList.add('d-none');

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Password berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update password');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    btnText.style.display = 'block';
                    btnLoader.classList.add('d-none');

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to update password'
                    });
                });
        }

        // Add this before the event listener
        function updateProfile(formData) {
            fetch('/api/dosen/profile/update', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide edit mode and show view mode
                        document.getElementById('profile-edit-mode').style.display = 'none';
                        document.getElementById('profile-view-mode').style.display = 'block';

                        // Reload profile data
                        loadProfileData();

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Profil berhasil diperbarui',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message || 'Failed to update profile');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to update profile'
                    });
                });
        }

        // Then modify your form submit handler to include both minat and skills
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            // Add selected skills
            const selectedSkills = Array.from(document.querySelectorAll('.skill-button.active'))
                .map(button => button.dataset.skillId);
            formData.append('skill_ids', JSON.stringify(selectedSkills));

            // Add selected minat
            const selectedMinat = Array.from(document.querySelectorAll('.minat-button.active'))
                .map(button => button.dataset.minatId);
            formData.append('minat_ids', JSON.stringify(selectedMinat));

            // Submit the form
            updateProfile(formData);
        });
    </script>
@endpush
