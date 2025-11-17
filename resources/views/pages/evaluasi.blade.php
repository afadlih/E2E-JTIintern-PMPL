<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\evaluasi.blade.php -->
@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Evaluasi Dosen'])
    <div class="container-fluid py-4">
        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Evaluasi</p>
                                    <h5 class="font-weight-bolder" id="total-evaluasi">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="fas fa-clipboard-check text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Rata-Rata Nilai</p>
                                    <h5 class="font-weight-bolder" id="avg-nilai">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-chart-bar text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Dosen</p>
                                    <h5 class="font-weight-bolder" id="total-dosen">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="fas fa-user-tie text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Total Perusahaan</p>
                                    <h5 class="font-weight-bolder" id="total-perusahaan">
                                        <div class="placeholder-glow">
                                            <span class="placeholder col-4"></span>
                                        </div>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-building text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header pb-0">
                <div class="d-flex align-items-center">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Evaluasi</h6>
                    <button id="reset-filters" class="btn btn-link text-secondary ms-auto mb-0">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dosen</label>
                        <select class="form-select" id="filter-dosen">
                            <option value="">Semua Dosen</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Perusahaan</label>
                        <select class="form-select" id="filter-perusahaan">
                            <option value="">Semua Perusahaan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Container -->
        <div id="loading-container" class="text-center my-5">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
            <p class="mt-3 mb-0 text-primary fw-bold">Memuat data evaluasi...</p>
            <p class="text-muted small">Mohon tunggu sebentar</p>
        </div>

        <!-- Empty State Container -->
        <div id="empty-container" class="d-none">
            <div class="card">
                <div class="card-body">
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h5 class="mb-2">Tidak ada data evaluasi</h5>
                        <p class="text-muted mb-0" id="empty-message">
                            Belum ada evaluasi yang tercatat dalam sistem
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Container -->
        <div id="error-container" class="d-none">
            <div class="card bg-light-danger">
                <div class="card-body">
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-danger mb-2" id="error-message">Terjadi kesalahan saat memuat data</h5>
                        <p class="text-muted mb-3">Coba refresh halaman atau hubungi administrator</p>
                        <button class="btn btn-primary" onclick="loadEvaluationData()">
                            <i class="fas fa-sync-alt me-2"></i>Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluasi Cards Container -->
        <div id="evaluasi-container" class="d-none">
            <div class="row" id="evaluasi-cards">
                <!-- Evaluasi cards will be inserted here -->
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Pagination">
                    <ul class="pagination" id="pagination-container"></ul>
                </nav>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/evaluasi.css') }}">
@endpush

