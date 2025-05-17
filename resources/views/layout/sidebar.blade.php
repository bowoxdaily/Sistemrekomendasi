<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        @if (auth()->user()->role === 'operator')
            <!-- Menu khusus operator -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#admin-menu" aria-expanded="false">
                    <i class="mdi mdi-account-cog menu-icon"></i> {{-- Ganti ikon sesuai kebutuhan --}}
                    <span class="menu-title">Manajemen</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="admin-menu">
                    <ul class="nav flex-column sub-menu">
                        {{-- Tambahkan item submenu di sini tanpa icon jika tidak diperlukan --}}
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('view.siswa') }}">Manajemen User</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('view.jurusan') }}">Manajemen Jurusan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('operator.questionnaires.index') }}">Pengaturan
                                kuisioner</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('operator.jobs.index') }}">Pengaturan
                                Rekomendasi Kerja</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif


        @auth
            <!-- Menu untuk semua user yang login -->
            @if (auth()->user()->role === 'siswa')
                <!-- Menu khusus siswa -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon-book-open menu-icon"></i>
                        <span class="menu-title">Kelas Saya</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="icon-note menu-icon"></i>
                        <span class="menu-title">Tugas</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->role === 'guru')
                <!-- Menu khusus guru -->
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#kelas-guru" aria-expanded="false">
                        <i class="icon-book-open menu-icon"></i>
                        <span class="menu-title">Manajemen Kelas</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="kelas-guru">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"><a class="nav-link" href="#">Daftar Kelas</a></li>
                            <li class="nav-item"><a class="nav-link" href="#">Buat Tugas</a></li>
                        </ul>
                    </div>
                </li>
            @endif







            @if (auth()->user()->role === 'operator')
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#icons" aria-expanded="false" aria-controls="icons">
                        <i class="icon-contract menu-icon"></i>
                        <span class="menu-title">Icons</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="icons">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"> <a class="nav-link" href="pages/icons/mdi.html">Mdi icons</a></li>
                        </ul>
                    </div>
                </li>
            @endif




            <!-- Menu UI Elements hanya untuk guru dan operator -->
            @if (in_array(auth()->user()->role, ['guru', 'operator']))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false">
                        <i class="icon-layout menu-icon"></i>
                        <span class="menu-title">UI Elements</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="ui-basic">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('pages/ui-features/buttons') }}">Buttons</a></li>
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('pages/ui-features/dropdowns') }}">Dropdowns</a></li>
                        </ul>
                    </div>
                </li>
            @endif
        @else
            <!-- Menu untuk guest (belum login) -->
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="icon-login menu-icon"></i>
                    <span class="menu-title">Login</span>
                </a>
            </li>
        @endauth
    </ul>
</nav>
