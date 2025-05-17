@extends('layout.app')

@section('title', 'Dashboard')

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

    <!-- Modal untuk belum kerja - Dengan pengecekan has_completed_questionnaire -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
            !Auth::user()->student->has_completed_questionnaire)
        <div class="modal fade" id="belumKerjaModal" tabindex="-1" role="dialog" aria-labelledby="belumKerjaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="belumKerjaForm">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="belumKerjaModalLabel">Lengkapi Data Anda</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Alasan Belum Bekerja</label>
                                <textarea class="form-control" name="alasan" rows="3" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Keterampilan yang Dimiliki (opsional)</label>
                                <textarea class="form-control" name="keterampilan" rows="2"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Minat Bidang Kerja (opsional)</label>
                                <textarea class="form-control" name="minat" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>



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
