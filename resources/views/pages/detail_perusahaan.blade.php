@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detail Perusahaan'])
    <div class="container-fluid py-4">
        <!-- Card Info Perusahaan -->
        <div class="card mb-4">
            <div class="card-header p-4">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <!-- Replace existing logo div with this -->
                        <div class="company-logo position-relative">
                            <img src="{{ asset('images/company-placeholder.png') }}" alt="Logo Perusahaan" class="rounded"
                                width="80" height="80" id="companyLogo">

                            <!-- Hidden file input for logo upload -->
                            <input type="file" id="logoInput" accept="image/*" style="display: none;"
                                onchange="handleLogoChange(event)">

                            <!-- Edit overlay (hidden by default) -->
                            <div class="logo-edit-overlay" style="display: none;" id="logoEditOverlay">
                                <button type="button" class="btn btn-sm btn-light"
                                    onclick="document.getElementById('logoInput').click()">
                                    <i class="bi bi-camera"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <h3 class="mb-2" id="namaPerusahaan"></h3>
                        <p class="text-muted mb-2" id="deskripsiPerusahaan" style="max-width: 900px;">-</p>
                        <p class="text-muted mb-0" id="wilayah"></p>
                    </div>
                    <div class="col-auto">

                        <!-- Add save button (hidden by default) -->
                        <button class="btn" id="saveBtn" style="background: #02A232; color: white; display: none;"
                            onclick="saveChanges()">
                            <i class="bi bi-check-lg me-2"></i>Simpan
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-12">
                        <div class="detail-section mb-4">
                            <h5 class="detail-heading mb-4">Alamat</h5>
                            <p class="mb-0" id="alamatLengkap"></p>
                            <a href="#" class="text-primary mt-2 d-inline-block" id="lihatPeta" target="_blank">
                                <i class="bi bi-geo-alt me-1"></i>Lihat Peta
                            </a>
                        </div>
                        <div class="detail-section">
                            <h5 class="detail-heading mb-4">Kontak & Sosial Media</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="contact-item mb-3">
                                        <i class="bi bi-globe text-primary me-2"></i>
                                        <a href="#" id="websiteLink" target="_blank">Website</a>
                                    </div>
                                    <div class="contact-item mb-3">
                                        <i class="bi bi-instagram text-primary me-2"></i>
                                        <a href="#" id="instagramLink" target="_blank">Instagram</a>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="contact-item mb-3">
                                        <i class="bi bi-envelope text-primary me-2"></i>
                                        <a href="mailto:" id="emailLink">Email</a>
                                    </div>
                                    <div class="contact-item mb-3">
                                        <i class="bi bi-telephone text-primary me-2"></i>
                                        <span id="contactPerson"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Lowongan Pekerjaan (Terpisah) -->
        <div class="card">
            <div class="card-header p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lowongan Pekerjaan</h5>
                    <span class="badge bg-primary rounded-pill px-3" id="totalLowongan">3 Lowongan</span>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row" id="lowonganList">
                    <!-- Lowongan akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/detail_perusahaan.css') }}">

