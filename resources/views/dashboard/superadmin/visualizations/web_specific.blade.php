@php
    // Helper untuk badge status
    function statusBadge($status)
    {
        switch ($status) {
            case 'kerja':
                return 'success';
            case 'kuliah':
                return 'info';
            case 'belum_kerja':
                return 'warning';
            default:
                return 'secondary';
        }
    }
@endphp
@extends('layout.app')
@section('title', 'Export Web Spesifik')
@section('content')
    <div class="container mt-4">
        <div class="report-header" style="text-align:center; margin-bottom:20px;">
            <h4 style="color:#3f51b5; font-size:22px; margin-bottom:5px;">
                @switch($type)
                    @case('employment')
                        LAPORAN DATA PEKERJAAN ALUMNI
                    @break

                    @case('education')
                        LAPORAN DATA PENDIDIKAN ALUMNI
                    @break

                    @case('unemployment')
                        LAPORAN ALUMNI BELUM BEKERJA
                    @break

                    @default
                        LAPORAN UMUM ALUMNI
                @endswitch
            </h4>
            <div class="text-muted">Tanggal Laporan: {{ $generatedAt }}</div>
        </div>

        <div class="summary-box"
            style="background:#f5f7ff; border:1px solid #dbe1ff; border-radius:5px; padding:12px; margin-bottom:20px;">
            <span class="summary-label" style="font-weight:bold; color:#555; width:120px; display:inline-block;">Total
                Data:</span>
            <span class="summary-value highlight-value" style="color:#3f51b5; font-weight:bold;">{{ count($students) }}
                alumni</span>
            <span class="summary-label" style="margin-left:30px; font-weight:bold; color:#555;">Tahun:</span>
            <span class="summary-value">{{ $year ?: 'Semua' }}</span>
            <span class="summary-label" style="margin-left:30px; font-weight:bold; color:#555;">Jurusan:</span>
            <span class="summary-value">{{ $department ?: 'Semua' }}</span>
            <span class="summary-label" style="margin-left:30px; font-weight:bold; color:#555;">Status:</span>
            <span class="summary-value">{{ $status ?: 'Semua' }}</span>
        </div>

        @if (count($students) > 0)
            <!-- Tombol Print -->
            <div class="d-flex justify-content-end mb-2 no-print">
                <button onclick="printTable()" class="btn btn-primary">
                    <i class="fas fa-print"></i> Print Tabel
                </button>
            </div>

            <!-- Area yang akan di-print -->
            <div id="printArea" style="margin-top: 0;" <!-- Header untuk print -->
                <div class="print-header" style="display: none; text-align: center; margin: 0; padding: 0;">
                    <h4 style="color: #000; font-size: 18px; margin: 0 0 8px 0; padding: 0;">
                        @switch($type)
                            @case('employment')
                                LAPORAN DATA PEKERJAAN ALUMNI
                            @break

                            @case('education')
                                LAPORAN DATA PENDIDIKAN ALUMNI
                            @break

                            @case('unemployment')
                                LAPORAN ALUMNI BELUM BEKERJA
                            @break

                            @default
                                LAPORAN UMUM ALUMNI
                        @endswitch
                    </h4>
                    <p style="margin: 0 0 3px 0; padding: 0; font-size: 12px;">
                        Total Data: {{ count($students) }} alumni |
                        Tahun: {{ $year ?: 'Semua' }} |
                        Jurusan: {{ $department ?: 'Semua' }} |
                        Status: {{ $status ?: 'Semua' }}
                    </p>
                    <p style="margin: 0 0 8px 0; padding: 0; font-size: 12px;">Tanggal Laporan: {{ $generatedAt }}</p>
                    <hr style="border-top: 2px solid #000; margin: 0 0 10px 0; padding: 0;">
                </div>

                @if ($type == 'employment')
                    <table class="table table-bordered" id="dataTable" style="margin-top: 0;" <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Tahun Lulus</th>
                            <th>Perusahaan</th>
                            <th>Posisi</th>
                            <th>Gaji</th>
                            <th>Kesesuaian</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $i => $student)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $student->nama_lengkap }}</td>
                                    <td>{{ $student->jurusan ? $student->jurusan->nama : '-' }}</td>
                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                    </td>
                                    <td>{{ $student->dataKerja->nama_perusahaan ?? '-' }}</td>
                                    <td>{{ $student->dataKerja->posisi ?? '-' }}</td>
                                    <td>{{ isset($student->dataKerja->gaji) ? 'Rp ' . number_format($student->dataKerja->gaji, 0, ',', '.') : '-' }}
                                    </td>
                                    <td><span
                                            class="badge badge-{{ ($student->dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'success' : 'info' }}">
                                            {{ ($student->dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'Sesuai Jurusan' : 'Tidak Sesuai' }}
                                        </span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($type == 'education')
                    <table class="table table-bordered" id="dataTable" style="margin-top: 0;" <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Tahun Lulus</th>
                            <th>Perguruan Tinggi</th>
                            <th>Prodi</th>
                            <th>Jenjang</th>
                            <th>Tahun Masuk</th>
                            <th>Beasiswa</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $i => $student)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $student->nama_lengkap }}</td>
                                    <td>{{ $student->jurusan ? $student->jurusan->nama : '-' }}</td>
                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                    </td>
                                    <td>{{ $student->dataKuliah->nama_pt ?? '-' }}</td>
                                    <td>{{ $student->dataKuliah->jurusan ?? '-' }}</td>
                                    <td>{{ $student->dataKuliah->jenjang ?? '-' }}</td>
                                    <td>{{ $student->dataKuliah->tahun_masuk ?? '-' }}</td>
                                    <td>
                                        @if (($student->dataKuliah->status_beasiswa ?? '-') == 'ya')
                                            <span class="badge badge-success">Beasiswa</span>
                                            <div class="text-small">{{ $student->dataKuliah->nama_beasiswa ?? '' }}</div>
                                        @else
                                            <span class="badge badge-info">Non-Beasiswa</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($type == 'unemployment')
                    <table class="table table-bordered" id="dataTable" style="margin-top: 0;" <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Tahun Lulus</th>
                            <th>Jenis Kelamin</th>
                            <th>Durasi Lulus</th>
                            <th>Alamat</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $i => $student)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $student->nama_lengkap }}</td>
                                    <td>{{ $student->jurusan ? $student->jurusan->nama : '-' }}</td>
                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                    </td>
                                    <td>{{ ucfirst($student->jenis_kelamin ?? '-') }}</td>
                                    <td>
                                        @php
                                            $duration = '-';
                                            if ($student->tanggal_lulus) {
                                                $graduationDate = \Carbon\Carbon::parse($student->tanggal_lulus);
                                                $now = \Carbon\Carbon::now();
                                                $durationMonths = $graduationDate->diffInMonths($now);
                                                if ($durationMonths < 12) {
                                                    $duration = $durationMonths . ' bulan';
                                                } else {
                                                    $years = floor($durationMonths / 12);
                                                    $months = $durationMonths % 12;
                                                    $duration =
                                                        $years . ' tahun ' . ($months > 0 ? $months . ' bulan' : '');
                                                }
                                            }
                                        @endphp
                                        {{ $duration }}
                                    </td>
                                    <td>{{ $student->alamat ?? '-' }}</td>
                                    <td><span class="badge badge-warning">Belum Bekerja</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="table table-bordered" id="dataTable" style="margin-top: 0;" <thead class="thead-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jurusan</th>
                            <th>Tahun Lulus</th>
                            <th>Status</th>
                            <th>Jenis Kelamin</th>
                            <th>Alamat</th>
                            <th>Kontak</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $i => $student)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $student->nama_lengkap }}</td>
                                    <td>{{ $student->jurusan ? $student->jurusan->nama : '-' }}</td>
                                    <td>{{ $student->tanggal_lulus ? date('Y', strtotime($student->tanggal_lulus)) : '-' }}
                                    </td>
                                    <td><span class="badge badge-{{ statusBadge($student->status_setelah_lulus) }}">
                                            @if ($student->status_setelah_lulus == 'kerja')
                                                Bekerja
                                            @elseif($student->status_setelah_lulus == 'kuliah')
                                                Kuliah
                                            @else
                                                Belum Bekerja
                                            @endif
                                        </span></td>
                                    <td>{{ ucfirst($student->jenis_kelamin ?? '-') }}</td>
                                    <td>{{ $student->alamat ?? '-' }}</td>
                                    <td>{{ $student->no_hp ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @else
            <div
                style="text-align: center; padding: 30px 0; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px;">
                <h3 style="color: #6c757d; font-size: 18px;">Tidak ada data yang sesuai dengan kriteria yang dipilih.</h3>
                <p style="color: #6c757d; margin-top: 10px;">Silakan ubah filter dan coba lagi.</p>
            </div>
        @endif

        <div class="report-footer"
            style="margin-top:30px; padding-top:15px; border-top:1px solid #ddd; text-align:center; font-size:10px; color:#777;">
            <p>Laporan Tracer Study - {{ $generatedAt }}</p>
            <p>Digenerate oleh Sistem Tracer Study & Rekomendasi Karir</p>
        </div>
    </div>

    <!-- CSS untuk Print -->
    <style>
        @media print {

            /* Reset semua margin dan padding */
            * {
                box-sizing: border-box;
                margin: 0 !important;
                padding: 0 !important;
            }

            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                width: 100% !important;
            }

            /* Sembunyikan semua elemen kecuali area print */
            body * {
                visibility: hidden;
            }

            /* Tampilkan hanya area print */
            #printArea,
            #printArea * {
                visibility: visible;
            }

            /* Posisikan area print */
            #printArea {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Tampilkan header print */
            .print-header {
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Reset container dan wrapper */
            .container {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: none !important;
            }

            /* Styling untuk print */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 12px !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                text-align: left !important;
                margin: 0 !important;
            }

            th {
                background-color: #f2f2f2 !important;
                font-weight: bold !important;
            }

            /* Hapus warna background badge saat print */
            .badge {
                background-color: transparent !important;
                color: #000 !important;
                border: 1px solid #000 !important;
                padding: 2px 4px !important;
                border-radius: 3px !important;
                margin: 0 !important;
            }

            /* Sembunyikan elemen yang tidak perlu di print */
            .no-print {
                display: none !important;
            }

            /* Atur margin halaman */
            @page {
                margin: 0.2cm !important;
                size: A4 landscape;
            }

            /* Pastikan tabel tidak terpotong */
            table {
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid;
            }

            /* Reset semua element yang mungkin punya margin/padding */
            h1,
            h2,
            h3,
            h4,
            h5,
            h6,
            p,
            div {
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Khusus untuk header print */
            .print-header h4 {
                margin: 0 0 8px 0 !important;
            }

            .print-header p {
                margin: 0 0 3px 0 !important;
            }

            .print-header hr {
                margin: 8px 0 10px 0 !important;
            }
        }

        /* Class untuk elemen yang tidak di-print */
        .no-print {
            /* Styling normal untuk tampilan layar */
        }
    </style>

    <!-- JavaScript untuk fungsi print -->
    <script>
        function printTable() {
            window.print();
        }
    </script>
@endsection
