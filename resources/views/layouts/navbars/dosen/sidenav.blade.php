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
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dosen.dashboard' ? 'active' : '' }}"
                        href="{{ route('dosen.dashboard') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-grid-fill text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dosen.mahasiswa' ? 'active' : '' }}"
                        href="{{ route('dosen.mahasiswa') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-mortarboard text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1">Mahasiswa Bimbingan</span>
                    </a>
                </li>


                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteName() == 'dosen.profile' ? 'active' : '' }}"
                        href="{{ route('dosen.profile') }}">
                        <div
                            class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="bi bi-person-gear text-sm opacity-10" style="color: #E091FF;"></i>
                        </div>
                        <span class="nav-link-text ms-1">Manajemen Profile</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Logout button -->
        <div class="sidenav-footer">
            <form method="POST" action="{{ route('logout') }}" class="mx-3 mb-3">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm w-100">
                    Log out
                </button>
            </form>
        </div>
    </div>
</aside>