@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const perusahaanId = {{ $id }};
            loadDetailPerusahaan(perusahaanId);
        });

        function loadDetailPerusahaan(id) {
            fetch(`/api/perusahaan/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const p = data.data;
                        
                        console.log('📊 Detail perusahaan loaded:', p); // ← DEBUG
                        
                        document.getElementById('namaPerusahaan').textContent = p.nama_perusahaan;
                        document.getElementById('wilayah').textContent = p.kota;
                        document.getElementById('alamatLengkap').textContent = p.alamat_perusahaan;

                        // ✅ PERBAIKI: Update logo display
                        const logoImg = document.getElementById('companyLogo');
                        if (p.logo && p.logo !== null && p.logo !== '') {
                            let logoUrl;
                            if (p.logo.startsWith('http')) {
                                logoUrl = p.logo;
                            } else if (p.logo.startsWith('storage/')) {
                                logoUrl = `/${p.logo}`;
                            } else {
                                logoUrl = `/storage/${p.logo}`;
                            }
                            
                            console.log('🖼️ Setting logo URL:', logoUrl); // ← DEBUG
                            
                            logoImg.src = logoUrl;
                            logoImg.alt = `Logo ${p.nama_perusahaan}`;
                            logoImg.onerror = function() {
                                console.error('❌ Logo failed to load:', logoUrl);
                                this.src = '/images/company-placeholder.png';
                                this.alt = 'Logo tidak dapat dimuat';
                            };
                        } else {
                            console.log('📝 No logo found, using placeholder');
                            logoImg.src = '/images/company-placeholder.png';
                            logoImg.alt = 'Tidak ada logo';
                        }

                        // Update GMaps link
                        const lihatPeta = document.getElementById('lihatPeta');
                        if (p.gmaps) {
                            lihatPeta.href = p.gmaps;
                            lihatPeta.style.display = 'inline-block';
                        } else {
                            lihatPeta.style.display = 'none';
                        }

                        // Update description
                        const deskripsi = document.getElementById('deskripsiPerusahaan');
                        deskripsi.textContent = p.deskripsi || 'Tidak ada deskripsi perusahaan.';

                        // Update contact info
                        document.getElementById('websiteLink').href = p.website || '#';
                        document.getElementById('websiteLink').textContent = p.website || 'Tidak ada website';
                        
                        document.getElementById('instagramLink').href = p.instagram ? `https://instagram.com/${p.instagram}` : '#';
                        document.getElementById('instagramLink').textContent = p.instagram ? `@${p.instagram}` : 'Tidak ada Instagram';
                        
                        document.getElementById('emailLink').href = `mailto:${p.email}`;
                        document.getElementById('emailLink').textContent = p.email;
                        document.getElementById('contactPerson').textContent = p.contact_person;

                        // Update total lowongan badge
                        const totalLowongan = p.lowongan ? p.lowongan.length : 0;
                        document.getElementById('totalLowongan').textContent = `${totalLowongan} Lowongan`;

                        // Render lowongan if exists
                        if (p.lowongan && p.lowongan.length > 0) {
                            const lowonganHTML = p.lowongan.map(l => `
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="mb-2">${l.judul_lowongan}</h6>
                                            <p class="text-muted small mb-3">${l.deskripsi || 'Tidak ada deskripsi.'}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge bg-light text-dark">
                                                    <i class="bi bi-people me-1"></i>${l.kapasitas} Orang
                                                </span>
                                                <a href="/lowongan/${l.id_lowongan}" class="btn btn-sm btn-primary">
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                            document.getElementById('lowonganList').innerHTML = lowonganHTML;
                        } else {
                            document.getElementById('lowonganList').innerHTML = `
                                <div class="col-12">
                                    <div class="text-center py-4">
                                        <p class="text-muted mb-0">Belum ada lowongan tersedia.</p>
                                    </div>
                                </div>
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        let isEditing = false;
        let originalElements = {};

        function toggleEdit() {
            isEditing = !isEditing;
            const editBtn = document.getElementById('editBtn');
            const saveBtn = document.getElementById('saveBtn');

            // Define editable fields
            const editableFields = {
                'namaPerusahaan': { type: 'text', tag: 'h3', class: 'mb-2' },
                'deskripsiPerusahaan': { type: 'textarea', tag: 'p', class: 'text-muted mb-2' },
                'wilayah': { type: 'text', tag: 'p', class: 'text-muted mb-0' },
                'alamatLengkap': { type: 'textarea', tag: 'p', class: 'mb-0' },
                'websiteLink': { type: 'url', tag: 'a', class: 'text-primary' },
                'instagramLink': { type: 'text', tag: 'a', class: 'text-primary' },
                'emailLink': { type: 'email', tag: 'a', class: 'text-primary' },
                'contactPerson': { type: 'text', tag: 'span', class: '' },
                'lihatPeta': { type: 'url', tag: 'a', class: 'text-primary mt-2 d-inline-block' }
            };

            if (isEditing) {
                // Show logo edit overlay
                document.getElementById('logoEditOverlay').style.display = 'flex';

                // Simpan elemen asli sebelum mengubah ke mode edit
                Object.entries(editableFields).forEach(([id, field]) => {
                    const element = document.getElementById(id);
                    if (element) {
                        originalElements[id] = element.cloneNode(true);
                        const currentValue = field.type === 'url' ? element.href : element.textContent;

                        if (field.type === 'textarea') {
                            const textarea = document.createElement('textarea');
                            textarea.className = 'form-control';
                            textarea.value = currentValue;
                            textarea.id = `edit_${id}`;
                            element.parentNode.replaceChild(textarea, element);
                        } else {
                            const input = document.createElement('input');
                            input.type = field.type;
                            input.className = 'form-control';
                            input.value = currentValue;
                            input.id = `edit_${id}`;
                            element.parentNode.replaceChild(input, element);
                        }
                    }
                });

                // Ubah tampilan tombol ke mode edit
                editBtn.style.background = '#dc3545';
                editBtn.querySelector('i').classList.replace('bi-pencil-square', 'bi-x-lg');
                document.getElementById('editBtnText').textContent = 'Batal';
                saveBtn.style.display = 'inline-block';
            } else {
                // Hide logo edit overlay
                document.getElementById('logoEditOverlay').style.display = 'none';

                // Kembalikan ke tampilan non-edit menggunakan elemen yang tersimpan
                Object.entries(editableFields).forEach(([id, field]) => {
                    const editElement = document.getElementById(`edit_${id}`);
                    if (editElement && originalElements[id]) {
                        editElement.parentNode.replaceChild(originalElements[id], editElement);
                    }
                });

                // Kembalikan tampilan tombol ke mode non-edit
                editBtn.style.background = '#5988FF';
                editBtn.querySelector('i').classList.replace('bi-x-lg', 'bi-pencil-square');
                document.getElementById('editBtnText').textContent = 'Edit Data';
                saveBtn.style.display = 'none';
            }
        }

        function confirmDelete() {
            const perusahaanId = {{ $id }};
            const perusahaanName = document.getElementById('namaPerusahaan').textContent;

            Swal.fire({
                title: 'Hapus Perusahaan?',
                text: `Anda yakin ingin menghapus perusahaan "${perusahaanName}"? Semua data terkait termasuk lowongan akan ikut terhapus dan tidak dapat dikembalikan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading state
                    Swal.fire({
                        title: 'Menghapus...',
                        html: 'Sedang memproses penghapusan',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Kirim request DELETE ke server
                    fetch(`/api/perusahaan/${perusahaanId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Dihapus!',
                                    text: 'Data perusahaan berhasil dihapus',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Redirect ke halaman daftar perusahaan
                                    window.location.href = '/data_perusahaan';
                                });
                            } else {
                                throw new Error(data.message || 'Terjadi kesalahan saat menghapus data');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Menghapus',
                                text: error.message || 'Terjadi kesalahan saat menghapus data perusahaan'
                            });
                        });
                }
            });
        }

        function saveChanges() {
            const perusahaanId = {{ $id }};
            const formData = new FormData();

            // Add all text data
            const updatedData = {
                nama_perusahaan: document.getElementById('edit_namaPerusahaan').value,
                deskripsi: document.getElementById('edit_deskripsiPerusahaan').value,
                kota: document.getElementById('edit_wilayah').value,
                alamat_perusahaan: document.getElementById('edit_alamatLengkap').value,
                website: document.getElementById('edit_websiteLink').value,
                instagram: document.getElementById('edit_instagramLink').value,
                email: document.getElementById('edit_emailLink').value,
                contact_person: document.getElementById('edit_contactPerson').value,
                gmaps: document.getElementById('edit_lihatPeta').value
            };

            Object.keys(updatedData).forEach(key => {
                formData.append(key, updatedData[key]);
            });

            // Add logo if changed
            if (window.logoFile) {
                formData.append('logo', window.logoFile);
            }

            fetch(`/api/perusahaan/${perusahaanId}`, {
                method: 'POST', // Changed to POST for FormData
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    // Remove Content-Type header to let browser set it with boundary for FormData
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toggleEdit();
                        loadDetailPerusahaan(perusahaanId);
                        window.logoFile = null; // Clear stored file
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data perusahaan berhasil diperbarui'
                        });
                    } else {
                        throw new Error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                });
        }

        // Handle logo change
        function handleLogoChange(event) {
            const file = event.target.files[0];
            if (!file) return;

            console.log('📤 Processing logo upload:', {
                name: file.name,
                size: file.size,
                type: file.type
            });

            // ✅ VALIDATE file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Tidak Valid',
                    text: 'Silakan pilih file gambar (JPG, PNG, SVG, WebP)',
                    confirmButtonText: 'OK'
                });
                event.target.value = '';
                return;
            }

            // ✅ VALIDATE file size (max 2MB)
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 2MB',
                    confirmButtonText: 'OK'
                });
                event.target.value = '';
                return;
            }

            // ✅ PREVIEW the image
            const reader = new FileReader();
            reader.onload = function (e) {
                const logoImg = document.getElementById('companyLogo');
                logoImg.src = e.target.result;
                
                console.log('✅ Logo preview loaded successfully');
            };
            
            reader.onerror = function() {
                console.error('❌ Error reading file');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal membaca file gambar'
                });
            };
            
            reader.readAsDataURL(file);

            // ✅ STORE file for upload
            window.logoFile = file;
            
            console.log('📝 Logo file stored for upload');
        }
    </script>
@endpush

@push('css')
    <style>
        .form-control {
            margin-bottom: 8px;
        }

        textarea.form-control {
            min-height: 100px;
        }

        #saveBtn {
            margin-left: 8px;
        }

        .contact-item .form-control {
            width: 100%;
        }

        /* Logo edit overlay style */
        .logo-edit-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            padding: 5px;
            cursor: pointer;
            display: none;
            /* Hide by default */
        }
    </style>
@endpush