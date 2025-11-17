{{-- filepath: c:\laragon\www\JTIintern\resources\views\components\evaluasi_magang.blade.php --}}

{{-- ✅ SIMPLE: Component that doesn't interfere with existing design --}}
@props(['idMagang'])

<div id="evaluasi-magang-section-{{ $idMagang }}">
    <div class="alert alert-info mb-3" style="display: none;" id="evaluasi-notice-{{ $idMagang }}">
        <h6 class="alert-heading">📝 Input Nilai Magang Diperlukan</h6>
        <p class="mb-0">Magang Anda telah selesai. Silakan input nilai dari pengawas lapangan untuk menyelesaikan proses magang.</p>
        <hr>
        <button class="btn btn-primary btn-sm" id="show-evaluasi-form-{{ $idMagang }}">
            <i class="fas fa-edit me-1"></i> Input Nilai Sekarang
        </button>
    </div>

    <div class="card border-primary" style="display: none;" id="evaluasi-form-card-{{ $idMagang }}">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">📝 Form Input Nilai Magang</h6>
        </div>
        <div class="card-body">
            <form id="evaluasi-form-{{ $idMagang }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_magang" value="{{ $idMagang }}">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nilai dari Perusahaan (0-100) *</label>
                            <input type="number" name="nilai_perusahaan" class="form-control" min="0" max="100" step="0.1" required>
                            <div class="form-text">Masukkan nilai yang diberikan oleh pengawas lapangan</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">File Nilai/Sertifikat *</label>
                            <input type="file" name="file_nilai_perusahaan" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <div class="form-text">Upload dokumen nilai (PDF/Gambar, Max: 5MB)</div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan_mahasiswa" class="form-control" rows="3" placeholder="Catatan tambahan..."></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success" id="submit-btn-{{ $idMagang }}">
                        <i class="fas fa-paper-plane me-1"></i> Submit Evaluasi
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancel-btn-{{ $idMagang }}">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ SIMPLE: Check if evaluation needed
    fetch(`/api/evaluasi-magang/check/{{ $idMagang }}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.need_evaluation) {
            document.getElementById('evaluasi-notice-{{ $idMagang }}').style.display = 'block';
        }
    })
    .catch(error => console.log('Evaluasi check error:', error));

    // ✅ SIMPLE: Show/hide form
    document.getElementById('show-evaluasi-form-{{ $idMagang }}').addEventListener('click', function() {
        document.getElementById('evaluasi-notice-{{ $idMagang }}').style.display = 'none';
        document.getElementById('evaluasi-form-card-{{ $idMagang }}').style.display = 'block';
    });

    document.getElementById('cancel-btn-{{ $idMagang }}').addEventListener('click', function() {
        document.getElementById('evaluasi-form-card-{{ $idMagang }}').style.display = 'none';
        document.getElementById('evaluasi-notice-{{ $idMagang }}').style.display = 'block';
    });

    // ✅ SIMPLE: Form submission
    document.getElementById('evaluasi-form-{{ $idMagang }}').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn-{{ $idMagang }}');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...';
        
        const formData = new FormData(this);
        
        fetch('/api/evaluasi-magang/submit', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            credentials: 'same-origin',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Evaluasi berhasil disubmit!');
                document.getElementById('evaluasi-magang-section-{{ $idMagang }}').style.display = 'none';
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Terjadi kesalahan'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Evaluasi';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat submit evaluasi');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Submit Evaluasi';
        });
    });
});
</script>
