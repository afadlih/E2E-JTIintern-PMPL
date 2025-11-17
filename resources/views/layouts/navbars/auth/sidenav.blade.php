<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="bi bi-x-lg p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="{{ route('home') }}" target="_blank">
            <img src="/img/Jti_polinema.png" alt="Logo" />
            <span class="fw-bold"
                style="color:#2D2D2D; font-size:24px; font-family:'Poppins', sans-serif;">JTIintern</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">

    <!-- Main navigation container -->
    <div class="sidenav-content">
        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}"
                        href="{{ route('home') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-grid-fill text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                {{-- Mahasiswa Section --}}
                <li class="nav-item">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Mahasiswa</h6>
                    <a class="nav-link {{ Route::currentRouteName() == 'Data_Mahasiswa' ? 'active' : '' }}"
                        href="{{ route('Data_Mahasiswa') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-mortarboard text-sm opacity-10 "></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Mahasiswa</span>
                    </a>

                    <a class="nav-link {{ str_contains(request()->url(), 'permintaan') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'permintaan']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-envelope text-sm opacity-10 "></i>
                        </div>
                        <span class="nav-link-text ms-1">Permintaan Magang</span>
                    </a>
                </li>

                {{-- Perusahaan Section --}}
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Perusahaan</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'data_perusahaan') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'data_perusahaan']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-building text-sm opacity-10 "></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Perusahaan</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'lowongan') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'lowongan']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-briefcase text-sm opacity-10 "></i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen Lowongan</span>
                    </a>
                </li>

                {{-- Account Section --}}
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Dosen</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'dosen') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'dosen']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-person text-sm opacity-10 text-purple"></i>
                        </div>
                        <span class="nav-link-text ms-1">Data Dosen</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'evaluasi') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'evaluasi']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-file-bar-graph text-sm opacity-10 text-purple"></i>
                        </div>
                        <span class="nav-link-text ms-1">Evaluasi Magang</span>
                    </a>
                </li>

                {{-- Umum Section --}}
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Umum</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'kelas') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'kelas']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-building-gear text-sm opacity-10" style="color: #5988FF;"></i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen Kelas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'periode') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'periode']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-range text-sm opacity-10" style="color: #5988FF;"></i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen Periode</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'skill') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'skill']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-stars text-sm opacity-10" style="color: #5988FF;"></i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen Skill</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ str_contains(request()->url(), 'minat') == true ? 'active' : '' }}"
                        href="{{ route('page', ['page' => 'minat']) }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-bookmark-heart text-sm opacity-10" style="color: #5988FF;"></i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen Minat</span>
                    </a>
                </li>

                {{-- Superadmin Only Section --}}
                @if (Auth::check() && Auth::user()->role === 'superadmin')
                    <li class="nav-item mt-3">
                        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Superadmin</h6>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Request::is('admin') ? 'active' : '' }}" href="{{ route('admin') }}">
                            <div
                                class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                                <i class="bi bi-gear-fill text-sm opacity-10" style="color: #FF5959;"></i>
                            </div>
                            <span class="nav-link-text ms-1">Manajemen Admin</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Logout button -->
        <div class="sidenav-footer">
            <form method="POST" action="{{ route('logout') }}" class="mx-3">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm w-100">
                    Log out
                </button>
            </form>
        </div>
    </div>
</aside>

@push('css')
    <link href="{{ asset('assets/css/sidenav.css') }}" rel="stylesheet" />
@endpush
