<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl
        {{ str_contains(Request::url(), 'virtual-reality') == true ? ' mt-3 mx-3 bg-primary' : '' }}" id="navbarBlur"
        data-scroll="false">
        <div class="container-fluid py-4 px-2">
            <nav aria-label="breadcrumb">
                <div class="row">
                    <h2 class="font-weight-bolder text-black mb-0">{{ $title }}</h2>
                </div>
            </nav>
        </div>
</nav>
<!-- End Navbar -->