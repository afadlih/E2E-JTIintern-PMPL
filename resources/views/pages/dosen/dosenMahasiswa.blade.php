@extends('layouts.app', ['class' => 'bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Mahasiswa Bimbingan'])

    <div class="container-fluid py-0">


        <!-- Search and Filter Section -->
        <div class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text border-end-0 bg-white">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari Mahasiswa"
                            style="font-family: 'Open Sans', sans-serif;">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="periodeFilter" class="form-select">
                        <option value="">Semua Periode</option>
                        <!-- Will be populated by JavaScript -->
                    </select>
                </div>
            </div>
        </div>

        <!-- Cards Container -->
        <div class="row g-3" id="mahasiswa-cards">
            <!-- Cards will be populated by JavaScript -->
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <p class="text-sm mb-0 pagination-info" style="font-family: 'Open Sans', sans-serif;">
                <span class="skeleton"
                    style="height: 14px; width: 120px; border-radius: 4px; display: inline-block;"></span>
            </p>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                    <!-- Will be populated by JavaScript -->
                </ul>
            </nav>
        </div>
    </div>

    {{-- ✅ Include modals --}}
    @include('pages.dosen.modals.logAktivitas')
    @include('pages.dosen.modals.evaluasi')
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/dosen/mahasiswa.css') }}">
@endpush

