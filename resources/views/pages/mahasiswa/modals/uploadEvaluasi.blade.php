{{-- filepath: c:\laragon\www\JTIintern\resources\views\pages\mahasiswa\modals\uploadEvaluasi.blade.php --}}

<div class="modal fade" id="uploadEvaluasiModal" tabindex="-1" aria-labelledby="uploadEvaluasiModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="uploadEvaluasiModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Penilaian Perusahaan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="uploadEvaluasiForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Petunjuk:</strong> Upload form penilaian yang telah diisi dan ditandatangani oleh
                        perusahaan tempat magang Anda.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nilai_perusahaan" class="form-label">
                                    <i class="fas fa-star text-warning me-1"></i>Nilai dari Perusahaan
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="nilai_perusahaan"
                                        name="nilai_perusahaan" min="0" max="100" step="0.01" required>
                                    <span class="input-group-text">/ 100</span>
                                </div>
                                <div class="form-text">Masukkan nilai yang diberikan oleh perusahaan (0-100)</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="grade-preview-card">
                                <h6>Preview Grade:</h6>
                                <div class="grade-preview" id="grade-preview">
                                    <span class="grade-value">-</span>
                                    <small class="grade-desc">Masukkan nilai</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="file_penilaian" class="form-label">
                            <i class="fas fa-file-pdf text-danger me-1"></i>Upload Form Penilaian (PDF)
                        </label>
                        <input type="file" class="form-control" id="file_penilaian" name="file_penilaian"
                            accept=".pdf" required>
                        <div class="form-text">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            File harus berformat PDF, maksimal 5MB. Upload scan form penilaian yang sudah diisi dan
                            ditandatangani perusahaan.
                        </div>
                    </div>

                    <div class="upload-area" id="upload-area">
                        <div class="upload-placeholder">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                            <small class="text-muted">Maksimal 5MB</small>
                        </div>
                        <div class="upload-preview" id="upload-preview" style="display: none;">
                            <div class="file-info">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span id="file-name">-</span>
                                <small id="file-size" class="text-muted">-</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <input type="hidden" id="id_magang" name="id_magang">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-upload me-2"></i>Submit Penilaian
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .grade-preview-card {
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        border: 2px dashed #dee2e6;
    }

    .grade-preview {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .grade-value {
        font-size: 2rem;
        font-weight: bold;
        color: #495057;
        margin-bottom: 5px;
    }

    .grade-desc {
        color: #6c757d;
    }

    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .upload-area:hover {
        border-color: #ffc107;
        background: #fffdf2;
    }

    .upload-area.dragover {
        border-color: #28a745;
        background: #f8fff8;
    }

    .upload-preview {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: white;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }

    .file-info {
        display: flex;
        align-items: center;
        flex: 1;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Grade preview functionality
        const nilaiInput = document.getElementById('nilai_perusahaan');
        const gradePreview = document.getElementById('grade-preview');

        nilaiInput.addEventListener('input', function() {
            const nilai = parseFloat(this.value);
            updateGradePreview(nilai);
        });

        function updateGradePreview(nilai) {
            const gradeValue = gradePreview.querySelector('.grade-value');
            const gradeDesc = gradePreview.querySelector('.grade-desc');

            if (isNaN(nilai) || nilai === '') {
                gradeValue.textContent = '-';
                gradeDesc.textContent = 'Masukkan nilai';
                gradeValue.style.color = '#495057';
                return;
            }

            let grade, description, color;

            if (nilai >= 81) {
                grade = 'A';
                description = 'Sangat Baik';
                color = '#28a745';
            } else if (nilai >= 74) {
                grade = 'B+';
                description = 'Lebih dari Baik';
                color = '#17a2b8';
            } else if (nilai >= 66) {
                grade = 'B';
                description = 'Baik';
                color = '#007bff';
            } else if (nilai >= 61) {
                grade = 'C+';
                description = 'Lebih dari Cukup';
                color = '#ffc107';
            } else if (nilai >= 51) {
                grade = 'C';
                description = 'Cukup';
                color = '#fd7e14';
            } else if (nilai >= 40) {
                grade = 'D';
                description = 'Kurang';
                color = '#dc3545';
            } else {
                grade = 'E';
                description = 'Sangat Kurang';
                color = '#6c757d';
            }

            gradeValue.textContent = grade;
            gradeDesc.textContent = description;
            gradeValue.style.color = color;
        }

        // File upload functionality
        const fileInput = document.getElementById('file_penilaian');
        const uploadArea = document.getElementById('upload-area');
        const uploadPlaceholder = uploadArea.querySelector('.upload-placeholder');
        const uploadPreview = document.getElementById('upload-preview');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showFilePreview(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                showFilePreview(this.files[0]);
            }
        });

        function showFilePreview(file) {
            if (file.type !== 'application/pdf') {
                Swal.fire('Error', 'File harus berformat PDF', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                Swal.fire('Error', 'File terlalu besar (maksimal 5MB)', 'error');
                return;
            }

            uploadPlaceholder.style.display = 'none';
            uploadPreview.style.display = 'flex';

            document.getElementById('file-name').textContent = file.name;
            document.getElementById('file-size').textContent = formatFileSize(file.size);
        }

        window.removeFile = function() {
            fileInput.value = '';
            uploadPlaceholder.style.display = 'block';
            uploadPreview.style.display = 'none';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });

    // Handle form submission
    document.getElementById('uploadEvaluasiForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        Swal.fire({
            title: 'Uploading...',
            text: 'Sedang mengupload penilaian',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/api/mahasiswa/evaluasi/upload', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                Swal.close();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('uploadEvaluasiModal')).hide();
                    Swal.fire('Berhasil', data.message, 'success');
                    checkEvaluationStatus();
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire('Error', 'Terjadi kesalahan saat mengupload', 'error');
            });
    });
</script>
