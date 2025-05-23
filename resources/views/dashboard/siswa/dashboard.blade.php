@extends('layout.app')

@section('title', 'Dashboard | Siswa')

@section('content')
    <!-- Alert jika profil belum lengkap -->
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif

    <!-- Alert untuk info -->
    @if (session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <!-- Top Row: Welcome message and date -->
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Selamat Datang, {{ Auth::user()->student->nama_lengkap }}</h3>
                    <h6 class="font-weight-normal mb-0">
                        Status: 
                        @php
                            $status = Auth::user()->student->status_setelah_lulus;
                            $badgeClass = 'primary';
                            $iconClass = 'user-graduate';
                            
                            if($status === 'belum_kerja') {
                                $badgeClass = 'warning';
                                $iconClass = 'search';
                            } elseif($status === 'kerja') {
                                $badgeClass = 'success';
                                $iconClass = 'briefcase';
                            } elseif($status === 'kuliah') {
                                $badgeClass = 'info';
                                $iconClass = 'university';
                            }
                        @endphp
                        <span class="text-{{ $badgeClass }} font-weight-bold">
                            <i class="fas fa-{{ $iconClass }} mr-1"></i>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                        <span class="ml-3 badge badge-{{ Auth::user()->student->is_profile_complete ? 'success' : 'warning' }}">
                            {{ Auth::user()->student->is_profile_complete ? 'Profil Lengkap' : 'Profil Belum Lengkap' }}
                        </span>
                    </h6>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="justify-content-end d-flex">
                        <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                            <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button"
                                id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <i class="mdi mdi-calendar"></i> {{ date('d M Y') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                                <a class="dropdown-item" href="#">Lihat Kalender</a>
                                <a class="dropdown-item" href="#">Lihat Event</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card tale-bg">
                <div class="card-people mt-auto">
                    <img src="{{ asset('admin/images/dashboard/people.svg') }}" alt="people">
                    <div class="weather-info">
                        <div class="d-flex">
                            <div class="ml-2">
                                <h4 class="location font-weight-normal">Alumni SMKN 1 Terisi</h4>
                                <h6 class="font-weight-normal">Tahun Lulus: {{ Auth::user()->student->tanggal_lulus ? date('Y', strtotime(Auth::user()->student->tanggal_lulus)) : 'Belum diatur' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7 grid-margin transparent">
            <div class="row">
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-tale">
                        <div class="card-body">
                            <p class="mb-4">Status Profil</p>
                            <p class="fs-30 mb-2">{{ Auth::user()->student->is_profile_complete ? 'Lengkap' : 'Belum Lengkap' }}</p>
                            <p>{{ Auth::user()->student->is_profile_complete ? 'Profil Anda sudah diisi dengan lengkap' : 'Silahkan lengkapi profil Anda' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                        <div class="card-body">
                            @if(Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                                <p class="mb-4">Status Kuesioner</p>
                                <p class="fs-30 mb-2">{{ Auth::user()->student->has_completed_questionnaire ? 'Selesai' : 'Belum Diisi' }}</p>
                                <p>{{ Auth::user()->student->has_completed_questionnaire ? 'Anda sudah mengisi kuesioner' : 'Isi kuesioner untuk mendapat rekomendasi' }}</p>
                            @elseif(Auth::user()->student->status_setelah_lulus === 'kerja')
                                <p class="mb-4">Status Pekerjaan</p>
                                <p class="fs-30 mb-2">{{ ucfirst(Auth::user()->student->jenis_pekerjaan ?? 'Aktif') }}</p>
                                <p>Anda sedang bekerja di {{ Auth::user()->student->nama_perusahaan ?? 'perusahaan' }}</p>
                            @else
                                <p class="mb-4">Status Pendidikan</p>
                                @php
                                    $dataKuliah = \App\Models\DataKuliah::where('student_id', Auth::user()->student->id)->first();
                                @endphp
                                <p class="fs-30 mb-2">{{ $dataKuliah->jenjang ?? 'Kuliah' }}</p>
                                <p>{{ $dataKuliah ? 'Jurusan ' . $dataKuliah->jurusan : 'Melanjutkan pendidikan tinggi' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                    <div class="card card-light-blue">
                        <div class="card-body">
                            <p class="mb-4">Status Setelah Lulus</p>
                            <p class="fs-30 mb-2">{{ ucfirst(str_replace('_', ' ', Auth::user()->student->status_setelah_lulus)) }}</p>
                            <p>{{ Auth::user()->student->status_setelah_lulus === 'belum_kerja' ? 'Lihat rekomendasi pekerjaan' : 'Terima kasih atas informasinya' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                    <div class="card card-light-danger">
                        <div class="card-body">
                            @if(Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                                <p class="mb-4">Rekomendasi</p>
                                @php
                                    $latestResponse = \App\Models\QuestionnaireResponse::where('student_id', Auth::user()->student->id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                                @endphp
                                
                                @if($latestResponse)
                                    <div class="fs-30 mb-2">{!! $latestResponse->getRecommendationBadge() !!}</div>
                                    <p>
                                        @if($latestResponse->hasRecommendations())
                                            Tersedia {{ count($latestResponse->getFormattedRecommendations()) }} rekomendasi pekerjaan
                                        @else
                                            Silahkan isi kuesioner untuk mendapatkan rekomendasi
                                        @endif
                                    </p>
                                @else
                                    <p class="fs-30 mb-2">Belum Ada</p>
                                    <p>Isi kuesioner untuk melihat rekomendasi</p>
                                @endif
                            @elseif(Auth::user()->student->status_setelah_lulus === 'kerja')
                                <p class="mb-4">Informasi Gaji</p>
                                <p class="fs-30 mb-2">{{ Auth::user()->student->gaji ? 'Rp ' . number_format(Auth::user()->student->gaji, 0, ',', '.') : 'Tidak Ditampilkan' }}</p>
                                <p>{{ Auth::user()->student->gaji ? 'Gaji saat ini' : 'Data gaji tidak tersedia' }}</p>
                            @else
                                <p class="mb-4">Status Beasiswa</p>
                                @php
                                    $dataKuliah = \App\Models\DataKuliah::where('student_id', Auth::user()->student->id)->first();
                                @endphp
                                <p class="fs-30 mb-2">{{ $dataKuliah && $dataKuliah->status_beasiswa === 'ya' ? 'Aktif' : 'Tidak Ada' }}</p>
                                <p>{{ $dataKuliah && $dataKuliah->status_beasiswa === 'ya' ? ($dataKuliah->nama_beasiswa ?? 'Menerima beasiswa') : 'Tidak menerima beasiswa' }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="row">
        <!-- Recommendation/Information Section -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">
                        @if (Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                            Rekomendasi Pekerjaan
                        @elseif (Auth::user()->student->status_setelah_lulus === 'kerja')
                            Informasi Pekerjaan Anda
                        @else
                            Informasi Pendidikan Anda
                        @endif
                    </h4>
                    
                    @if (Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                        @if (Auth::user()->student->has_completed_questionnaire)
                            <div class="text-center">
                                <h5>Rekomendasi Pekerjaan untuk Anda:</h5>
                                <div class="list-group">
                                    @php
                                        $latestResponse = \App\Models\QuestionnaireResponse::where('student_id', Auth::user()->student->id)
                                            ->orderBy('created_at', 'desc')
                                            ->first();
                                        
                                        $topRecommendations = $latestResponse ? $latestResponse->getTopRecommendations(5) : [];
                                    @endphp
                                    
                                    @if (!empty($topRecommendations))
                                        @foreach ($topRecommendations as $recommendation)
                                            <a href="#" class="list-group-item list-group-item-action">
                                                {{ $recommendation['job']->job_title ?? $recommendation['job']->name }}
                                                <span class="badge badge-{{ 
                                                    $recommendation['match_percentage'] >= 80 ? 'success' : 
                                                    ($recommendation['match_percentage'] >= 60 ? 'info' : 'primary') 
                                                }} float-right">{{ number_format($recommendation['match_percentage'], 1) }}% Match</span>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle mr-1"></i> Rekomendasi Anda sedang diproses atau belum tersedia.
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('student.recommendation.show') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-list mr-1"></i> Lihat Semua Rekomendasi
                                </a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <img src="{{ asset('admin/images/undraw_career_progress.svg') }}" style="max-height: 150px;" class="img-fluid mb-3">
                                <h5>Dapatkan Rekomendasi Pekerjaan</h5>
                                <p class="text-muted">Lengkapi kuesioner untuk mendapatkan rekomendasi pekerjaan yang sesuai dengan keterampilan dan minat Anda.</p>
                                <a href="{{ url('siswa/questionnaire') }}" class="btn btn-primary">
                                    <i class="fas fa-clipboard-list mr-1"></i> Isi Kuesioner Sekarang
                                </a>
                            </div>
                        @endif
                    @elseif (Auth::user()->student->status_setelah_lulus === 'kerja')
                        <div class="text-center py-3">
                            <img src="{{ asset('admin/images/undraw_work.svg') }}" style="max-height: 150px;" class="img-fluid mb-3">
                            <h5><i class="fas fa-briefcase mr-2"></i>Status: Sudah Bekerja</h5>
                            <div class="card bg-light p-3 mt-3">
                                <div class="row">
                                    <div class="col-md-6 text-left border-right">
                                        <p class="mb-1"><strong>Perusahaan:</strong></p>
                                        <p class="text-muted">{{ Auth::user()->student->nama_perusahaan ?? 'Belum diisi' }}</p>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <p class="mb-1"><strong>Posisi:</strong></p>
                                        <p class="text-muted">{{ Auth::user()->student->posisi ?? 'Belum diisi' }}</p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6 text-left border-right">
                                        <p class="mb-1"><strong>Jenis Pekerjaan:</strong></p>
                                        <p class="text-muted">{{ Auth::user()->student->jenis_pekerjaan ?? 'Belum diisi' }}</p>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <p class="mb-1"><strong>Mulai Bekerja:</strong></p>
                                        <p class="text-muted">{{ Auth::user()->student->tanggal_mulai ? date('d-m-Y', strtotime(Auth::user()->student->tanggal_mulai)) : 'Belum diisi' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif (Auth::user()->student->status_setelah_lulus === 'kuliah')
                        <div class="text-center py-3">
                            <img src="{{ asset('admin/images/undraw_education.svg') }}" style="max-height: 150px;" class="img-fluid mb-3">
                            <h5><i class="fas fa-university mr-2"></i>Status: Kuliah</h5>
                            @php
                                $dataKuliah = \App\Models\DataKuliah::where('student_id', Auth::user()->student->id)->first();
                            @endphp
                            
                            @if($dataKuliah)
                                <div class="card bg-light p-3 mt-3">
                                    <div class="row">
                                        <div class="col-md-6 text-left border-right">
                                            <p class="mb-1"><strong>Perguruan Tinggi:</strong></p>
                                            <p class="text-muted">{{ $dataKuliah->nama_pt }}</p>
                                        </div>
                                        <div class="col-md-6 text-left">
                                            <p class="mb-1"><strong>Program Studi:</strong></p>
                                            <p class="text-muted">{{ $dataKuliah->jurusan }} ({{ $dataKuliah->jenjang }})</p>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6 text-left border-right">
                                            <p class="mb-1"><strong>Tahun Masuk:</strong></p>
                                            <p class="text-muted">{{ $dataKuliah->tahun_masuk }}</p>
                                        </div>
                                        <div class="col-md-6 text-left">
                                            <p class="mb-1"><strong>Status Beasiswa:</strong></p>
                                            <p class="text-muted">
                                                @if($dataKuliah->status_beasiswa === 'ya')
                                                    <span class="badge badge-success">Menerima Beasiswa</span>
                                                    @if($dataKuliah->nama_beasiswa)
                                                        <small class="d-block mt-1">{{ $dataKuliah->nama_beasiswa }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-secondary">Tidak Menerima Beasiswa</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @if($dataKuliah->prestasi_akademik)
                                        <div class="row mt-2">
                                            <div class="col-12 text-left">
                                                <p class="mb-1"><strong>Prestasi Akademik:</strong></p>
                                                <p class="text-muted">{{ $dataKuliah->prestasi_akademik }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Data pendidikan belum lengkap. Silahkan lengkapi data Anda.
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    
        <!-- Info and Resources Section -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title">Informasi & Berita</h4>
                        <a href="#" class="text-info">Lihat Semua</a>
                    </div>
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Tips Mencari Kerja</h6>
                                <small class="text-muted">3 hari yang lalu</small>
                            </div>
                            <p class="mb-1 text-muted small">Panduan lengkap untuk lulusan baru dalam mencari pekerjaan pertama.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Info Beasiswa Perguruan Tinggi</h6>
                                <small class="text-muted">1 minggu yang lalu</small>
                            </div>
                            <p class="mb-1 text-muted small">Informasi tentang berbagai program beasiswa untuk melanjutkan pendidikan.</p>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Workshop Persiapan Karir</h6>
                                <small class="text-muted">2 minggu yang lalu</small>
                            </div>
                            <p class="mb-1 text-muted small">Workshop online untuk mempersiapkan karir setelah lulus.</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk belum kerja - Dengan pengecekan has_completed_questionnaire -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
            !Auth::user()->student->has_completed_questionnaire)
        


        <!-- Modal Rekomendasi Pekerjaan - Kondisi disederhanakan -->
        <div class="modal fade" id="rekomendasiModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-lightbulb mr-2"></i>Rekomendasi Pekerjaan
                        </h5>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-4">
                            <h4 class="font-weight-bold">Butuh Saran Karir?</h4>
                            <p class="text-muted">
                                Kami akan membantu Anda menemukan pekerjaan yang sesuai dengan kemampuan dan minat Anda
                                melalui kuesioner singkat.
                            </p>
                        </div>
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            Dengan mengisi kuesioner, Anda akan mendapatkan rekomendasi pekerjaan yang lebih akurat.
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center border-top-0">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="btnSkipRekomendasi2">
                            <i class="fas fa-times mr-2"></i>Nanti Saja
                        </button>
                        <button type="button" class="btn btn-primary btn-lg px-4" id="btnMauRekomendasi">
                            <i class="fas fa-check mr-2"></i>Dapatkan Rekomendasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal input data kuliah jika status lulus dan kuliah -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'kuliah' &&
            !Auth::user()->student->is_profile_complete)
        {{-- Modal untuk Data Kuliah --}}
        <div class="modal fade" id="kuliahModal" tabindex="-1" role="dialog" aria-labelledby="kuliahModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="kuliahForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kuliahModalLabel">Lengkapi Data Kuliah Anda</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Perguruan Tinggi</label>
                                <input type="text" class="form-control" name="nama_pt" placeholder="Universitas XXX"
                                    required>
                            </div>
                            <input type="hidden" name="status_setelah_lulus" value="kuliah">

                            <div class="form-group">
                                <label>Jurusan</label>
                                <input type="text" class="form-control" name="jurusan"
                                    placeholder="Teknik Informatika, RPL" required>
                            </div>
                            <div class="form-group">
                                <label>Jenjang</label>
                                <select class="form-control" name="jenjang" required>
                                    <option value="" disabled selected hidden>Pilih Jenjang</option>
                                    <option value="D3">D3</option>
                                    <option value="D4">D4</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tahun Masuk</label>
                                <input type="number" class="form-control" name="tahun_masuk" placeholder="2023"
                                    min="1900" max="2099" step="1" required>
                            </div>
                            <div class="form-group">
                                <label>Status Beasiswa</label>
                                <select class="form-control" name="status_beasiswa" id="status_beasiswa" required>
                                    <option value="" disabled selected hidden>Pilih Status Beasiswa</option>
                                    <option value="ya">Ya</option>
                                    <option value="tidak">Tidak</option>
                                </select>
                            </div>

                            <div class="form-group" id="beasiswa_nama_group" style="display: none;">
                                <label>Nama Beasiswa</label>
                                <input type="text" class="form-control" placeholder="BEASISWA KIP"
                                    name="nama_beasiswa">
                            </div>
                            <div class="form-group mt-3">
                                <label>Prestasi Akademik</label>
                                <textarea class="form-control" name="prestasi_akademik" rows="3"
                                    placeholder="Contoh: Juara 1 Lomba Karya Tulis Ilmiah, IPK 3.9, Sertifikasi TOEFL 600, dll"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal input data kerja jika status kerja -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'kerja' &&
            !Auth::user()->student->is_profile_complete)
        <div class="modal fade" id="kerjaModal" tabindex="-1" role="dialog" aria-labelledby="kerjaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="kerjaForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="kerjaModalLabel">Lengkapi Data Pekerjaan Anda</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nama Perusahaan</label>
                                <input type="text" class="form-control" name="nama_perusahaan" required>
                            </div>
                            <div class="form-group">
                                <label>Posisi</label>
                                <input type="text" class="form-control" name="posisi" required>
                            </div>
                            <div class="form-group">
                                <label>Jenis Pekerjaan</label>
                                <input type="text" class="form-control" name="jenis_pekerjaan" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                            <div class="form-group">
                                <label>Gaji (opsional)</label>
                                <input type="number" class="form-control" name="gaji">
                            </div>
                            <div class="form-group">
                                <label>Pekerjaan Sesuai Jurusan?</label>
                                <select class="form-control" name="sesuai_jurusan" required>
                                    <option value="ya">Ya</option>
                                    <option value="tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Kompetensi yang Dibutuhkan (opsional)</label>
                                <textarea class="form-control" name="kompetensi_dibutuhkan" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Rekomendasi Pekerjaan -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
            !Auth::user()->student->has_completed_questionnaire)
        <div class="modal fade" id="rekomendasiModal" data-backdrop="static" data-keyboard="false" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-lightbulb mr-2"></i>Rekomendasi Pekerjaan
                        </h5>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-4">
                            <h4 class="font-weight-bold">Butuh Saran Karir?</h4>
                            <p class="text-muted">
                                Kami dapat membantu Anda menemukan pekerjaan yang sesuai dengan kemampuan dan minat Anda
                                melalui kuesioner singkat.
                            </p>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Dengan mengisi kuesioner, Anda akan mendapatkan rekomendasi pekerjaan yang lebih akurat.
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="btnSkipRekomendasi">
                            <i class="fas fa-times mr-2"></i>Nanti Saja
                        </button>
                        <button type="button" class="btn btn-primary btn-lg px-4" id="btnMauRekomendasi">
                            <i class="fas fa-check mr-2"></i>Dapatkan Rekomendasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        /* Card styling */
        .card-tale {
            background: linear-gradient(to right, #4747a1, #7978e9);
            color: #ffffff;
        }
        
        .card-dark-blue {
            background: linear-gradient(to right, #376bff, #4ca2ff);
            color: #ffffff;
        }
        
        .card-light-blue {
            background: linear-gradient(to right, #13b5ea, #0dc8f2);
            color: #ffffff;
        }
        
        .card-light-danger {
            background: linear-gradient(to right, #f48a63, #ff9f87);
            color: #ffffff;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {
            // Tampilkan modal konfirmasi untuk siswa dengan status belum kerja
            @if (Auth::check() && Auth::user()->role === 'siswa' && Auth::user()->student->status_setelah_lulus === 'belum_kerja')
                $('#konfirmasiRekomendasiModal').modal('show');

                // Handle tombol "Ya, Saya Mau"
                $('#btnGetRekomendasi').click(function() {
                    $('#konfirmasiRekomendasiModal').modal('hide');
                    window.location.href = _baseURL + 'student/kuis';
                });

                // Handle tombol "Nanti Saja"
                $('#btnSkipRekomendasi, #btnSkipRekomendasi2').click(function() {
                    $('#konfirmasiRekomendasiModal').modal('hide');
                    $('#rekomendasiModal').modal('hide');
                    $('#belumKerjaModal').modal('show');
                });

                // Handler untuk tombol di modal rekomendasi
                $('#btnMauRekomendasi').click(function() {
                    window.location.href = _baseURL + 'student/kuis';
                });
            @endif

            // Show recommendation modal for unemployed users
            @if (Auth::check() &&
                    Auth::user()->role === 'siswa' &&
                    Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
                    !Auth::user()->student->has_completed_questionnaire)

                // Show recommendation modal first
                $('#rekomendasiModal').modal('show');

                // Handle recommendation buttons
                $('#btnMauRekomendasi').click(function() {
                    window.location.href = _baseURL + 'siswa/questionnaire';
                });

                $('#btnSkipRekomendasi').click(function() {
                    $('#rekomendasiModal').modal('hide');
                    // Show form for those who skip recommendation
                    $('#belumKerjaModal').modal('show');
                });
            @endif

            // ======= SHOW MODAL SESUAI STATUS =======
            @if (Auth::check() && Auth::user()->role === 'siswa')
                @if (!Auth::user()->student->is_profile_complete)
                    @if (Auth::user()->student->status_setelah_lulus === 'kuliah')
                        $('#kuliahModal').modal('show');
                    @elseif (Auth::user()->student->status_setelah_lulus === 'kerja')
                        $('#kerjaModal').modal('show');
                    @endif
                @endif
            @endif

            // ======= HANDLE BEASISWA (KHUSUS KULIAH) =======
            $('#status_beasiswa').on('change', function() {
                const beasiswaGroup = $('#beasiswa_nama_group');
                const beasiswaInput = beasiswaGroup.find('input');

                if ($(this).val() === 'ya') {
                    beasiswaGroup.slideDown();
                    beasiswaInput.attr('required', true);
                } else {
                    beasiswaGroup.slideUp();
                    beasiswaInput.removeAttr('required').val('');
                }
            });

            // ======= SUBMIT DATA KULIAH =======
            const kuliahForm = $('#kuliahForm');
            if (kuliahForm.length) {
                kuliahForm.on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: _baseURL + 'api/siswa/insert/data/graduation',
                        type: 'POST',
                        data: kuliahForm.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#kuliahModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data kuliah berhasil disimpan!'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menyimpan data kuliah.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menyimpan data kuliah.'
                            });
                        }
                    });
                });
            }

            // ======= SUBMIT DATA KERJA =======
            const kerjaForm = $('#kerjaForm');
            if (kerjaForm.length) {
                kerjaForm.on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: _baseURL + 'api/siswa/insert/data/graduation',
                        type: 'POST',
                        data: kerjaForm.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#kerjaModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data kerja berhasil disimpan!'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menyimpan data kerja.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menyimpan data kerja.'
                            });
                        }
                    });
                });
            }

            // ======= SUBMIT DATA BELUM KERJA =======
            const belumKerjaForm = $('#belumKerjaForm');
            if (belumKerjaForm.length) {
                belumKerjaForm.on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: _baseURL + 'api/siswa/insert/data/graduation',
                        type: 'POST',
                        data: belumKerjaForm.serialize(),
                        success: function(response) {
                            if (response.success) {
                                $('#belumKerjaModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Data belum kerja berhasil disimpan!'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message ||
                                        'Terjadi kesalahan saat menyimpan data.'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menyimpan data belum kerja.'
                            });
                        }
                    });
                });
            }
        });
    </script>
@endpush