@push('js')
    <script>
        // Initialize the dosen_id from the server-side value
        @if (Auth::user()->role === 'dosen' && Auth::user()->dosen)
            const dosen_id = {{ Auth::user()->dosen->id_dosen ?? 'null' }};
        @else
            const dosen_id = null;
        @endif

        let filterState = {
            search: '',
            status: '',
            perusahaan: '',
            periode: ''
        };

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (dosen_id) {
                showSkeletonLoading();
                showFiltersSkeleton();

                loadMahasiswaData(filterState);

                loadPeriodeOptions().then(() => {
                    hideFiltersSkeleton();
                });
            } else {
                showErrorState('Tidak dapat menentukan ID dosen Anda. Silakan muat ulang halaman.');
            }

            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(function(e) {
                    filterState.search = this.value.trim();
                    filterState.page = 1; // Reset to first page when searching
                    loadMahasiswaData(filterState);
                }, 500));
            }

            const periodeFilter = document.getElementById('periodeFilter');
            if (periodeFilter) {
                periodeFilter.addEventListener('change', function(e) {
                    filterState.periode = this.value;
                    filterState.page = 1; // Reset to first page when filtering
                    loadMahasiswaData(filterState);
                });
            }
        });

        function showErrorState(message, isSystemError = false) {
            const cardsContainer = document.getElementById('mahasiswa-cards');

            if (!cardsContainer) {
                console.error('Cards container not found');
                return;
            }

            cardsContainer.innerHTML = `
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-3">
                            <i class="fas fa-exclamation-triangle text-danger" style="font-size: 40px;"></i>
                        </div>
                        <h5 class="text-danger">${message}</h5>
                        ${isSystemError ? `
                                                                                                                                                                                                                                                                <p class="text-muted mt-2 mb-3">Coba muat ulang halaman atau hubungi administrator</p>
                                                                                                                                                                                                                                                            ` : ''}
                        <button class="btn btn-sm btn-primary mt-2" onclick="window.location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Coba Lagi
                        </button>
                    </div>
                </div>
            </div>
        `;
        }

        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true
        });

        function showSkeletonLoading() {
            const cardsContainer = document.getElementById('mahasiswa-cards');

            if (!cardsContainer) {
                console.error('Cards container not found');
                return;
            }

            // Generate 6 skeleton cards
            let skeletonHTML = '';
            for (let i = 0; i < 6; i++) {
                skeletonHTML += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="skeleton-card">
                            <!-- Status Badge Skeleton -->
                            <div style="height: 29px; display: flex; align-items: center;">
                                <div class="skeleton skeleton-status"></div>
                            </div>
                            
                            <!-- Main Content Skeleton -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-grow: 1;">
                                <!-- Left Side - Student Info -->
                                <div style="width: 50%; display: flex; flex-direction: column; gap: 8px;">
                                    <div class="skeleton skeleton-name"></div>
                                    <div class="skeleton skeleton-nim"></div>
                                    <div class="skeleton skeleton-class"></div>
                                </div>
                                
                                <!-- Right Side - Company Info -->
                                <div style="width: 45%; display: flex; flex-direction: column; gap: 5px; align-items: flex-end;">
                                    <div class="skeleton skeleton-company"></div>
                                    <div class="skeleton skeleton-position"></div>
                                </div>
                            </div>
                            
                            <!-- Divider Skeleton -->
                            <div class="skeleton skeleton-divider"></div>
                            
                            <!-- Buttons Skeleton -->
                            <div style="display: flex; justify-content: space-between; gap: 10px; padding-top: 5px;">
                                <div class="skeleton skeleton-button"></div>
                                <div class="skeleton skeleton-button"></div>
                            </div>
                        </div>
                    </div>
                `;
            }

            cardsContainer.innerHTML = skeletonHTML;

            // Update pagination info
            const paginationInfo = document.querySelector('.pagination-info');
            if (paginationInfo) {
                paginationInfo.innerHTML =
                    '<span class="skeleton" style="height: 14px; width: 120px; border-radius: 4px; display: inline-block;"></span>';
            }
        }

        function showFiltersSkeleton() {
            const searchInput = document.getElementById('searchInput');
            const periodeFilter = document.getElementById('periodeFilter');

            if (searchInput) {
                searchInput.disabled = true;
                searchInput.placeholder = 'Memuat...';
            }

            if (periodeFilter) {
                periodeFilter.disabled = true;
                periodeFilter.innerHTML = '<option>Memuat periode...</option>';
            }
        }

        function hideFiltersSkeleton() {
            const searchInput = document.getElementById('searchInput');
            const periodeFilter = document.getElementById('periodeFilter');

            if (searchInput) {
                searchInput.disabled = false;
                searchInput.placeholder = 'Cari Mahasiswa';
            }

            if (periodeFilter) {
                periodeFilter.disabled = false;
            }
        }

        let isLoading = false;

        function loadMahasiswaData(filters = {}) {
            if (isLoading) return;
            isLoading = true;

            console.log('📊 Loading data with filters:', filters);

            const cardsContainer = document.getElementById('mahasiswa-cards');

            if (!cardsContainer) {
                console.error('Cards container not found');
                isLoading = false;
                return;
            }

            showSkeletonLoading();

            const currentPage = filters.page || 1;

            api.get(`/dosen/${dosen_id}/mahasiswa-bimbingan`, {
                    params: {
                        search: filters.search || '',
                        status: filters.status || '',
                        perusahaan: filters.perusahaan || '',
                        periode: filters.periode || '',
                        page: currentPage
                    }
                })
                .then(function(response) {
                    setTimeout(() => {
                        if (response.data.success) {
                            const mahasiswa = response.data.data;
                            const meta = response.data.meta;

                            console.log('Pagination meta:', meta);

                            if (mahasiswa.length === 0) {
                                showEmptyState(filters);
                                updatePaginationInfo({
                                    total: 0,
                                    from: 0,
                                    to: 0,
                                    last_page: 1
                                }, 1);
                            } else {
                                const skeletonCards = cardsContainer.querySelectorAll('.skeleton-card');
                                skeletonCards.forEach(card => {
                                    card.classList.add('skeleton-fade-out');
                                });

                                setTimeout(() => {
                                    cardsContainer.innerHTML = '';
                                    loadRealContent(mahasiswa, cardsContainer);
                                    updatePaginationInfo(meta, currentPage);
                                }, 300);
                            }
                        } else {
                            showErrorState('Gagal memuat data mahasiswa', true);
                        }
                        isLoading = false;
                    }, 800);
                })
                .catch(function(error) {
                    console.error('Error:', error);
                    setTimeout(() => {
                        showErrorState('Terjadi kesalahan saat memuat data', true);
                        isLoading = false;
                    }, 800);
                });
        }

        // ✅ ADD: Separate function for loading real content
        async function loadRealContent(mahasiswa, cardsContainer) {
            for (const [index, item] of mahasiswa.entries()) {
                const card = document.createElement('div');
                card.className = 'col-md-6 col-lg-4 mb-3';
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                const jobTitle = item.judul_lowongan || 'Belum ada posisi';

                // Check evaluation status untuk mahasiswa yang selesai magang
                let needsEvaluation = false;
                let hasEvaluation = false;
                let needsDosenEvaluation = false;
                let evaluationGrade = null;

                if (item.status && item.status.toLowerCase() === 'selesai' && item.id_magang) {
                    const evalStatus = await checkEvaluationStatus(item.id_magang);

                    // Check if there's an evaluation but dosen needs to input their part
                    needsDosenEvaluation = evalStatus.needs_dosen_evaluation || false;
                    hasEvaluation = evalStatus.has_evaluation && !needsDosenEvaluation;
                    needsEvaluation = !hasEvaluation && !needsDosenEvaluation;

                    // Get the grade if evaluation is complete
                    if (hasEvaluation && evalStatus.grade) {
                        evaluationGrade = evalStatus.grade;
                    }
                }

                let cardStyles = '';
                let statusMessage = '';

                if (item.status && item.status.toLowerCase() === 'selesai') {
                    const evalStatus = await checkEvaluationStatus(item.id_magang);

                    needsDosenEvaluation = evalStatus.needs_dosen_evaluation || false;
                    hasEvaluation = evalStatus.has_evaluation && !needsDosenEvaluation;

                    const waitingForCompany = !evalStatus.has_evaluation;

                    if (needsDosenEvaluation) {
                        cardStyles = 'border: 2px solid #ffc107; box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);';
                    } else if (hasEvaluation) {
                        cardStyles = 'border: 2px solid #28a745; box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);';
                    } else if (waitingForCompany) {
                        cardStyles = 'border: 2px solid #17a2b8; box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.1);';
                        statusMessage = `
                            <div class="evaluation-alert" 
                                 style="position: absolute; top: -8px; right: -8px; 
                                        background: linear-gradient(135deg, #17a2b8, #138496); 
                                        color: white; padding: 4px 8px; 
                                        border-radius: 12px; font-size: 10px; 
                                        font-weight: 700; text-transform: uppercase; 
                                        box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3); 
                                        z-index: 10; animation: pulse 2s infinite;">
                            <i class="fas fa-clock" style="margin-right: 4px;"></i>
                            Menunggu Input Nilai Perusahaan
                        </div>
                        `;
                    }

                    // Get grade if evaluation is complete
                    if (hasEvaluation && evalStatus.grade) {
                        evaluationGrade = evalStatus.grade;
                    }
                }

                card.innerHTML = `
                    <div class="mahasiswa-card" 
                         data-mahasiswa-id="${item.id_mahasiswa}"
                         data-magang-id="${item.id_magang}"
                         style="width: 100%; height: 100%; padding: 20px; background: white; border-radius: 5px; outline: 1px #E8EDF5 solid; display: flex; flex-direction: column; gap: 15px; position: relative; ${cardStyles}">
                        
                        ${statusMessage}
                        
                        ${needsDosenEvaluation ? `
                                    <div class="evaluation-alert" style="position: absolute; top: -8px; right: -8px; background: linear-gradient(135deg, #ffc107, #ff9800); color: white; padding: 4px 8px; border-radius: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3); z-index: 10; animation: pulse 2s infinite;">
                                        <i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i>
                                        Perlu Evaluasi Dosen
                                    </div>
                                ` : ''}

                        ${hasEvaluation && evaluationGrade ? `
                                                                                                                                <div class="evaluation-grade" style="position: absolute; top: -8px; right: -8px; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 6px 12px; border-radius: 15px; font-size: 12px; font-weight: 700; box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3); z-index: 10;">
                                                                                                                                    <i class="fas fa-trophy" style="margin-right: 4px;"></i>
                                                                                                                                    Grade: ${evaluationGrade}
                                                                                                                                </div>
                                                                                                                            ` : ''}

                        <!-- Status Badge -->
                        <div data-property-1="Status" style="height: 29px; display: inline-flex; align-items: center; gap: 10px;">
                            <div data-property-1="Default" style="padding: 5px 40px; ${getStatusStyles(item.status)}">
                                <div style="${getStatusTextStyles(item.status)}">${item.status || 'Belum Magang'}</div>
                            </div>
                        </div>
                        
                        <!-- Mahasiswa Info -->
                        <div style="align-self: stretch; display: flex; justify-content: space-between; align-items: center; flex-grow: 1;">
                            <div style="width: 50%; flex-direction: column; display: flex; gap: 8px;">
                                <div style="color: #2D2D2D; font-size: 14px; font-family: 'Open Sans', sans-serif; font-weight: 600;">${item.name}</div>
                                <div style="color: #7D7D7D; font-size: 12px; font-family: 'Open Sans', sans-serif; font-weight: 500;">${item.nim}</div>
                                <div style="color: #7D7D7D; font-size: 11px; font-family: 'Open Sans', sans-serif; font-weight: 400;">${item.nama_kelas}</div>
                            </div>
                            <div style="width: 50%; flex-direction: column; display: flex; gap: 5px; align-items: flex-end;">
                                <div style="color: #2D2D2D; font-size: 13px; font-family: 'Open Sans', sans-serif; font-weight: 600; text-align: right;">
                                    ${item.nama_perusahaan || 'Belum ada perusahaan'}
                                </div>
                                <div style="color: #7D7D7D; font-size: 11px; font-family: 'Open Sans', sans-serif; font-weight: 500; text-align: right;">
                                    ${jobTitle}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Divider -->
                        <div style="height: 1px; background: linear-gradient(90deg, transparent, #E8EDF5, transparent); margin: 5px 0;"></div>
                        
                        <!-- Action Buttons Container -->
                        <div class="action-buttons" style="display: flex; justify-content: space-between; align-items: center; gap: 10px; padding-top: 5px;">
                            <button onclick="logAktivitas('${item.id_mahasiswa}', '${item.id_magang}')" class="btn btn-primary">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Log Aktivitas</span>
                            </button>
                            
                            ${item.status && item.status.toLowerCase() === 'selesai' ? `
                                                                                                            <!-- Button container for completed internships -->
                                                                                                            ${needsDosenEvaluation ? `
        <!-- Show active evaluation button when dosen needs to evaluate -->
        <button onclick="evaluasiMahasiswa('${item.id_mahasiswa}', '${item.id_magang || ''}')" 
                class="btn btn-warning" 
                style="flex: 1; padding: 8px 12px; 
                       background: linear-gradient(135deg, #ffc107, #ff9800); 
                       border-radius: 6px; border: 1.5px solid #ffc107; 
                       display: flex; align-items: center; justify-content: center; 
                       gap: 8px; font-size: 12px; font-weight: 600; 
                       transition: all 0.3s ease;">
            <i class="fas fa-star" style="color: white; font-size: 12px;"></i>
            <span style="color: white;">Evaluasi Sekarang</span>
        </button>
    ` : hasEvaluation ? `
        <!-- Show completed evaluation with grade -->
        <div class="evaluation-completed" 
             style="flex: 1; padding: 8px 12px; 
                    background: linear-gradient(135deg, #d4edda, #c3e6cb); 
                    border-radius: 6px; border: 1.5px solid #28a745; 
                    display: flex; align-items: center; justify-content: center; 
                    gap: 8px; font-size: 12px; font-weight: 600;">
            <i class="fas fa-check-circle" style="color: #28a745; font-size: 12px;"></i>
            <span style="color: #28a745;">Grade: ${evaluationGrade || 'Selesai'}</span>
        </div>
    ` : `
        <!-- Show disabled button when evaluation is not needed -->
        <button disabled class="btn" 
                style="flex: 1; padding: 8px 12px; 
                       background: #f8f9fa; border-radius: 6px; 
                       border: 1.5px solid #e9ecef; 
                       display: flex; align-items: center; justify-content: center; 
                       gap: 8px; font-size: 12px; font-weight: 600; 
                       opacity: 0.6; cursor: not-allowed;">
            <i class="fas fa-check-circle" style="color: #adb5bd; font-size: 12px;"></i>
            <span style="color: #adb5bd;">Sudah Dievaluasi</span>
        </button>
    `}
                                                                                                        ` : `
                                                                                                            <!-- Show disabled button for non-completed internships -->
                                                                                                            <button disabled class="btn" 
                                                                                                                    style="flex: 1; padding: 8px 12px; 
                                                                                                                           background: #f8f9fa; border-radius: 6px; 
                                                                                                                           border: 1.5px solid #e9ecef; 
                                                                                                                           display: flex; align-items: center; justify-content: center; 
                                                                                                                           gap: 8px; font-size: 12px; font-weight: 600; 
                                                                                                                           opacity: 0.6; cursor: not-allowed;">
                                                                                                                <i class="fas fa-star" style="color: #adb5bd; font-size: 12px;"></i>
                                                                                                                <span style="color: #adb5bd;">Evaluasi</span>
                                                                                                            </button>
                                                                                                        `}
                        </div>
                    </div>
                `;

                cardsContainer.appendChild(card);

                // ✅ ADD: Staggered animation for each card
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100); // 100ms delay between each card
            }
        }

        // ✅ UPDATE: Modified DOMContentLoaded event
        document.addEventListener('DOMContentLoaded', function() {
            if (dosen_id) {
                showSkeletonLoading();
                showFiltersSkeleton();

                loadMahasiswaData(filterState);

                loadPeriodeOptions().then(() => {
                    hideFiltersSkeleton();
                });
            } else {
                showErrorState('Tidak dapat menentukan ID dosen Anda. Silakan muat ulang halaman.');
            }

            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.addEventListener('input', debounce(function(e) {
                    filterState.search = this.value.trim();
                    filterState.page = 1; // Reset to first page when searching
                    loadMahasiswaData(filterState);
                }, 500));
            }

            const periodeFilter = document.getElementById('periodeFilter');
            if (periodeFilter) {
                periodeFilter.addEventListener('change', function(e) {
                    filterState.periode = this.value;
                    filterState.page = 1; // Reset to first page when filtering
                    loadMahasiswaData(filterState);
                });
            }
        });

        // ✅ UPDATE: Modified loadPeriodeOptions to return Promise
        function loadPeriodeOptions() {
            const periodeFilter = document.getElementById('periodeFilter');

            if (!periodeFilter) {
                console.error('Periode filter not found');
                return Promise.resolve();
            }

            periodeFilter.innerHTML = `<option value="">Memuat periode...</option>`;

            // ✅ GANTI: Gunakan route yang sudah ada
            return api.get('/periode-list') // ❌ HAPUS: `/dosen/${dosen_id}/periode-options`
                .then(function(response) {
                    if (response.data.success) {
                        const options = response.data.data;

                        // ✅ SESUAIKAN: Format response untuk dropdown
                        const formattedOptions = options.map(option => ({
                            value: option.periode_id,
                            label: option.waktu
                        }));

                        periodeFilter.innerHTML = `
                            <option value="">Semua Periode</option>
                            ${formattedOptions.map(option => `<option value="${option.value}">${option.label}</option>`).join('')}
                        `;
                    } else {
                        periodeFilter.innerHTML = `<option value="">Gagal memuat periode</option>`;
                    }
                })
                .catch(function(error) {
                    console.error('Error loading periode options:', error);
                    periodeFilter.innerHTML = `<option value="">Terjadi kesalahan</option>`;
                });
        }

        function showEmptyState(filters) {
            const cardsContainer = document.getElementById('mahasiswa-cards');

            if (!cardsContainer) {
                console.error('Cards container not found');
                return;
            }

            cardsContainer.innerHTML = `
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-folder-open" style="font-size: 50px; color: #adb5bd;"></i>
                        <h5 class="mt-3" style="color: #2D2D2D;">Tidak ada data mahasiswa yang ditemukan</h5>
                        <p class="text-muted mb-4">Coba ubah pencarian atau filter Anda</p>
                        <button class="btn btn-primary" onclick="window.location.reload()" style="background: #5988FF; border: none;">
                            <i class="fas fa-sync-alt me-2"></i>Segarkan Halaman
                        </button>
                    </div>
                </div>
            </div>
        `;
        }

        async function checkEvaluationStatus(id_magang) {
            try {
                if (!id_magang) {
                    console.warn('ID Magang tidak tersedia');
                    return {
                        has_evaluation: false,
                        needs_dosen_evaluation: false,
                        grade: null
                    };
                }

                const response = await api.get(`/dosen/magang/${id_magang}/evaluation-status`);

                if (response.data.success) {
                    console.log(
                        `Magang ${id_magang}: has_evaluation = ${response.data.has_evaluation}, needs_dosen_evaluation = ${response.data.needs_dosen_evaluation}, grade = ${response.data.grade || 'N/A'}`
                    );
                    return {
                        has_evaluation: response.data.has_evaluation,
                        needs_dosen_evaluation: response.data.needs_dosen_evaluation,
                        grade: response.data.grade || null
                    };
                } else {
                    console.warn('Failed to check evaluation status:', response.data.message);
                    return {
                        has_evaluation: false,
                        needs_dosen_evaluation: false,
                        grade: null
                    };
                }
            } catch (error) {
                console.error('Error checking evaluation status:', error);
                return {
                    has_evaluation: false,
                    needs_dosen_evaluation: false,
                    grade: null
                };
            }
        }

        function evaluasiMahasiswa(id_mahasiswa, id_magang) {
            console.log('Opening evaluasi for mahasiswa:', id_mahasiswa, 'magang:', id_magang);

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('evaluasiModal'));
            modal.show();

            // Show loading
            document.getElementById('evaluasiLoading').style.display = 'block';
            document.getElementById('evaluasiContent').style.display = 'none';

            // Load evaluation data
            api.get(`/mahasiswa/${id_mahasiswa}/evaluasi?magang_id=${id_magang}`)
                .then(function(response) {
                    if (response.data.success) {
                        // Hide loading and show content
                        document.getElementById('evaluasiLoading').style.display = 'none';
                        document.getElementById('evaluasiContent').style.display = 'block';

                        // Populate form with data
                        populateEvaluasiForm(response.data.data);
                    } else {
                        throw new Error(response.data.message || 'Gagal memuat data evaluasi');
                    }
                })
                .catch(function(error) {
                    console.error('Error loading evaluation:', error);
                    Swal.fire('Error', 'Gagal memuat data evaluasi: ' + error.message, 'error');
                    modal.hide();
                });
        }

        function generateEvaluasiHTML(data) {
            return `
        <form id="evaluasiForm">
            <div class="mb-3">
                <label class="form-label">Nilai Dosen</label>
                <input type="number" class="form-control" id="nilai_dosen" name="nilai_dosen" 
                       min="0" max="100" value="${data.nilai_dosen || ''}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan Evaluasi</label>
                <textarea class="form-control" id="catatan_dosen" name="catatan_dosen" 
                          rows="4" required>${data.catatan_dosen || ''}</textarea>
            </div>
            <input type="hidden" id="id_mahasiswa" name="id_mahasiswa" value="${data.id_mahasiswa}">
            <input type="hidden" id="magang_id" name="magang_id" value="${data.id_magang}">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitEvaluasi()">
                    ${data.is_existing ? 'Update Evaluasi' : 'Simpan Evaluasi'}
                </button>
            </div>
        </form>
    `;
        }

        // Add missing helper functions
        function getStatusStyles(status) {
            if (!status) return 'background: #f8f9fa; border-radius: 3px;';

            switch (status.toLowerCase()) {
                case 'aktif':
                    return 'background: rgba(87, 196, 90, 0.1); border-radius: 3px;';
                case 'selesai':
                    return 'background: rgba(253, 105, 0, 0.1); border-radius: 3px;';
                default:
                    return 'background: #f8f9fa; border-radius: 3px;';
            }
        }

        function getStatusTextStyles(status) {
            if (!status) return 'color: #aaaaaa; font-size: 13px; font-family: "Open Sans", sans-serif; font-weight: 700;';

            switch (status.toLowerCase()) {
                case 'aktif':
                    return 'color: #57C45A; font-size: 13px; font-family: "Open Sans", sans-serif; font-weight: 700;';
                case 'selesai':
                    return 'color: #fd6900; font-size: 13px; font-family: "Open Sans", sans-serif; font-weight: 700;';
                default:
                    return 'color: #aaaaaa; font-size: 13px; font-family: "Open Sans", sans-serif; font-weight: 700;';
            }
        }

        function updatePaginationInfo(meta, currentPage) {
            const paginationInfo = document.querySelector('.pagination-info');
            const paginationContainer = document.querySelector('.pagination');

            if (paginationInfo) {
                if (meta.total > 0) {
                    paginationInfo.innerHTML = `Menampilkan ${meta.from} sampai ${meta.to} dari ${meta.total} mahasiswa`;
                } else {
                    paginationInfo.innerHTML = 'Tidak ada data mahasiswa';
                }
            }

            if (paginationContainer && meta.last_page > 1) {
                let paginationHTML = '';

                // Previous button
                if (currentPage > 1) {
                    paginationHTML += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(${currentPage - 1})" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    `;
                } else {
                    paginationHTML += `
                        <li class="page-item disabled">
                            <span class="page-link">&laquo;</span>
                        </li>
                    `;
                }

                // Page numbers
                for (let i = 1; i <= meta.last_page; i++) {
                    if (i === currentPage) {
                        paginationHTML += `
                            <li class="page-item active">
                                <span class="page-link">${i}</span>
                            </li>
                        `;
                    } else {
                        paginationHTML += `
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="changePage(${i})">${i}</a>
                            </li>
                        `;
                    }
                }

                // Next button
                if (currentPage < meta.last_page) {
                    paginationHTML += `
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="changePage(${currentPage + 1})" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    `;
                } else {
                    paginationHTML += `
                        <li class="page-item disabled">
                            <span class="page-link">&raquo;</span>
                        </li>
                    `;
                }

                paginationContainer.innerHTML = paginationHTML;
            } else if (paginationContainer) {
                paginationContainer.innerHTML = '';
            }
        }

        function changePage(page) {
            filterState.page = page;
            loadMahasiswaData(filterState);
        }

        function debugModal() {
            console.log('🐛 Testing modal functionality...');

            // Test bootstrap modal
            try {
                const testModal = new bootstrap.Modal(document.getElementById('logAktivitasModal'));
                console.log('✅ Bootstrap modal initialized successfully');
                testModal.show();
                setTimeout(() => testModal.hide(), 2000);
            } catch (error) {
                console.error('❌ Bootstrap modal error:', error);
            }
        }


        document.addEventListener('DOMContentLoaded', function() {
            console.log('🚀 DOM Content Loaded');
            console.log('📋 Checking if logAktivitas function exists:', typeof logAktivitas);
            console.log('🎯 Checking if modal exists:', document.getElementById('logAktivitasModal') ? 'Yes' :
                'No');

            // ... existing DOMContentLoaded code ...
        });

        // Add this function in your JavaScript code right after your filterState declaration
        function logAktivitas(id_mahasiswa, id_magang) {
            // Gunakan parameter id_magang yang diteruskan langsung dari tombol
            console.log('Fetching logs for mahasiswa:', id_mahasiswa, 'magang:', id_magang);

            if (!id_magang) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'ID Magang tidak ditemukan'
                });
                return;
            }

            // Show loading first
            Swal.fire({
                title: 'Memuat Log Aktivitas',
                text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make the API call with both IDs directly from parameters
            api.get(`/mahasiswa/${id_mahasiswa}/logbook`, {
                    params: {
                        id_magang: id_magang
                    }
                })
                .then(response => {
                    if (response.data.success) {
                        // Get the modal element
                        const modal = new bootstrap.Modal(document.getElementById('logAktivitasModal'));
                        const modalBody = document.querySelector('#logAktivitasModal .modal-body');
                        const modalTitle = document.querySelector('#logAktivitasModal .modal-title');

                        // Update modal title
                        modalTitle.textContent = `Log Aktivitas`;

                        // Generate content based on the data
                        if (response.data.data.length === 0) {
                            modalBody.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="text-muted mb-0">Belum ada log aktivitas yang tercatat</p>
                            </div>
                        `;
                        } else {
                            // Sort and group logs by month
                            const groupedLogs = response.data.data;
                            let logsHTML = '';

                            groupedLogs.forEach(group => {
                                logsHTML += `
                                <div class="month-group mb-4">
                                    <h6 class="text-primary mb-3">${group.month}</h6>
                                    <div class="timeline">
                                        ${group.entries.map(log => `
                                                <div class="timeline-item mb-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <span class="badge bg-info">${log.tanggal_hari}</span>
                                                            <span class="text-muted ms-2">${log.tanggal_formatted}</span>
                                                        </div>
                                                        <small class="text-muted">${log.time_ago}</small>
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <p class="mb-0">${log.deskripsi}</p>
                                                            ${log.has_foto ? `
                                                            <div class="mt-2">
                                                                <img src="${log.foto}" class="img-fluid rounded" 
                                                                     style="max-height: 200px; cursor: pointer"
                                                                     onclick="showFullImage('${log.foto}')"
                                                                     alt="Dokumentasi aktivitas">
                                                            </div>
                                                        ` : ''}
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                    </div>
                                </div>
                            `;
                            });

                            modalBody.innerHTML = logsHTML;
                        }

                        // Hide loading and show modal
                        Swal.close();
                        modal.show();
                    } else {
                        throw new Error(response.data.message || 'Gagal memuat log aktivitas');
                    }
                })
                .catch(error => {
                    console.error('Error fetching log aktivitas:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Terjadi kesalahan saat memuat log aktivitas'
                    });
                });
        }

        // Helper function to show full image
        function showFullImage(imageUrl) {
            Swal.fire({
                imageUrl: imageUrl,
                imageAlt: 'Dokumentasi aktivitas',
                width: '80%',
                showConfirmButton: false,
                showCloseButton: true
            });
        }
    </script>
@endpush

<!-- Add this to your page, after your main content -->
<div class="modal fade" id="logAktivitasModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Aktivitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be inserted here by JavaScript -->
            </div>
        </div>
    </div>
</div>
