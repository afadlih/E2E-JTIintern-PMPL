@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])

    <div class="container-fluid py-4">
        {{-- Statistics Cards --}}
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-dark mb-2"
                                    style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 600;">Total
                                    Mahasiswa
                                </h5>
                                <h3 class="mb-0" id="total-mahasiswa"
                                    style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 600;">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                </h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 38px; height: 38px; background: #5988FF;">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="text-dark mb-2"
                                    style="font-family: 'Poppins', sans-serif; font-size: 20px; font-weight: 600;">
                                    Menunggu Evaluasi</h5>
                                <h3 class="mb-0" id="perlu-evaluasi"
                                    style="font-family: 'Poppins', sans-serif; font-size: 24px; font-weight: 600;">
                                    <div class="spinner-border spinner-border-sm" role="status"></div>
                                </h3>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                style="width: 38px; height: 38px; background: #5988FF;">
                                <i class="fas fa-clipboard-check text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- List Mahasiswa Section --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 600;">
                        List Mahasiswa Bimbingan</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 600;">
                            Periode:</span>
                        <span class="period-text px-4 py-1 rounded-pill"
                            style="border: 1px solid #96B3FF; font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 600;">
                            2025/2026</span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header pb-0">
                        <div class="mt-2">
                            <div class="input-group">
                                <span class="input-group-text border-end-0 bg-white"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Cari Mahasiswa"
                                    style="font-family: 'Open Sans', sans-serif;">
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Mahasiswa</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NIM
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Perusahaan</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="student-overview-table">
                                    <!-- Will be populated by JavaScript -->
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <p class="text-sm mb-0">Menampilkan 1-4 dari 10 Mahasiswa</p>
                                <nav>
                                    <ul class="pagination mb-0">
                                        <li class="page-item"><a class="page-link" href="#"><i
                                                    class="fas fa-angle-left"></i></a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                                        <li class="page-item"><a class="page-link" href="#"><i
                                                    class="fas fa-angle-right"></i></a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dosen_id = '{{ Auth::user()->dosen->id_dosen }}';

            // Load dashboard stats
            axios.get(`/api/dosen/dashboard/stats/${dosen_id}`)
                .then(response => {
                    if (response.data.success) {
                        const stats = response.data.data;
                        // Update period text
                        document.querySelector('.period-text').innerHTML = stats.periode ||
                            'Tidak ada periode aktif';

                        // Update total mahasiswa
                        document.getElementById('total-mahasiswa').innerHTML = stats.total_mahasiswa;

                        // Update evaluasi counter - remove spinner and show count
                        const evaluasiElement = document.getElementById('perlu-evaluasi');
                        evaluasiElement.innerHTML = stats.perlu_evaluasi;

                        // Optional: Add color indicator if there are pending evaluations
                        if (stats.perlu_evaluasi > 0) {
                            evaluasiElement.classList.add('text-danger');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Show error state instead of spinner
                    document.getElementById('perlu-evaluasi').innerHTML = '-';
                    document.querySelector('.period-text').innerHTML = 'Error loading data';
                });

            // Load mahasiswa table
            axios.get(`/api/dosen/dashboard/mahasiswa/${dosen_id}`)
                .then(response => {
                    if (response.data.success) {
                        const tableBody = document.getElementById('student-overview-table');
                        const mahasiswa = response.data.data;

                        tableBody.innerHTML = mahasiswa.map(mhs => `
                        <tr>
                            <td>
                                <h6 class="text-sm ms-3">${mhs.name}</h6>
                            </td>
                            <td>
                                <p class="text-xs mb-0">${mhs.nim}</p>
                            </td>
                            <td>
                                <p class="text-sm mb-0">${mhs.nama_perusahaan}</p>
                                <p class="text-xs text-secondary mb-0">${mhs.judul_lowongan}</p>
                            </td>
                            <td>
                                <span class="badge ${mhs.status.toLowerCase() === 'aktif' ? 'bg-success' : 'bg-secondary'}">
                                    ${mhs.status}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-sm px-3" 
                                        onclick="logAktivitas('${mhs.id_mahasiswa}')">
                                        Log Aktivitas
                                    </button>
                                    ${mhs.status.toLowerCase() === 'selesai' ? `
                                                                    <button class="btn btn-outline-secondary btn-sm px-3" 
                                                                        onclick="evaluasiMahasiswa('${mhs.id_mahasiswa}')">
                                                                        Evaluasi
                                                                    </button>
                                                                ` : ''}
                                </div>
                            </td>
                        </tr>
                    `).join('');
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endpush
