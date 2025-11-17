{{-- filepath: c:\laragon\www\JTIintern\resources\views\pages\dosen\modals\evaluasi.blade.php --}}

<div class="modal fade" id="evaluasiModal" tabindex="-1" aria-labelledby="evaluasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="evaluasiModalLabel">
                    <i class="fas fa-star me-2"></i>Evaluasi Mahasiswa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="container-fluid">
                    <!-- Loading State -->
                    <div id="evaluasiLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat data evaluasi...</p>
                    </div>

                    <!-- Content -->
                    <div id="evaluasiContent" style="display: none;">
                        <div class="row">
                            <!-- Left Column: Company Evaluation Details -->
                            <div class="col-lg-6 border-end">
                                <div class="p-4">
                                    <h6 class="fw-bold text-primary mb-3">
                                        <i class="fas fa-building me-2"></i>Evaluasi Perusahaan
                                    </h6>

                                    <!-- Company Evaluation Info -->
                                    <div id="companyEvaluationSection">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Dosen Evaluation Form -->
                            <div class="col-lg-6">
                                <div class="p-4">
                                    <h6 class="fw-bold text-success mb-3">
                                        <i class="fas fa-user-graduate me-2"></i>Evaluasi Dosen
                                    </h6>

                                    <!-- Dosen Evaluation Form -->
                                    <form id="evaluasiForm">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Nilai Dosen <span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" id="nilai_dosen"
                                                name="nilai_dosen" min="0" max="100" step="0.1"
                                                placeholder="Masukkan nilai (0-100)" required>
                                            <div class="form-text">Nilai berkisar antara 0-100</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Catatan Evaluasi <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" id="catatan_dosen" name="catatan_dosen" rows="5"
                                                placeholder="Berikan catatan evaluasi untuk mahasiswa..." required></textarea>
                                        </div>

                                        <!-- Grade Preview -->
                                        <div class="card bg-light mb-3">
                                            <div class="card-body py-2">
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">Nilai Dosen</small>
                                                        <strong id="preview-nilai-dosen" class="text-success">-</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">Nilai Akhir</small>
                                                        <strong id="preview-nilai-akhir" class="text-primary">-</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted d-block">Grade</small>
                                                        <strong id="preview-grade" class="text-warning">-</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" id="id_mahasiswa" name="id_mahasiswa">
                                        <input type="hidden" id="magang_id" name="magang_id">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" class="btn btn-primary" onclick="submitEvaluasi()" id="submitBtn">
                    <i class="fas fa-save me-1"></i>Simpan Evaluasi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function generateCompanyEvaluationHTML(data) {
        const hasCompanyEvaluation = data.nilai_perusahaan && data.file_penilaian_perusahaan;

        if (!hasCompanyEvaluation) {
            return `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Belum ada evaluasi dari perusahaan</strong>
                    <p class="mb-0 mt-2">Evaluasi dari perusahaan belum tersedia. Dosen tetap dapat memberikan penilaian.</p>
                </div>
            `;
        }

        return `
            <div class="card border-success">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Data Evaluasi Perusahaan
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Nilai Perusahaan -->
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>Nilai Perusahaan:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge bg-success fs-6 nilai-display">${data.nilai_perusahaan}</span>
                            <span class="text-muted ms-2">/100</span>
                        </div>
                    </div>
                    
                    <!-- File Penilaian -->
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <strong>File Penilaian:</strong>
                        </div>
                        <div class="col-sm-8">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-file-pdf text-danger"></i>
                                <a href="${getFileUrl(data.file_penilaian_perusahaan)}" 
                                   target="_blank" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-download me-1"></i>Unduh PDF
                                </a>
                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                        onclick="previewPDF('${getFileUrl(data.file_penilaian_perusahaan)}')">
                                    <i class="fas fa-eye me-1"></i>Preview
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Grade Preview dari Perusahaan -->
                    <div class="row">
                        <div class="col-sm-4">
                            <strong>Grade Perusahaan:</strong>
                        </div>
                        <div class="col-sm-8">
                            <span class="badge ${getGradeBadgeClass(getGradeFromScore(data.nilai_perusahaan))} fs-6">
                                ${getGradeFromScore(data.nilai_perusahaan)} - ${getGradeDescription(getGradeFromScore(data.nilai_perusahaan))}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- PDF Preview Modal -->
            <div class="modal fade" id="pdfPreviewModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Preview File Penilaian</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <iframe id="pdfFrame" style="width: 100%; height: 500px; border: none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getFileUrl(filePath) {
        if (!filePath) return '#';

        // If already a full URL
        if (filePath.startsWith('http')) {
            return filePath;
        }

        // If starts with storage/, use as is
        if (filePath.startsWith('storage/')) {
            return `${window.location.origin}/${filePath}`;
        }

        // Otherwise prepend storage/
        return `${window.location.origin}/storage/${filePath}`;
    }

    function previewPDF(url) {
        const pdfFrame = document.getElementById('pdfFrame');
        pdfFrame.src = url;

        const pdfModal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
        pdfModal.show();
    }

    function getGradeBadgeClass(grade) {
        const classes = {
            'A': 'bg-success',
            'B+': 'bg-info',
            'B': 'bg-primary',
            'C+': 'bg-warning',
            'C': 'bg-orange',
            'D': 'bg-danger',
            'E': 'bg-secondary'
        };
        return classes[grade] || 'bg-secondary';
    }

    function submitEvaluasi() {
        const form = document.getElementById('evaluasiForm');
        const formData = new FormData(form);
        const id_mahasiswa = formData.get('id_mahasiswa');
        const magang_id = formData.get('magang_id');

        if (!id_mahasiswa || !magang_id) {
            Swal.fire('Error', 'Data tidak lengkap', 'error');
            return;
        }

        // Validate form
        const nilaiDosen = formData.get('nilai_dosen');
        const catatanDosen = formData.get('catatan_dosen');

        if (!nilaiDosen || nilaiDosen < 0 || nilaiDosen > 100) {
            Swal.fire('Error', 'Nilai dosen harus antara 0-100', 'error');
            return;
        }

        if (!catatanDosen || catatanDosen.trim() === '') {
            Swal.fire('Error', 'Catatan evaluasi harus diisi', 'error');
            return;
        }

        // Disable submit button
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

        Swal.fire({
            title: 'Menyimpan Evaluasi',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        api.post(`/mahasiswa/${id_mahasiswa}/evaluasi`, {
                nilai_dosen: parseFloat(nilaiDosen),
                catatan_dosen: catatanDosen.trim(),
                magang_id: magang_id
            })
            .then(function(response) {
                Swal.close();
                if (response.data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        bootstrap.Modal.getInstance(document.getElementById('evaluasiModal')).hide();
                        loadMahasiswaData(filterState);
                    });
                } else {
                    Swal.fire('Gagal', response.data.message || 'Gagal menyimpan evaluasi', 'error');
                }
            })
            .catch(function(error) {
                Swal.close();
                console.error('Error:', error);
                const errorMessage = error.response?.data?.message || 'Terjadi kesalahan saat menyimpan evaluasi';
                Swal.fire('Error', errorMessage, 'error');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan Evaluasi';
            });
    }

    // Update form values and previews when modal loads
    function populateEvaluasiForm(data) {
        // Set form values
        document.getElementById('nilai_dosen').value = data.nilai_akhir || '';
        document.getElementById('catatan_dosen').value = data.catatan_evaluasi || '';
        document.getElementById('id_mahasiswa').value = data.id_mahasiswa;
        document.getElementById('magang_id').value = data.id_magang;

        // Update submit button text
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = data.is_existing ?
            '<i class="fas fa-edit me-1"></i>Update Evaluasi' :
            '<i class="fas fa-save me-1"></i>Simpan Evaluasi';

        // Populate company evaluation section
        document.getElementById('companyEvaluationSection').innerHTML = generateCompanyEvaluationHTML(data);

        // Update previews if nilai_dosen already filled
        const nilaiDosen = parseFloat(data.nilai_akhir || 0);
        const nilaiPerusahaan = parseFloat(data.nilai_perusahaan || 0);

        if (nilaiDosen > 0) {
            updatePreview(nilaiDosen, nilaiPerusahaan);
        }
    }

    // Add event listener for real-time updates
    document.addEventListener('input', function(e) {
        if (e.target.id === 'nilai_dosen') {
            const nilaiDosen = parseFloat(e.target.value) || 0;
            const nilaiPerusahaanElement = document.querySelector('.nilai-display');
            const nilaiPerusahaan = nilaiPerusahaanElement ? parseFloat(nilaiPerusahaanElement.textContent) ||
                0 : 0;

            updatePreview(nilaiDosen, nilaiPerusahaan);
        }
    });

    function updatePreview(nilaiDosen, nilaiPerusahaan) {
        const previewNilaiDosen = document.getElementById('preview-nilai-dosen');
        const previewNilaiAkhir = document.getElementById('preview-nilai-akhir');
        const previewGrade = document.getElementById('preview-grade');

        if (previewNilaiDosen) {
            previewNilaiDosen.textContent = nilaiDosen || '-';
        }

        if (nilaiDosen > 0 && nilaiPerusahaan > 0) {
            const nilaiAkhir = ((nilaiDosen + nilaiPerusahaan) / 2).toFixed(1);
            const grade = getGradeFromScore(nilaiAkhir);

            if (previewNilaiAkhir) previewNilaiAkhir.textContent = nilaiAkhir;
            if (previewGrade) {
                previewGrade.textContent = grade;
                previewGrade.style.color = getGradeColor(grade);
            }
        } else {
            if (previewNilaiAkhir) previewNilaiAkhir.textContent = '-';
            if (previewGrade) {
                previewGrade.textContent = '-';
                previewGrade.style.color = '#6c757d';
            }
        }
    }

    function getGradeFromScore(score) {
        score = parseFloat(score);
        if (score >= 81) return 'A';
        if (score >= 74) return 'B+';
        if (score >= 66) return 'B';
        if (score >= 61) return 'C+';
        if (score >= 51) return 'C';
        if (score >= 40) return 'D';
        return 'E';
    }

    function getGradeDescription(grade) {
        const descriptions = {
            'A': 'Sangat Baik',
            'B+': 'Lebih dari Baik',
            'B': 'Baik',
            'C+': 'Lebih dari Cukup',
            'C': 'Cukup',
            'D': 'Kurang',
            'E': 'Sangat Kurang'
        };
        return descriptions[grade] || '';
    }

    function getGradeColor(grade) {
        const colors = {
            'A': '#28a745',
            'B+': '#17a2b8',
            'B': '#007bff',
            'C+': '#ffc107',
            'C': '#fd7e14',
            'D': '#dc3545',
            'E': '#6c757d'
        };
        return colors[grade] || '#6c757d';
    }
</script>

<style>
    .bg-orange {
        background-color: #fd7e14 !important;
    }

    #pdfFrame {
        border: 1px solid #dee2e6;
    }

    .card-header h6 {
        font-size: 0.9rem;
    }

    .evaluation-preview {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 8px;
        padding: 15px;
    }
</style>
