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

    <!-- Modal untuk kuesioner telah dihapus karena siswa langsung diarahkan ke halaman kuesioner -->

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
                                <input type="text" class="form-control" placeholder="BEASISWA KIP" name="nama_beasiswa">
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

    <!-- Modal input data belum kerja jika status belum kerja dan belum mengisi kuesioner tidak ada di sini lagi karena langsung dialihkan ke halaman kuesioner oleh middleware -->
    @if (Auth::check() &&
            Auth::user()->role === 'siswa' &&
            Auth::user()->student->status_setelah_lulus === 'belum_kerja' &&
            !Auth::user()->student->has_completed_questionnaire &&
            !Auth::user()->student->is_profile_complete)
        <div class="modal fade" id="belumKerjaModal" tabindex="-1" role="dialog"
            aria-labelledby="belumKerjaModalLabel" aria-hidden="true">
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
    @endif

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ======= SHOW MODAL SESUAI STATUS =======
            @if (Auth::check() && Auth::user()->role === 'siswa')
                // Siswa dengan status belum_bekerja sudah dialihkan oleh middleware
                // Jadi kita hanya perlu menampilkan modal untuk status lainnya
                @if (!Auth::user()->student->is_profile_complete)
                    @if (Auth::user()->student->status_setelah_lulus === 'kuliah')
                        $('#kuliahModal').modal('show');
                    @elseif (Auth::user()->student->status_setelah_lulus === 'kerja')
                        $('#kerjaModal').modal('show');
                        // Modal untuk belum_kerja sudah dimodifikasi untuk menyertakan kondisi has_completed_questionnaire
                    @elseif (Auth::user()->student->status_setelah_lulus === 'belum_kerja' && Auth::user()->student->has_completed_questionnaire)
                        $('#belumKerjaModal').modal('show');
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
