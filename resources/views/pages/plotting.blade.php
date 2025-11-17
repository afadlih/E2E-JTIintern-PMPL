@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Plotting Dosen'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between">
                            <h6>Plotting Manual Dosen</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Replace the existing filter row with this improved version -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label d-flex align-items-center">
                                    <i class="fas fa-search text-primary me-2"></i>
                                    <span>Cari Dosen</span>
                                </label>
                                <div class="input-group input-group-dynamic">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchDosen"
                                        placeholder="Nama dosen atau NIP">
                                    <button class="btn btn-sm btn-outline-secondary border-0" type="button"
                                        id="clearSearch" style="display:none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- Remove or comment out wilayah filter section
                                                                                                                    <div class="col-md-6">
                                                                                                                        <label class="form-label d-flex align-items-center">
                                                                                                                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                                                                                                            <span>Filter Wilayah</span>
                                                                                                                        </label>
                                                                                                                        <select class="form-select" id="wilayahFilter">
                                                                                                                            <option value="">Semua Wilayah</option>
                                                                                                                            <!-- Will be populated dynamically
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    -->
                        </div>

                        <!-- Update table class and structure -->
                        <div class="table-responsive">
                            <table class="table plotting-table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                            </div>
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">Dosen</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">Mahasiswa
                                        </th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7">Skills</th>
                                        <th class="text-uppercase text-secondary font-weight-bolder opacity-7 text-end">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="plotting-table-body">
                                    <!-- Data will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <span class="text-sm text-secondary">
                                    Menampilkan <span id="showingCount">0-0</span> dari <span id="totalCount">0</span> dosen
                                </span>
                            </div>
                            <ul class="pagination mb-0" id="pagination">
                                <!-- Pagination will be generated dynamically -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto Plot Section -->
        <div class="card mt-4">
            <div class="card-header pb-0">
                <h6>Plotting Otomatis dengan SAW</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-sm">
                            Plotting otomatis akan menggunakan metode SAW (Simple Additive Weighting) untuk menemukan dosen
                            pembimbing terbaik untuk setiap mahasiswa berdasarkan kriteria wilayah dan kecocokan skill.
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-primary" id="autoPlotBtn">
                            <i class="fas fa-magic me-2"></i>Auto-Plot Dosen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign Mahasiswa -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignModalLabel">Assign Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="selectedDosenId">
                    <div class="mb-3">
                        <label for="mahasiswaSelect" class="form-label">Pilih Mahasiswa</label>
                        <!-- In your modal -->
                        <select class="form-select" id="mahasiswaSelect" multiple size="5">
                            <!-- Increased size for better visibility -->
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveAssignBtn">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/plotting.css') }}">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Variabel global
        let allDosen = [];
        let filteredDosen = [];
        let allWilayah = [];
        let currentPage = 1;
        let searchTerm = '';
        let selectedWilayahId = '';
        const itemsPerPage = 10;

        // Add clear search button functionality
        document.getElementById('searchDosen').addEventListener('input', function() {
            const clearBtn = document.getElementById('clearSearch');
            if (this.value) {
                clearBtn.style.display = 'block';
            } else {
                clearBtn.style.display = 'none';
            }
        });

        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('searchDosen').value = '';
            searchTerm = '';
            applyFilters();
            this.style.display = 'none';
        });

        // Add hover effects for buttons
        document.addEventListener('DOMContentLoaded', function() {
            // Initial load
            loadPlottingData();

            // Add event delegation for tooltip behavior
            document.body.addEventListener('mouseover', function(e) {
                if (e.target.closest('[data-tooltip]')) {
                    const tooltip = e.target.closest('[data-tooltip]');
                    tooltip.classList.add('tooltip-active');
                }
            });

            document.body.addEventListener('mouseout', function(e) {
                if (e.target.closest('[data-tooltip]')) {
                    const tooltip = e.target.closest('[data-tooltip]');
                    tooltip.classList.remove('tooltip-active');
                }
            });
        });

        function loadPlottingData() {
            // Show loading state
            // Show loading state with improved animation
            document.getElementById('plotting-table-body').innerHTML = `
                                                                                                            <tr>
                                                                                                                <td colspan="6" class="text-center py-5">
                                                                                                                    <div class="spinner-grow text-primary mb-2" role="status" style="width: 3rem; height: 3rem;">
                                                                                                                        <span class="visually-hidden">Loading...</span>
                                                                                                                    </div>
                                                                                                                    <div class="d-flex justify-content-center">
                                                                                                                        <div class="spinner-grow text-secondary mx-1" style="width: 1rem; height: 1rem;"></div>
                                                                                                                        <div class="spinner-grow text-secondary mx-1" style="width: 1rem; height: 1rem; animation-delay: 0.2s"></div>
                                                                                                                        <div class="spinner-grow text-secondary mx-1" style="width: 1rem; height: 1rem; animation-delay: 0.4s"></div>
                                                                                                                    </div>
                                                                                                                    <p class="mt-3 text-secondary">Memuat data plotting...</p>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        `;

            // Use existing endpoint instead of new one
            fetch('/api/dosen/with-perusahaan?t=' + new Date().getTime(), {
                    headers: {
                        'Content-Type': 'application/json',
                        'Cache-Control': 'no-store, no-cache',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('API response status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        allDosen = data.data;
                        filteredDosen = [...allDosen];
                        renderDosenTable();
                        setupPagination();
                        // Remove this line since we don't need wilayah data anymore
                        // loadWilayahData(); 
                    } else {
                        throw new Error(data.message || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error loading plotting data:', error);
                    document.getElementById('plotting-table-body').innerHTML =
                        `
                                                                                                                            <tr>
                                                                                                                                <td colspan="5" class="text-center text-danger">
                                                                                                                                    Gagal memuat data: ${error.message}
                                                                                                                                </td>
                                                                                                                            </tr>
                                                                                                                        `;
                });
        }

        // 1. Add a helper function to check if elements exist before accessing them
        function elementExists(id) {
            return document.getElementById(id) !== null;
        }

        // 2. Update the loadWilayahData function
        function loadWilayahData() {
            console.log('Fetching wilayah data...');

            // Skip this function entirely since wilayahFilter no longer exists
            if (!elementExists('wilayahFilter')) {
                console.log('wilayahFilter element does not exist, skipping wilayah data load');
                return;
            }

            // Rest of the function (will never execute if the element doesn't exist)
            fetch('/api/wilayah')
                .then(response => {
                    console.log('Wilayah API response status:', response.status);
                    if (!response.ok) {
                        throw new Error('API response status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Wilayah data received:', data);
                    if (data.success) {
                        allWilayah = data.data;

                        // Populate wilayah filter
                        const wilayahFilter = document.getElementById('wilayahFilter');
                        wilayahFilter.innerHTML = '<option value="">Semua Wilayah</option>';

                        if (allWilayah && allWilayah.length > 0) {
                            allWilayah.forEach(wilayah => {
                                const option = document.createElement('option');
                                option.value = wilayah.id_wilayah || wilayah.wilayah_id;
                                option.textContent = wilayah.nama_wilayah || wilayah.nama_kota || wilayah.name;
                                wilayahFilter.appendChild(option);
                            });
                            console.log(`Added ${allWilayah.length} wilayah options to dropdown`);
                        } else {
                            console.warn('No wilayah data found in API response');
                        }
                    } else {
                        console.error('API returned success: false', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading wilayah data:', error);
                    // Add fallback data if API fails
                    addFallbackWilayahData();
                });
        }

        // 3. Fix the addFallbackWilayahData function
        function addFallbackWilayahData() {
            // Check if element exists first
            if (!elementExists('wilayahFilter')) {
                console.log('wilayahFilter element does not exist, skipping fallback data');
                return;
            }

            // Rest of the function (will never execute if the element doesn't exist)
            const wilayahFilter = document.getElementById('wilayahFilter');
            if (wilayahFilter.options.length <= 1) {
                // Only add fallback if dropdown is empty
                wilayahFilter.innerHTML = '<option value="">Semua Wilayah</option>';
                wilayahFilter.innerHTML += '<option value="1">Jakarta</option>';
                wilayahFilter.innerHTML += '<option value="2">Bandung</option>';
                wilayahFilter.innerHTML += '<option value="3">Surabaya</option>';
                console.log('Added fallback wilayah options');
            }
        }

        // 4. Fix the applyFilters function to not use wilayahFilter
        function applyFilters() {
            // Apply only search filter (without wilayah filter)
            filteredDosen = allDosen.filter(dosen => {
                const userName = typeof dosen.name === 'string' ? dosen.name : '';
                const nipValue = typeof dosen.nip === 'string' ? dosen.nip : '';

                return !searchTerm ||
                    userName.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    nipValue.toLowerCase().includes(searchTerm.toLowerCase());
            });

            // Reset to first page and update UI
            currentPage = 1;
            renderDosenTable();
            setupPagination();

            // Update filter status indicator
            updateFilterStatus();
        }

        function updateFilterStatus() {
            const statusContainer = document.getElementById('filterStatus') ||
                createFilterStatusElement();

            if (searchTerm) {
                let statusText = `Menampilkan ${filteredDosen.length} dari ${allDosen.length} dosen`;

                if (searchTerm) {
                    statusText += ` (Filter: "${searchTerm}")`;
                }

                statusContainer.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <span>${statusText}</span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                            <i class="fas fa-times me-1"></i>Reset Filter
                        </button>
                    </div>
                `;
                statusContainer.style.display = 'block';
            } else {
                statusContainer.style.display = 'none';
            }
        }

        // Create filter status element if it doesn't exist
        function createFilterStatusElement() {
            const filtersRow = document.querySelector('.row.g-4.mb-4');
            const statusDiv = document.createElement('div');
            statusDiv.id = 'filterStatus';
            statusDiv.className = 'alert alert-info mt-2';
            statusDiv.style.display = 'none';
            filtersRow.insertAdjacentElement('afterend', statusDiv);
            return statusDiv;
        }

        // 6. Fix the resetFilters function
        function resetFilters() {
            document.getElementById('searchDosen').value = '';
            // Remove reference to wilayahFilter
            // document.getElementById('wilayahFilter').value = '';
            searchTerm = '';
            selectedWilayahId = '';
            filteredDosen = [...allDosen];
            currentPage = 1;
            renderDosenTable();
            setupPagination();
            updateFilterStatus();
        }

        // Enhance the loadWilayahData function with better error handling
        function loadWilayahData() {
            console.log('Fetching wilayah data...');

            fetch('/api/wilayah')
                .then(response => {
                    console.log('Wilayah API response status:', response.status);
                    if (!response.ok) {
                        throw new Error('API response status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Wilayah data received:', data);
                    if (data.success) {
                        allWilayah = data.data;

                        // Populate wilayah filter
                        const wilayahFilter = document.getElementById('wilayahFilter');
                        wilayahFilter.innerHTML = '<option value="">Semua Wilayah</option>';

                        if (allWilayah && allWilayah.length > 0) {
                            allWilayah.forEach(wilayah => {
                                const option = document.createElement('option');
                                option.value = wilayah.id_wilayah || wilayah.wilayah_id;
                                option.textContent = wilayah.nama_wilayah || wilayah.nama_kota || wilayah.name;
                                wilayahFilter.appendChild(option);
                            });
                            console.log(`Added ${allWilayah.length} wilayah options to dropdown`);
                        } else {
                            console.warn('No wilayah data found in API response');
                        }
                    } else {
                        console.error('API returned success: false', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error loading wilayah data:', error);
                    // Add fallback data if API fails
                    addFallbackWilayahData();
                });
        }

        // Update the renderDosenTable function
        function renderDosenTable() {
            const tableBody = document.getElementById('plotting-table-body');
            tableBody.innerHTML = '';

            // Calculate start and end indices for current page (unchanged)
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredDosen.length);

            document.getElementById('showingCount').textContent =
                filteredDosen.length > 0 ? `${startIndex + 1}-${endIndex}` : '0-0';
            document.getElementById('totalCount').textContent = filteredDosen.length;

            // If no data
            if (filteredDosen.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <img src="/assets/img/empty-data.svg" alt="No Data" class="mb-3" style="height: 120px">
                            <p class="mb-0">Tidak ada data dosen yang sesuai dengan pencarian</p>
                        </td>
                    </tr>
                `;
                return;
            }

            // Render data for current page
            for (let i = startIndex; i < endIndex; i++) {
                const dosen = filteredDosen[i];

                // Count of students supervised
                let bimbinganCount = 0;
                if (Array.isArray(dosen.magang_bimbingan)) {
                    bimbinganCount = dosen.magang_bimbingan.length;
                } else if (Array.isArray(dosen.magangBimbingan)) {
                    bimbinganCount = dosen.magangBimbingan.length;
                }

                // Badge for student count with appropriate color
                const bimbinganBadge = bimbinganCount > 0 ?
                    `<span class="badge rounded-pill bg-primary badge-count" data-tooltip="${bimbinganCount} mahasiswa bimbingan">${bimbinganCount}</span>` :
                    `<span class="badge rounded-pill bg-light text-dark badge-count" data-tooltip="Belum ada mahasiswa bimbingan">0</span>`;

                // Format skills with a better visual style
                const skillsList = dosen.skills && dosen.skills.length > 0 ?
                    dosen.skills.map(s => `<span class="badge-skill">${s.skill.nama_skill}</span>`).join('') :
                    '<span class="text-muted fst-italic">Belum ada</span>';

                // Create row with improved styling - FIXED NAME ACCESS and REMOVED WILAYAH COLUMN
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        <div class="form-check">
                            <input class="form-check-input dosen-checkbox" type="checkbox" value="${dosen.id_dosen}">
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm rounded-circle bg-gradient-primary me-3">
                                <span class="text-white">${(dosen.name ? dosen.name.charAt(0) : '?')}</span>
                            </div>
                            <div>
                                <h6 class="dosen-name mb-0">${dosen.name || 'Tidak diketahui'}</h6>
                                <p class="dosen-nip mb-0">${dosen.nip || '-'}</p>
                            </div>
                        </div>
                    </td>
                    <td>${bimbinganBadge}</td>
                    <td>
                        <div class="badge-container">
                            ${skillsList}
                        </div>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" onclick="assignMahasiswa('${dosen.id_dosen}')" 
                                data-tooltip="Assign mahasiswa">
                                <i class="fas fa-link me-1"></i>Assign
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="removeAssignments('${dosen.id_dosen}')"
                                data-tooltip="Reset semua penugasan">
                                <i class="fas fa-trash me-1"></i>Reset
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            }

            // Setup checkbox listeners
            setupCheckboxListeners();
        }

        // Setup pagination
        function setupPagination() {
            const pagination = document.getElementById('pagination');
            const totalPages = Math.ceil(filteredDosen.length / itemsPerPage);

            pagination.innerHTML = '';

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML =
                `<a class="page-link" href="#" aria-label="Previous">
                                                                                                                                                                                                                                                                    <span aria-hidden="true">&laquo;</span>
                                                                                                                                                                                                                                                                </a>`;
            prevLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage > 1) {
                    currentPage--;
                    renderDosenTable();
                    setupPagination();
                }
            });
            pagination.appendChild(prevLi);

            // Page numbers (show max 5 pages)
            const maxVisiblePages = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
            let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

            if (endPage - startPage + 1 < maxVisiblePages) {
                startPage = Math.max(1, endPage - maxVisiblePages + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                pageLi.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage = i;
                    renderDosenTable();
                    setupPagination();
                });
                pagination.appendChild(pageLi);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            nextLi.innerHTML =
                `<a class="page-link" href="#" aria-label="Next">
                                                                                                                                                                                                                                                                    <span aria-hidden="true">&raquo;</span>
                                                                                                                                                                                                                                                                </a>`;
            nextLi.addEventListener('click', (e) => {
                e.preventDefault();
                if (currentPage < totalPages) {
                    currentPage++;
                    renderDosenTable();
                    setupPagination();
                }
            });
            pagination.appendChild(nextLi);
        }

        // Setup checkbox listeners
        function setupCheckboxListeners() {
            // Select all checkbox
            const selectAllCheckbox = document.getElementById('selectAll');
            const dosenCheckboxes = document.querySelectorAll('.dosen-checkbox');

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    dosenCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }

            // Check if all checkboxes are checked
            dosenCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (selectAllCheckbox) {
                        const allChecked = Array.from(dosenCheckboxes).every(c => c.checked);
                        const anyChecked = Array.from(dosenCheckboxes).some(c => c.checked);

                        selectAllCheckbox.checked = allChecked;
                        selectAllCheckbox.indeterminate = anyChecked && !allChecked;
                    }
                });
            });
        }

        // Filter dosen by name or NIP
        document.getElementById('searchDosen').addEventListener('input', function() {
            searchTerm = this.value.toLowerCase().trim(); // Only update the searchTerm variable
            applyFilters(); // Let applyFilters handle the actual filtering
        });

        // 5. Fix the event listener for wilayahFilter
        // Comment out or remove this code:
        /*
        document.getElementById('wilayahFilter').addEventListener('change', function () {
            selectedWilayahId = this.value; 
            applyFilters();
        });
        */

        function assignMahasiswa(dosenId) {
            // Set selected dosen ID
            document.getElementById('selectedDosenId').value = dosenId;

            // Show loading in SweetAlert first
            Swal.fire({
                title: 'Memuat Data',
                html: 'Sedang mengambil data mahasiswa yang tersedia...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch available mahasiswa with AJAX
            fetch('/api/magang/available', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Cache-Control': 'no-store, no-cache'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    // Close loading dialog
                    Swal.close();

                    if (data.success) {
                        if (data.data.length === 0) {
                            // Show notification about no available students
                            Swal.fire({
                                title: 'Perhatian',
                                text: 'Tidak ada mahasiswa yang tersedia untuk ditugaskan',
                                icon: 'info'
                            });
                        } else {
                            // ✅ BARU: Show table-based selection in SweetAlert
                            showMahasiswaTableSelection(data.data, dosenId);
                        }
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: `Gagal memuat data mahasiswa: ${data.message || 'Unknown error'}`,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching available magang:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memuat data mahasiswa: ' + error.message,
                        icon: 'error'
                    });
                });
        }

        // ✅ BARU: Function untuk menampilkan tabel mahasiswa sederhana dalam SweetAlert
        function showMahasiswaTableSelection(mahasiswaData, dosenId) {
            // Extract unique companies for filter
            const uniqueCompanies = [...new Set(mahasiswaData.map(item => item.nama_perusahaan || 'Tidak diketahui'))];

            // Create company filter options
            let companyFilterOptions = '<option value="">Semua Perusahaan</option>';
            uniqueCompanies.forEach(company => {
                companyFilterOptions += `<option value="${company}">${company}</option>`;
            });

            // Create simplified table HTML
            let tableHtml = `
                <div class="container-fluid px-0">
                    <div class="row mb-3">
                            <div class="form-check mb-2">
                            </div>
                      <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Filter Perusahaan:</label>
                            </div>
                            <div class="col-md-9">
                                <select class="form-select form-select-sm" id="companyFilter">
                                    ${companyFilterOptions}
                                </select>
                            </div>
                        </div>
                    </div>
                    </div>
                    
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th width="50">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllTableHeader">
                                        </div>
                                    </th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Perusahaan</th>
                                    <th>Wilayah</th>
                                </tr>
                            </thead>
                            <tbody id="mahasiswaTableBody">
            `;

            // Add each mahasiswa as a simplified table row
            mahasiswaData.forEach((item, index) => {
                const mahasiswaName = item.name || 'Tidak diketahui';
                const perusahaanName = item.nama_perusahaan || 'Tidak diketahui';
                const wilayahName = item.nama_kota || item.wilayah || 'Tidak diketahui';

                tableHtml += `
                    <tr class="mahasiswa-row" data-mahasiswa-id="${item.id_magang}" data-company="${perusahaanName}">
                        <td>
                            <div class="form-check">
                                <input class="form-check-input mahasiswa-checkbox" 
                                       type="checkbox" 
                                       value="${item.id_magang}" 
                                       id="mhs_${item.id_magang}">
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="fw-semibold">${mahasiswaName}</div>
                                <small class="text-muted">${item.nim || '-'}</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-medium">${perusahaanName}</div>
                            <small class="text-muted">${item.judul_lowongan || 'Posisi tidak tersedia'}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary rounded-pill">${wilayahName}</span>
                        </td>
                    </tr>
                `;
            });

            tableHtml += `
                    </tbody>
                </table>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Total: <span id="totalMahasiswa">${mahasiswaData.length}</span> mahasiswa tersedia | 
                            Tampil: <span id="visibleCount">${mahasiswaData.length}</span> mahasiswa |
                            Dipilih: <span id="selectedCount">0</span> mahasiswa
                        </small>
                    </div>
                </div>
            </div>
        </div>
    `;

            // Show SweetAlert with simplified table
            Swal.fire({
                title: '<i class="fas fa-users me-2"></i>Assign Mahasiswa',
                html: tableHtml,
                width: '800px',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-save me-1"></i>Simpan Penugasan',
                cancelButtonText: '<i class="fas fa-times me-1"></i>Batal',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                customClass: {
                    popup: 'swal-wide',
                    content: 'swal-table-content'
                },
                preConfirm: () => {
                    // Get selected mahasiswa IDs (only visible rows)
                    const selectedCheckboxes = document.querySelectorAll('.mahasiswa-checkbox:checked');
                    const visibleSelectedCheckboxes = Array.from(selectedCheckboxes).filter(cb => {
                        return cb.closest('tr').style.display !== 'none';
                    });
                    const selectedIds = visibleSelectedCheckboxes.map(cb => cb.value);

                    if (selectedIds.length === 0) {
                        Swal.showValidationMessage('Pilih minimal satu mahasiswa');
                        return false;
                    }

                    return selectedIds;
                },
                didOpen: () => {
                    // Setup checkbox behaviors and filter after modal opens
                    setupMahasiswaTableBehaviors();
                    setupCompanyFilter();
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Process the assignment
                    saveMahasiswaAssignment(dosenId, result.value);
                }
            });
        }

        // ✅ BARU: Setup company filter functionality
        function setupCompanyFilter() {
            const companyFilter = document.getElementById('companyFilter');
            const allRows = document.querySelectorAll('.mahasiswa-row');
            const visibleCountElement = document.getElementById('visibleCount');

            if (companyFilter) {
                companyFilter.addEventListener('change', function() {
                    const selectedCompany = this.value;
                    let visibleCount = 0;

                    allRows.forEach(row => {
                        const rowCompany = row.getAttribute('data-company');

                        if (selectedCompany === '' || rowCompany === selectedCompany) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                            // Uncheck hidden rows
                            const checkbox = row.querySelector('.mahasiswa-checkbox');
                            if (checkbox) {
                                checkbox.checked = false;
                            }
                        }
                    });

                    // Update visible count
                    if (visibleCountElement) {
                        visibleCountElement.textContent = visibleCount;
                    }

                    // Update selected count and select all state
                    updateSelectedCountAndSelectAll();
                });
            }
        }

        // ✅ UPDATE: Enhanced checkbox behaviors dengan filter support
        function setupMahasiswaTableBehaviors() {
            const selectAllMain = document.getElementById('selectAllMahasiswa');
            const selectAllHeader = document.getElementById('selectAllTableHeader');
            const selectedCountElement = document.getElementById('selectedCount');

            // Function to get visible checkboxes only
            function getVisibleCheckboxes() {
                const allCheckboxes = document.querySelectorAll('.mahasiswa-checkbox');
                return Array.from(allCheckboxes).filter(cb => {
                    return cb.closest('tr').style.display !== 'none';
                });
            }

            // Function to update selected count and select all state
            function updateSelectedCountAndSelectAll() {
                const visibleCheckboxes = getVisibleCheckboxes();
                const checkedVisibleCount = visibleCheckboxes.filter(cb => cb.checked).length;

                // Update selected count
                if (selectedCountElement) {
                    selectedCountElement.textContent = checkedVisibleCount;
                }

                // Update select all checkboxes state (only for visible items)
                const allVisibleChecked = visibleCheckboxes.length > 0 && checkedVisibleCount === visibleCheckboxes.length;
                const someVisibleChecked = checkedVisibleCount > 0;

                if (selectAllMain) {
                    selectAllMain.checked = allVisibleChecked;
                    selectAllMain.indeterminate = someVisibleChecked && !allVisibleChecked;
                }

                if (selectAllHeader) {
                    selectAllHeader.checked = allVisibleChecked;
                    selectAllHeader.indeterminate = someVisibleChecked && !allVisibleChecked;
                }
            }

            // Make updateSelectedCountAndSelectAll globally accessible
            window.updateSelectedCountAndSelectAll = updateSelectedCountAndSelectAll;

            // Select all functionality (only visible items)
            function toggleAllVisibleCheckboxes(checked) {
                const visibleCheckboxes = getVisibleCheckboxes();
                visibleCheckboxes.forEach(checkbox => {
                    checkbox.checked = checked;
                });
                updateSelectedCountAndSelectAll();
            }

            // Event listeners for select all checkboxes
            if (selectAllMain) {
                selectAllMain.addEventListener('change', function() {
                    toggleAllVisibleCheckboxes(this.checked);
                });
            }

            if (selectAllHeader) {
                selectAllHeader.addEventListener('change', function() {
                    toggleAllVisibleCheckboxes(this.checked);
                });
            }

            // Event listeners for individual checkboxes
            document.querySelectorAll('.mahasiswa-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCountAndSelectAll);
            });

            // Initial count update
            updateSelectedCountAndSelectAll();

            // Row click to toggle checkbox (only for visible rows)
            document.querySelectorAll('.mahasiswa-row').forEach(row => {
                row.addEventListener('click', function(e) {
                    // Don't trigger if clicking on checkbox directly or if row is hidden
                    if (e.target.type !== 'checkbox' && this.style.display !== 'none') {
                        const checkbox = this.querySelector('.mahasiswa-checkbox');
                        if (checkbox) {
                            checkbox.checked = !checkbox.checked;
                            updateSelectedCountAndSelectAll();
                        }
                    }
                });

                // Add hover effect
                row.style.cursor = 'pointer';
            });
        }

        // ✅ BARU: Function untuk save assignment dengan loading state
        function saveMahasiswaAssignment(dosenId, selectedMahasiswaIds) {
            // Show loading
            Swal.fire({
                title: 'Menyimpan...',
                html: 'Sedang menyimpan penugasan mahasiswa',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX request to save assignments
            fetch(`/api/dosen/${dosenId}/assign-mahasiswa`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        magang_ids: selectedMahasiswaIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                        <div class="text-center">
                            <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                            <p>Penugasan berhasil disimpan</p>
                            <div class="alert alert-success py-2">
                                <small><strong>${selectedMahasiswaIds.length}</strong> mahasiswa berhasil ditugaskan</small>
                            </div>
                        </div>
                    `,
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // Reload plotting data
                        loadPlottingData();
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan tidak diketahui',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saving assignments:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menyimpan penugasan: ' + error.message,
                        icon: 'error'
                    });
                });
        }

        // Add this function after saveMahasiswaAssignment function

        // Function untuk menghapus semua penugasan satu dosen
        function removeAssignments(dosenId) {
            // Confirm deletion first
            Swal.fire({
                title: 'Hapus Penugasan?',
                html: 'Anda yakin ingin menghapus <strong>semua</strong> penugasan mahasiswa dari dosen ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i>Ya, Hapus Semua',
                cancelButtonText: '<i class="fas fa-times me-1"></i>Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        html: 'Sedang menghapus penugasan mahasiswa',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX request to remove assignments
                    fetch(`/api/dosen/${dosenId}/remove-assignments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                Swal.fire({
                                    title: 'Berhasil!',
                                    html: `
                            <div class="text-center">
                                <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem;"></i>
                                <p>Penugasan berhasil dihapus</p>
                            </div>
                        `,
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });

                                // Reload plotting data
                                loadPlottingData();
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan tidak diketahui',
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error removing assignments:', error);
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus penugasan: ' + error.message,
                                icon: 'error'
                            });
                        });
                }
            });
        }

        // 2. Event Listeners untuk Matrix dan Auto Plot
        document.addEventListener('DOMContentLoaded', function() {

            // Event listener untuk Auto Plot button
            const autoPlotBtn = document.getElementById('autoPlotBtn');
            if (autoPlotBtn) {
                autoPlotBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: 'Konfirmasi Plotting Otomatis',
                        text: 'Apakah Anda yakin ingin melakukan plotting otomatis dengan metode SAW? Ini akan mengganti semua plotting manual yang ada.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, lakukan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            handleAutoPlot();
                        }
                    });
                });
            }

            // Event listeners untuk tabs
            document.getElementById('active-tab')?.addEventListener('click', () => loadMatrixData('active'));
            document.getElementById('pending-tab')?.addEventListener('click', () => loadMatrixData('pending'));
        });

        // 3. Function untuk handle auto plot
        function handleAutoPlot() {
            const btn = document.getElementById('autoPlotBtn');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Memproses...`;

            Swal.fire({
                title: 'Memproses',
                html: 'Sedang melakukan plotting otomatis...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/api/plotting/auto', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fas fa-magic me-2"></i>Auto-Plot Dosen`;

                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            html: `
                            <p>Plotting otomatis berhasil dilakukan!</p>
                            <div class="mt-3">
                                <table class="table table-sm">
                                    <tr><td>Total Dosen</td><td>${data.stats.total_dosen}</td></tr>
                                    <tr><td>Total Magang</td><td>${data.stats.total_magang}</td></tr>
                                    <tr><td>Total Assignments</td><td>${data.stats.total_assignments}</td></tr>
                                </table>
                            </div>
                        `,
                            icon: 'success'
                        });
                        loadPlottingData(); // Reload data setelah berhasil
                    } else {
                        Swal.fire({
                            title: 'Gagal!',
                            text: data.message || 'Terjadi kesalahan tidak diketahui',
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error during auto-plot:', error);
                    btn.disabled = false;
                    btn.innerHTML = `<i class="fas fa-magic me-2"></i>Auto-Plot Dosen`;

                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat melakukan plotting otomatis: ' + error.message,
                        icon: 'error'
                    });
                });
        }

        // Perbaikan fungsi loadMatrixData
        function loadMatrixData() {
            const container = document.getElementById('matrixContainer');

            if (!container) {
                console.error('Matrix container not found');
                return;
            }

            const spinnerHtml = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-secondary">Memuat perhitungan matrix SAW...</p>
                </div>
            `;

            container.innerHTML = spinnerHtml;

            // Update endpoint untuk mendapatkan hanya mahasiswa yang belum di-assign
            fetch('/api/matrix/unassigned', {
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderMatrixCalculation(data.data, data.weights);
                    } else {
                        container.innerHTML = `
                        <div class="alert alert-info">
                            <i class="fas fa-check-circle me-2"></i>
                            Semua mahasiswa sudah memiliki dosen pembimbing
                        </div>
                    `;
                    }
                })
                .catch(error => {
                    console.error('Error loading matrix data:', error);
                    container.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Terjadi kesalahan saat memuat data: ${error.message}
                    </div>
                `;
                });
        }

        function renderMatrixTable(matrixData, containerId) {
            if (!Array.isArray(matrixData) || matrixData.length === 0) {
                document.getElementById(containerId).innerHTML = `
                    <div class="text-center py-5">
                        <p class="text-muted">Tidak ada data untuk ditampilkan</p>
                    </div>
                `;
                return;
            }

            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
            `;

            // Generate table headers
            Object.keys(matrixData[0]).forEach(key => {
                html += `<th class="text-center">${key}</th>`;
            });

            html += `
                            </tr>
                        </thead>
                        <tbody>
            `;

            // Generate table rows
            matrixData.forEach(row => {
                html += `<tr>`;
                Object.values(row).forEach(value => {
                    html += `<td class="text-center">${value}</td>`;
                });
                html += `</tr>`;
            });

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            document.getElementById(containerId).innerHTML = html;
        }
    </script>
@endpush
