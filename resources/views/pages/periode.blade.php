<!-- filepath: d:\laragon\www\JTIintern\resources\views\pages\periode.blade.php -->
@extends('layouts.app',  ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Manajemen Periode'])
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Daftar Periode</h6>
                        <p class="text-sm text-secondary mb-0">
                            Manajemen periode magang untuk lowongan
                        </p>
                    </div>
                    <button class="btn btn-sm btn-success" onclick="tambahPeriode()">
                        <i class="fas fa-plus me-2"></i>Tambah Periode
                    </button>
                </div>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Waktu
                                </th>
                                <th
                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end pe-4">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="periode-table-body">
                            <!-- Data akan ditampilkan di sini -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div id="empty-state" class="text-center py-5 d-none">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-calendar-alt text-muted" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                    <h6 class="text-muted">Belum ada data periode</h6>
                    <p class="text-xs text-secondary mb-3">
                        Silahkan tambahkan periode baru untuk magang
                    </p>
                    <button class="btn btn-sm btn-success" onclick="tambahPeriode()">
                        <i class="fas fa-plus me-2"></i>Tambah Periode
                    </button>
                </div>

                <!-- Error State -->
                <div id="error-state" class="text-center py-5 d-none">
                    <div class="error-state-icon mb-3">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-danger">Gagal memuat data</h6>
                    <p class="text-xs text-secondary mb-3" id="error-message">
                        Terjadi kesalahan saat memuat data periode
                    </p>
                    <button class="btn btn-sm btn-primary" onclick="loadPeriodeData()">
                        <i class="fas fa-sync-alt me-2"></i>Coba Lagi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Periode -->
    <div class="modal fade" id="periodeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="periodeModalLabel">Tambah Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="periodeForm" onsubmit="handleSubmitPeriode(event)">
                    <div class="mb-3">
                        <label for="waktu" class="form-label">Tahun Akademik</label>
                        <input type="text" class="form-control" id="waktu" name="waktu" placeholder="Contoh: 2024/2025"
                            required>
                    </div>

                    <div class="mb-3">
                        <label for="tgl_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" required>
                    </div>

                    <div class="mb-3">
                        <label for="tgl_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div class="modal fade" id="detailPeriodeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Periode</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="130">Waktu</th>
                            <td><span id="detail-waktu"></span></td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td><span id="detail-tgl-mulai"></span></td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td><span id="detail-tgl-selesai"></span></td>
                        </tr>
                        <tr>
                            <th>Dibuat pada</th>
                            <td><span id="detail-created"></span></td>
                        </tr>
                        <tr>
                            <th>Diperbarui pada</th>
                            <td><span id="detail-updated"></span></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .action-buttons {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
        }

        .empty-state-icon,
        .error-state-icon {
            opacity: 0.5;
        }

        .table td,
        .table th {
            white-space: nowrap;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Initialize axios instance
        const api = axios.create({
            baseURL: '/api',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            withCredentials: true
        });

        // Format tanggal
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric'
            });
        }

        // Fungsi untuk memuat data periode
        function loadPeriodeData() {
            const tableBody = document.getElementById('periode-table-body');
            tableBody.innerHTML = `
                                                <tr>
                                                    <td colspan="2" class="text-center py-3">
                                                        <i class="fas fa-circle-notch fa-spin me-2"></i>Memuat data...
                                                    </td>
                                                </tr>
                                            `;

            document.getElementById('empty-state').classList.add('d-none');
            document.getElementById('error-state').classList.add('d-none');

            api.get('/periode')
                .then(response => {
                    if (response.data.success) {
                        tableBody.innerHTML = '';

                        if (response.data.data.length === 0) {
                            document.getElementById('empty-state').classList.remove('d-none');
                            return;
                        }

                        response.data.data.forEach((periode, index) => {
                            const row = document.createElement('tr');

                            // Format dates for display
                            const startDateStr = periode.tgl_mulai ? new Date(periode.tgl_mulai).toLocaleDateString('id-ID') : '-';
                            const endDateStr = periode.tgl_selesai ? new Date(periode.tgl_selesai).toLocaleDateString('id-ID') : '-';

                            row.innerHTML = `
        <td>
            <div class="d-flex px-3 py-1">
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm">${periode.waktu}</h6>
                    ${periode.is_active ? '<span class="badge bg-success ms-2">Periode Aktif</span>' : ''}
                    <small class="text-muted mt-1">${startDateStr} - ${endDateStr}</small>
                </div>
            </div>
        </td>
                                        <td class="align-middle text-end pe-4">
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-info me-1" onclick="detailPeriode(${periode.periode_id})" title="Lihat Detail">
                                                    <i class="fas fa-eye me-1"></i>Detail
                                                </button>
                                                ${!periode.is_active ?
                                    `<button class="btn btn-sm btn-success me-1" onclick="setActivePeriode(${periode.periode_id})" title="Jadikan Periode Aktif">
                                                        <i class="fas fa-check-circle me-1"></i>Set Aktif
                                                    </button>` : ''
                                }
                                                <button class="btn btn-sm btn-primary me-1" onclick="editPeriode(${periode.periode_id})" title="Edit Periode">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deletePeriode(${periode.periode_id})" title="Hapus Periode">
                                                    <i class="fas fa-trash me-1"></i>Hapus
                                                </button>
                                            </div>
                                        </td>
                                    `;
                            tableBody.appendChild(row);
                        });
                    } else {
                        throw new Error(response.data.message || 'Failed to load data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('error-message').textContent = error.message || 'Terjadi kesalahan saat memuat data periode';
                    document.getElementById('error-state').classList.remove('d-none');
                });
        }

        // Load data periode saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            loadPeriodeData();
        });

        // Fungsi untuk membuka modal tambah periode
        function tambahPeriode() {
            document.getElementById('periodeForm').reset();
            document.getElementById('periodeModalLabel').innerText = 'Tambah Periode';
            document.getElementById('periodeForm').removeAttribute('data-id');
            const modal = new bootstrap.Modal(document.getElementById('periodeModal'));
            modal.show();
        }

        // Function to set a period as active
        function setActivePeriode(id) {
            Swal.fire({
                title: 'Jadikan Periode Aktif?',
                text: "Periode ini akan dijadikan sebagai periode yang aktif dan ditampilkan di dashboard",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Aktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang mengaktifkan periode',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    api.post(`/periode/set-active/${id}`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Periode berhasil diaktifkan',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    loadPeriodeData(); // Reload data to show updated active status
                                });
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal mengaktifkan periode.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            let errorMsg = 'Terjadi kesalahan saat mengaktifkan periode.';

                            if (error.response && error.response.data && error.response.data.message) {
                                errorMsg = error.response.data.message;
                            }

                            Swal.fire('Gagal', errorMsg, 'error');
                        });
                }
            });
        }

        // Fungsi untuk membuka modal detail periode
        // Fungsi untuk membuka modal detail periode
        function detailPeriode(id) {
            api.get(`/periode/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const periode = response.data.data;
                        document.getElementById('detail-waktu').textContent = periode.waktu || '-';

                        // Format and display the date fields
                        const startDate = periode.tgl_mulai ? new Date(periode.tgl_mulai) : null;
                        const endDate = periode.tgl_selesai ? new Date(periode.tgl_selesai) : null;

                        const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
                        document.getElementById('detail-tgl-mulai').textContent = startDate ?
                            startDate.toLocaleDateString('id-ID', dateOptions) : '-';
                        document.getElementById('detail-tgl-selesai').textContent = endDate ?
                            endDate.toLocaleDateString('id-ID', dateOptions) : '-';

                        document.getElementById('detail-created').textContent = formatDate(periode.created_at);
                        document.getElementById('detail-updated').textContent = formatDate(periode.updated_at);

                        const modal = new bootstrap.Modal(document.getElementById('detailPeriodeModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data periode tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil detail periode.', 'error');
                });
        }

        // Fungsi untuk membuka modal edit periode
        function editPeriode(id) {
            api.get(`/periode/${id}`)
                .then(response => {
                    if (response.data.success) {
                        const periode = response.data.data;
                        document.getElementById('periodeModalLabel').innerText = 'Edit Periode';
                        document.getElementById('waktu').value = periode.waktu;

                        // Set date fields if they exist
                        if (periode.tgl_mulai) {
                            // Convert to YYYY-MM-DD format for input field
                            const startDate = new Date(periode.tgl_mulai);
                            document.getElementById('tgl_mulai').value = startDate.toISOString().split('T')[0];
                        } else {
                            document.getElementById('tgl_mulai').value = '';
                        }

                        if (periode.tgl_selesai) {
                            // Convert to YYYY-MM-DD format for input field
                            const endDate = new Date(periode.tgl_selesai);
                            document.getElementById('tgl_selesai').value = endDate.toISOString().split('T')[0];
                        } else {
                            document.getElementById('tgl_selesai').value = '';
                        }

                        document.getElementById('periodeForm').setAttribute('data-id', periode.periode_id);
                        const modal = new bootstrap.Modal(document.getElementById('periodeModal'));
                        modal.show();
                    } else {
                        Swal.fire('Gagal', 'Data periode tidak ditemukan.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Gagal', 'Terjadi kesalahan saat mengambil data periode.', 'error');
                });
        }

        // Fungsi untuk menangani submit form periode
        function handleSubmitPeriode(event) {
            event.preventDefault();

            const id = document.getElementById('periodeForm').getAttribute('data-id');
            const waktu = document.getElementById('waktu').value;
            const tgl_mulai = document.getElementById('tgl_mulai').value;
            const tgl_selesai = document.getElementById('tgl_selesai').value;

            if (!waktu) {
                Swal.fire('Peringatan', 'Waktu periode harus diisi', 'warning');
                return;
            }

            if (!tgl_mulai) {
                Swal.fire('Peringatan', 'Tanggal mulai harus diisi', 'warning');
                return;
            }

            if (!tgl_selesai) {
                Swal.fire('Peringatan', 'Tanggal selesai harus diisi', 'warning');
                return;
            }

            // Check if end date is after start date
            if (new Date(tgl_selesai) <= new Date(tgl_mulai)) {
                Swal.fire('Peringatan', 'Tanggal selesai harus setelah tanggal mulai', 'warning');
                return;
            }

            const formData = {
                waktu,
                tgl_mulai,
                tgl_selesai
            };

            const method = id ? 'put' : 'post';
            const url = id ? `/periode/${id}` : '/periode';

            const submitBtn = document.querySelector('#periodeForm button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';

            api[method](url, formData)
                .then(response => {
                    if (response.data.success) {
                        Swal.fire('Berhasil', response.data.message || 'Periode berhasil disimpan!', 'success');
                        bootstrap.Modal.getInstance(document.getElementById('periodeModal')).hide();
                        loadPeriodeData();
                    } else {
                        Swal.fire('Gagal', response.data.message || 'Gagal menyimpan periode.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMsg = 'Terjadi kesalahan saat menyimpan periode.';

                    if (error.response && error.response.data && error.response.data.message) {
                        errorMsg = error.response.data.message;
                    }

                    Swal.fire('Gagal', errorMsg, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
        }

        // Fungsi untuk menghapus periode
        function deletePeriode(id) {
            Swal.fire({
                title: 'Yakin ingin menghapus periode ini?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    api.delete(`/periode/${id}`)
                        .then(response => {
                            if (response.data.success) {
                                Swal.fire('Berhasil', 'Periode berhasil dihapus!', 'success');
                                loadPeriodeData();
                            } else {
                                Swal.fire('Gagal', response.data.message || 'Gagal menghapus periode.', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            let errorMsg = 'Terjadi kesalahan saat menghapus periode.';

                            if (error.response && error.response.data && error.response.data.message) {
                                errorMsg = error.response.data.message;
                            }

                            Swal.fire('Gagal', errorMsg, 'error');
                        });
                }
            });
        }
    </script>
@endpush