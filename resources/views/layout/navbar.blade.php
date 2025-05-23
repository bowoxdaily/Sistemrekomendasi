<!-- partials/navbar.blade.php -->
@php
    use App\Models\Setting;
    
    // Get custom logos with fallbacks to defaults
    $mainLogo = Setting::get('logo_path', asset('admin/images/logo.svg'));
    
    // Use the same logo for mini version instead of a separate setting
    $miniLogo = Setting::get('logo_path', asset('admin/images/logo-mini.svg'));
    $logoAlt = Setting::get('logo_alt_text', 'logo');
@endphp

<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{ route('dashboard') }}">
            <img src="{{ $mainLogo }}" class="mr-2" alt="{{ $logoAlt }}" style="height: auto; max-height: 40px; width: auto; object-fit: contain;" />
        </a>
        <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
            <img src="{{ $miniLogo }}" alt="{{ $logoAlt }}" style="height: auto; max-height: 30px; width: auto; object-fit: contain;" />
        </a>
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="icon-menu"></span>
        </button>
        <ul class="navbar-nav mr-lg-2">
            <li class="nav-item nav-search d-none d-lg-block">
                <div class="input-group">
                    <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                        <span class="input-group-text" id="search">
                            <i class="icon-search"></i>
                        </span>
                    </div>
                    <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now"
                        aria-label="search" aria-describedby="search">
                </div>
            </li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                    data-toggle="dropdown">
                    <i class="icon-bell mx-0"></i>
                    <span class="count"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                    aria-labelledby="notificationDropdown">
                    <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-success">
                                <i class="ti-info-alt mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">Application Error</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                                Just now
                            </p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-warning">
                                <i class="ti-settings mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">Settings</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                                Private message
                            </p>
                        </div>
                    </a>
                    <a class="dropdown-item preview-item">
                        <div class="preview-thumbnail">
                            <div class="preview-icon bg-info">
                                <i class="ti-user mx-0"></i>
                            </div>
                        </div>
                        <div class="preview-item-content">
                            <h6 class="preview-subject font-weight-normal">New user registration</h6>
                            <p class="font-weight-light small-text mb-0 text-muted">
                                2 days ago
                            </p>
                        </div>
                    </a>
                </div>
            </li>
            <li class="nav-item nav-profile dropdown">
                <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                    <img src="{{ Auth::user()->foto ? asset('storage/user_photos/' . Auth::user()->foto) : asset('admin/images/faces/face1.jpg') }}"
                        alt="profile" />

                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                    <a class="dropdown-item"
                        href="
                        @if (Auth::user()->role === 'siswa') {{ route('siswa.profile') }}
                        @elseif(Auth::user()->role === 'guru')
                            {{ route('siswa.profile') }}
                        @elseif(Auth::user()->role === 'operator')
                            {{ route('operator.profile') }} @endif
                    ">
                        <i class="ti-user text-primary"></i>
                        Profil Saya
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <a class="dropdown-item" href="#" id="logout-btn">
                        <i class="ti-power-off text-primary"></i>
                        Logout
                    </a>

                </div>
            </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
            data-toggle="offcanvas">
            <span class="icon-menu"></span>
        </button>
    </div>

</nav>

@push('scripts')
    <script>
        $(document).ready(function() {
            // Handle logout button click
            $('#logout-btn').on('click', function(e) {
                e.preventDefault();

                // Check if SweetAlert is available
                if (typeof Swal !== 'undefined') {
                    // Use SweetAlert2 for confirmation
                    Swal.fire({
                        title: 'Konfirmasi Logout',
                        text: "Apakah Anda yakin ingin keluar dari sistem?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Logout',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state notification
                            Swal.fire({
                                title: 'Logging out...',
                                text: 'Anda akan dialihkan dalam beberapa saat',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit the logout form
                            setTimeout(function() {
                                document.getElementById('logout-form').submit();
                            }, 1000); // Small delay for better UX
                        }
                    });
                } else if (typeof toastr !== 'undefined') {
                    // Fallback to toastr if SweetAlert is not available
                    if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                        toastr.info('Logging out...', 'Notifikasi');
                        setTimeout(function() {
                            document.getElementById('logout-form').submit();
                        }, 1000);
                    }
                } else {
                    // Fallback to basic confirmation if neither library is available
                    if (confirm('Apakah Anda yakin ingin keluar dari sistem?')) {
                        document.getElementById('logout-form').submit();
                    }
                }
            });
        });
    </script>
@endpush

@push('styles')
<style>
    /* Enhanced navbar logo styling */
    .navbar .navbar-brand-wrapper {
        transition: width 0.25s ease, background 0.25s ease;
        padding: 0 1.75rem;
        overflow: hidden;
    }
    
    .navbar .navbar-brand {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    
    .navbar .navbar-brand img {
        transition: all 0.2s linear;
    }
    
    .navbar .navbar-brand.brand-logo img {
        max-width: 100%;
        height: auto;
        max-height: 40px;
        width: auto;
        object-fit: contain;
    }
    
    .navbar .navbar-brand.brand-logo-mini img {
        max-height: 30px;
    }
    
    /* Fixes for responsive views */
    @media (max-width: 991px) {
        .navbar .navbar-brand-wrapper {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .navbar .navbar-brand.brand-logo img {
            max-height: 35px;
        }
    }
</style>
@endpush
