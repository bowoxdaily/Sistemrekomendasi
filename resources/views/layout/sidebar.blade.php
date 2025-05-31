<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('dashboard') }}">
                <i class="mdi mdi-view-dashboard menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>

        @if (auth()->user()->role === 'superadmin')
            <!-- Menu khusus superadmin -->
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#superadmin-schools" aria-expanded="false">
                    <i class="mdi mdi-domain menu-icon"></i>
                    <span class="menu-title">Manajemen Sekolah</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="superadmin-schools">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Daftar Sekolah</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{#">Tambah Sekolah Baru</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{#">Verifikasi Sekolah</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#superadmin-users" aria-expanded="false">
                    <i class="mdi mdi-account-multiple menu-icon"></i>
                    <span class="menu-title">Manajemen Pengguna</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="superadmin-users">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('superadmin.operator') }}">Operator Sekolah</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Siswa</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Alumni</a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="#superadmin-analytics" aria-expanded="false">
                    <i class="mdi mdi-chart-bar menu-icon"></i>
                    <span class="menu-title">Analitik & Laporan</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="superadmin-analytics">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Laporan Tracer Study</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Statistik Karir</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Export Data</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endif

        @if (auth()->user()->role === 'operator')
            <!-- Menu khusus operator -->
            @php
                $isManagementActive =
                    request()->routeIs('view.siswa') ||
                    request()->routeIs('view.jurusan') ||
                    request()->routeIs('operator.questionnaires.*') ||
                    request()->routeIs('operator.jobs.*');
            @endphp

            <li class="nav-item {{ $isManagementActive ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#admin-menu"
                    aria-expanded="{{ $isManagementActive ? 'true' : 'false' }}">
                    <i class="mdi mdi-account-cog menu-icon"></i>
                    <span class="menu-title">Manajemen</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ $isManagementActive ? 'show' : '' }}" id="admin-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('view.siswa') ? 'active' : '' }}"
                                href="{{ route('view.siswa') }}">Manajemen User</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('view.jurusan') ? 'active' : '' }}"
                                href="{{ route('view.jurusan') }}">Manajemen Jurusan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('operator.questionnaires.index') ? 'active' : '' }}"
                                href="{{ route('operator.questionnaires.index') }}">Pengaturan
                                kuisioner</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('operator.jobs.index') ? 'active' : '' }}"
                                href="{{ route('operator.jobs.index') }}">Pengaturan
                                Rekomendasi Kerja</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="collapse" href="{{ route('operator.blog.index') }}"
                    aria-expanded="false" aria-controls="icons">
                    <i class="mdi mdi-post-outline menu-icon"></i>
                    <span class="menu-title">Informasi</span>

                </a>

            </li>

            @php
                $isSettingsActive = request()->routeIs('operator.settings.*');
            @endphp
            <li class="nav-item {{ $isSettingsActive ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#settings-menu"
                    aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}">
                    <i class="mdi mdi-cog menu-icon"></i>
                    <span class="menu-title">Pengaturan Sistem</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse {{ $isSettingsActive ? 'show' : '' }}" id="settings-menu">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('operator.settings.general') }}">Pengaturan Umum</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('operator.settings.logo') }}">Logo & Gambar</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('operator.settings.school') }}">Informasi Sekolah</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('operator.settings.backup') }}">Backup & Restore</a>
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
                    <a class="nav-link" href="{{ route('siswa.profile') }}">
                        <i class="mdi mdi-account menu-icon"></i>
                        <span class="menu-title">Profil Saya</span>
                    </a>
                </li>

                @if (Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('student.kuis') }}">
                            <i class="mdi mdi-clipboard-text menu-icon"></i>
                            <span class="menu-title">Kuesioner Karir</span>
                            @php
                                $hasCompleted = Auth::user()->student->has_completed_questionnaire;
                            @endphp
                            <span class="badge {{ $hasCompleted ? 'badge-success' : 'badge-warning' }} ml-2">
                                {{ $hasCompleted ? 'Selesai' : 'Belum Diisi' }}
                            </span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('student.recommendation.show') }}">
                            <i class="mdi mdi-briefcase menu-icon"></i>
                            <span class="menu-title">Rekomendasi Pekerjaan</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#info-menu" aria-expanded="false">
                        <i class="mdi mdi-information-outline menu-icon"></i>
                        <span class="menu-title">Informasi</span>
                        <i class="menu-arrow"></i>
                    </a>
                    <div class="collapse" id="info-menu">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item">
                                <a class="nav-link" href="#">Lowongan Kerja</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Info Beasiswa</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Tips Karir</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="mdi mdi-account-group menu-icon"></i>
                        <span class="menu-title">Data Alumni</span>
                    </a>
                </li>
            @endif

            @if (auth()->user()->role === 'guru')
                <!-- Menu khusus guru -->
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#kelas-guru" aria-expanded="false">
                        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
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
            @endif

            <!-- Menu UI Elements hanya untuk guru dan operator -->
            @if (in_array(auth()->user()->role, ['guru', 'operator']))
                <li class="nav-item">
                    <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false">
                        <i class="mdi mdi-palette menu-icon"></i>
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
                    <i class="mdi mdi-login menu-icon"></i>
                    <span class="menu-title">Login</span>
                </a>
            </li>
        @endauth
    </ul>
</nav>