@push('js')
    <script>
        // Variables
        let allEvaluations = [];
        let filteredEvaluations = [];
        let currentPage = 1;
        const itemsPerPage = 6;

        // Helper untuk format tanggal relatif (timeago)
        function timeAgo(date) {
            const seconds = Math.floor((new Date() - new Date(date)) / 1000);

            let interval = Math.floor(seconds / 31536000);
            if (interval >= 1) {
                return interval + " tahun yang lalu";
            }

            interval = Math.floor(seconds / 2592000);
            if (interval >= 1) {
                return interval + " bulan yang lalu";
            }

            interval = Math.floor(seconds / 86400);
            if (interval >= 1) {
                return interval + " hari yang lalu";
            }

            interval = Math.floor(seconds / 3600);
            if (interval >= 1) {
                return interval + " jam yang lalu";
            }

            interval = Math.floor(seconds / 60);
            if (interval >= 1) {
                return interval + " menit yang lalu";
            }

            return "baru saja";
        }

        // Load data evaluasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            loadEvaluationData();

            // Event listener untuk filter
            document.getElementById('filter-dosen').addEventListener('change', applyFilters);
            document.getElementById('filter-perusahaan').addEventListener('change', applyFilters);

            // Event listener untuk reset filter
            document.getElementById('reset-filters').addEventListener('click', resetFilters);
        });

        function loadEvaluationData() {
            // Tampilkan loading
            document.getElementById('loading-container').classList.remove('d-none');
            document.getElementById('empty-container').classList.add('d-none');
            document.getElementById('error-container').classList.add('d-none');
            document.getElementById('evaluasi-container').classList.add('d-none');

            // Fetch data dari API
            fetch('/api/evaluasi')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data); // Log full response

                    if (data.success) {
                        allEvaluations = Array.isArray(data.data) ? data.data : [];
                        console.log('Evaluations count:', allEvaluations.length); // Log count for debugging
                        filteredEvaluations = [...allEvaluations];

                        // Debug data structure of first item if available
                        if (allEvaluations.length > 0) {
                            console.log('First evaluation item:', allEvaluations[0]);
                        }

                        // Update statistik
                        updateStatistics(allEvaluations);

                        // Populate filter options
                        populateFilterOptions(allEvaluations);

                        // Handle data display
                        if (allEvaluations.length === 0) {
                            document.getElementById('loading-container').classList.add('d-none');
                            document.getElementById('empty-container').classList.remove('d-none');
                        } else {
                            try {
                                renderEvaluations(filteredEvaluations);
                                document.getElementById('loading-container').classList.add('d-none');
                                document.getElementById('evaluasi-container').classList.remove('d-none');
                            } catch (renderError) {
                                console.error('Error rendering evaluations:', renderError);
                                document.getElementById('error-message').textContent = 'Error rendering data: ' +
                                    renderError.message;
                                document.getElementById('loading-container').classList.add('d-none');
                                document.getElementById('error-container').classList.remove('d-none');
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Failed to load data');
                    }
                })
                .catch(error => {
                    console.error('Error loading evaluations:', error);
                    document.getElementById('loading-container').classList.add('d-none');
                    document.getElementById('error-message').textContent = 'Error: ' + error.message;
                    document.getElementById('error-container').classList.remove('d-none');
                });
        }

        // Update statistik cards
        function updateStatistics(evaluations) {
            // Total evaluasi
            document.getElementById('total-evaluasi').textContent = evaluations.length;

            // Rata-rata nilai
            const totalNilai = evaluations.reduce((sum, item) => sum + parseFloat(item.nilai || 0), 0);
            const avgNilai = evaluations.length > 0 ? (totalNilai / evaluations.length).toFixed(1) : '0.0';
            document.getElementById('avg-nilai').textContent = avgNilai;

            // Total dosen (unik)
            const uniqueDosen = new Set(evaluations.map(item => item.id_dosen)).size;
            document.getElementById('total-dosen').textContent = uniqueDosen;

            // Total perusahaan (unik)
            const uniquePerusahaan = new Set(evaluations.map(item => item.perusahaan_id)).size;
            document.getElementById('total-perusahaan').textContent = uniquePerusahaan;
        }

        // Populate filter options
        function populateFilterOptions(evaluations) {
            // Extract unique dosen
            const uniqueDosen = [];
            const seenDosen = new Set();

            evaluations.forEach(item => {
                if (item.id_dosen && !seenDosen.has(item.id_dosen)) {
                    seenDosen.add(item.id_dosen);
                    uniqueDosen.push({
                        id: item.id_dosen,
                        name: item.nama_dosen || 'Unknown'
                    });
                }
            });

            // Sort dosen by name
            uniqueDosen.sort((a, b) => a.name.localeCompare(b.name));

            // Populate dosen dropdown
            const dosenSelect = document.getElementById('filter-dosen');
            dosenSelect.innerHTML = '<option value="">Semua Dosen</option>';
            uniqueDosen.forEach(dosen => {
                const option = document.createElement('option');
                option.value = dosen.id;
                option.textContent = dosen.name;
                dosenSelect.appendChild(option);
            });

            // Extract unique perusahaan
            const uniquePerusahaan = [];
            const seenPerusahaan = new Set();

            evaluations.forEach(item => {
                if (item.perusahaan_id && !seenPerusahaan.has(item.perusahaan_id)) {
                    seenPerusahaan.add(item.perusahaan_id);
                    uniquePerusahaan.push({
                        id: item.perusahaan_id,
                        name: item.nama_perusahaan || 'Unknown'
                    });
                }
            });

            // Sort perusahaan by name
            uniquePerusahaan.sort((a, b) => a.name.localeCompare(b.name));

            // Populate perusahaan dropdown
            const perusahaanSelect = document.getElementById('filter-perusahaan');
            perusahaanSelect.innerHTML = '<option value="">Semua Perusahaan</option>';
            uniquePerusahaan.forEach(perusahaan => {
                const option = document.createElement('option');
                option.value = perusahaan.id;
                option.textContent = perusahaan.name;
                perusahaanSelect.appendChild(option);
            });
        }

        // Apply filters
        function applyFilters() {
            const dosenFilter = document.getElementById('filter-dosen').value;
            const perusahaanFilter = document.getElementById('filter-perusahaan').value;

            // Filter data
            filteredEvaluations = allEvaluations.filter(item => {
                const dosenMatch = !dosenFilter || item.id_dosen == dosenFilter;
                const perusahaanMatch = !perusahaanFilter || item.perusahaan_id == perusahaanFilter;
                return dosenMatch && perusahaanMatch;
            });

            // Update UI
            if (filteredEvaluations.length === 0) {
                document.getElementById('empty-message').textContent =
                    'Tidak ada data yang sesuai dengan filter yang dipilih';
                document.getElementById('empty-container').classList.remove('d-none');
                document.getElementById('evaluasi-container').classList.add('d-none');
            } else {
                document.getElementById('empty-container').classList.add('d-none');
                renderEvaluations(filteredEvaluations);
                document.getElementById('evaluasi-container').classList.remove('d-none');
            }
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('filter-dosen').value = '';
            document.getElementById('filter-perusahaan').value = '';

            // Reset data and UI
            filteredEvaluations = [...allEvaluations];

            if (filteredEvaluations.length === 0) {
                document.getElementById('empty-message').textContent = 'Belum ada evaluasi yang tercatat dalam sistem';
                document.getElementById('empty-container').classList.remove('d-none');
                document.getElementById('evaluasi-container').classList.add('d-none');
            } else {
                document.getElementById('empty-container').classList.add('d-none');
                renderEvaluations(filteredEvaluations);
                document.getElementById('evaluasi-container').classList.remove('d-none');
            }
        }

        // Render evaluations with pagination
        function renderEvaluations(evaluations) {
            // Calculate pagination
            const totalPages = Math.ceil(evaluations.length / itemsPerPage);

            // Adjust current page if needed
            if (currentPage > totalPages) {
                currentPage = 1;
            }

            // Calculate start and end index
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, evaluations.length);

            // Get current page items
            const currentItems = evaluations.slice(startIndex, endIndex);
            console.log(`Rendering ${currentItems.length} items from page ${currentPage}`);

            // Render cards
            const cardsContainer = document.getElementById('evaluasi-cards');
            cardsContainer.innerHTML = '';

            if (currentItems.length === 0) {
                console.warn('No items to display for current page');
                return;
            }

            currentItems.forEach((eval, index) => {
                if (!eval) {
                    console.warn('Evaluation item is undefined or null, skipping');
                    return;
                }

                // Determine card color based on score
                let scoreColorClass = 'bg-gradient-primary';
                const score = parseFloat(eval.nilai || 0);

                if (score < 60) {
                    scoreColorClass = 'bg-gradient-danger';
                } else if (score < 75) {
                    scoreColorClass = 'bg-gradient-warning';
                } else if (score >= 90) {
                    scoreColorClass = 'bg-gradient-success';
                }

                // Create card element
                const col = document.createElement('div');
                col.className = 'col-lg-4 col-md-6 mb-4';
                col.style.opacity = '0';
                col.style.animation = `fadeIn 0.5s ease-out forwards ${index * 0.1}s`;

                // Safe access to properties with fallbacks
                const namaDosen = eval.nama_dosen || 'Tidak diketahui';
                const createdAt = eval.created_at ? timeAgo(eval.created_at) : 'Waktu tidak diketahui';
                const nilai = eval.nilai || '0';
                const namaMahasiswa = eval.nama_mahasiswa || '-';
                const nim = eval.nim || '-';
                const namaPerusahaan = eval.nama_perusahaan || '-';
                const evaluasi = eval.eval || 'Tidak ada evaluasi yang diberikan';

                col.innerHTML = `
                <div class="card card-evaluation h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar ${scoreColorClass} me-3 shadow">
                                    ${namaDosen.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <h6 class="mb-0 text-sm">${namaDosen}</h6>
                                    <p class="text-xs text-secondary mb-0">${createdAt}</p>
                                </div>
                            </div>
                            <div class="score-badge">
                                <i class="fas fa-star me-1 text-warning"></i>
                                ${nilai}
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <p class="text-xs text-uppercase text-muted mb-1">Mahasiswa</p>
                                    <h6 class="mb-0 text-sm">${namaMahasiswa}</h6>
                                    <p class="text-xs text-secondary mb-0">${nim}</p>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-xs text-uppercase text-muted mb-1">Perusahaan</p>
                                    <h6 class="mb-0 text-sm">${namaPerusahaan}</h6>
                                </div>
                            </div>
                        </div>

                        <h6 class="text-xs text-uppercase text-muted mb-2">Evaluasi</h6>
                        <p class="mb-0 text-sm">${evaluasi}</p>
                    </div>
                </div>
            `;

                cardsContainer.appendChild(col);
            });

            // Render pagination
            renderPagination(totalPages);
        }

        // Render pagination
        function renderPagination(totalPages) {
            const paginationContainer = document.getElementById('pagination-container');
            paginationContainer.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            // Previous button
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
            const prevLink = document.createElement('a');
            prevLink.className = 'page-link';
            prevLink.href = '#';
            prevLink.setAttribute('aria-label', 'Previous');
            prevLink.innerHTML = '<i class="fas fa-chevron-left"></i>';

            if (currentPage !== 1) {
                prevLink.onclick = function(e) {
                    e.preventDefault();
                    currentPage--;
                    renderEvaluations(filteredEvaluations);
                };
            }

            prevLi.appendChild(prevLink);
            paginationContainer.appendChild(prevLi);

            // Page numbers
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, currentPage + 2);

            // Adjust for edge cases
            if (endPage - startPage < 4 && totalPages > 5) {
                if (startPage === 1) {
                    endPage = Math.min(5, totalPages);
                } else if (endPage === totalPages) {
                    startPage = Math.max(1, totalPages - 4);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                const pageLink = document.createElement('a');
                pageLink.className = 'page-link';
                pageLink.href = '#';
                pageLink.textContent = i;

                if (i !== currentPage) {
                    pageLink.onclick = function(e) {
                        e.preventDefault();
                        currentPage = i;
                        renderEvaluations(filteredEvaluations);
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    };
                }

                pageLi.appendChild(pageLink);
                paginationContainer.appendChild(pageLi);
            }

            // Next button
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
            const nextLink = document.createElement('a');
            nextLink.className = 'page-link';
            nextLink.href = '#';
            nextLink.setAttribute('aria-label', 'Next');
            nextLink.innerHTML = '<i class="fas fa-chevron-right"></i>';

            if (currentPage !== totalPages) {
                nextLink.onclick = function(e) {
                    e.preventDefault();
                    currentPage++;
                    renderEvaluations(filteredEvaluations);
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                };
            }

            nextLi.appendChild(nextLink);
            paginationContainer.appendChild(nextLi);
        }
    </script>
@endpush
