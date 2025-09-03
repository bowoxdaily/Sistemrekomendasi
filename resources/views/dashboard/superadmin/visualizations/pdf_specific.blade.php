@extends('layout.app')
@section('title', 'Export PDF Spesifik')
@section('content')
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
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <title>
            @switch($type)
                @case('employment')
                    Laporan Data Pekerjaan Alumni
                @break

                @case('education')
                    Laporan Data Pendidikan Alumni
                @break

                @case('unemployment')
                    Laporan Alumni Belum Bekerja
                @break

                @default
                    Laporan Umum Alumni
            @endswitch
        </title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                color: #333;
            }

            .container {
                padding: 20px;
            }

            .report-header {
                text-align: center;
                margin-bottom: 20px;
            }

            .report-header h1 {
                color: #3f51b5;
                font-size: 22px;
                margin: 0 0 10px 0;
            }

            .summary-box {
                background: #f5f7ff;
                border: 1px solid #dbe1ff;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 20px;
            }

            .summary-label {
                font-weight: bold;
                color: #555;
                display: inline-block;
                width: 120px;
            }

            .summary-value {
                color: #333;
            }

            .highlight-value {
                color: #3f51b5;
                font-weight: bold;
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            .data-table th {
                background: #3f51b5;
                color: #fff;
                font-weight: bold;
                padding: 8px;
                border: 1px solid #ddd;
            }

            .data-table td {
                padding: 7px;
                border: 1px solid #ddd;
                vertical-align: top;
            }

            .data-table tr:nth-child(even) {
                background: #f9f9ff;
            }

            .name-cell {
                font-weight: bold;
                color: #333;
            }

            .info-block {
                margin-bottom: 4px;
            }

            .info-label {
                font-weight: bold;
                min-width: 80px;
                color: #555;
                display: inline-block;
            }

            .info-value {
                display: inline-block;
            }

            .status-badge {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 3px;
                font-size: 10px;
                font-weight: bold;
                color: #fff;
            }

            .badge-success {
                background: #43a047;
            }

            .badge-info {
                background: #1e88e5;
            }

            .badge-warning {
                background: #ffa000;
            }

            .badge-secondary {
                background: #888;
            }

            .text-small {
                font-size: 10px;
            }

            .text-muted {
                color: #666;
            }

            .report-footer {
                margin-top: 30px;
                padding-top: 15px;
                border-top: 1px solid #ddd;
                text-align: center;
                font-size: 10px;
                color: #777;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="report-header">
                <h1>
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
                </h1>
                <p>Tanggal Laporan: {{ $generatedAt }}</p>
            </div>
            <div class="summary-box">
                <span class="summary-label">Total Data:</span>
                <span class="summary-value highlight-value">{{ count($students) }} alumni</span>
                <span class="summary-label" style="margin-left:30px;">Tahun:</span>
                <span class="summary-value">{{ $year ?: 'Semua' }}</span>
                <span class="summary-label" style="margin-left:30px;">Jurusan:</span>
                <span class="summary-value">{{ $department ?: 'Semua' }}</span>
                <span class="summary-label" style="margin-left:30px;">Status:</span>
                <span class="summary-value">{{ $status ?: 'Semua' }}</span>
            </div>
            @if (count($students) > 0)
                @if ($type == 'employment')
                    <table class="data-table">
                        <thead>
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
                                            class="status-badge badge-{{ ($student->dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'success' : 'info' }}">
                                            {{ ($student->dataKerja->sesuai_jurusan ?? '-') == 'ya' ? 'Sesuai Jurusan' : 'Tidak Sesuai' }}
                                        </span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($type == 'education')
                    <table class="data-table">
                        <thead>
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
                                            <span class="status-badge badge-success">Beasiswa</span>
                                            <div class="text-small">{{ $student->dataKuliah->nama_beasiswa ?? '' }}</div>
                                        @else
                                            <span class="status-badge badge-info">Non-Beasiswa</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif($type == 'unemployment')
                    <table class="data-table">
                        <thead>
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
                                    <td><span class="status-badge badge-warning">Belum Bekerja</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="data-table">
                        <thead>
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
                                    <td><span class="status-badge badge-{{ statusBadge($student->status_setelah_lulus) }}">
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
            @else
                <div
                    style="text-align: center; padding: 30px 0; background-color: #f8f9fa; border: 1px solid #e9ecef; border-radius: 5px;">
                    <h3 style="color: #6c757d; font-size: 18px;">Tidak ada data yang sesuai dengan kriteria yang dipilih.
                    </h3>
                    <p style="color: #6c757d; margin-top: 10px;">Silakan ubah filter dan coba lagi.</p>
                </div>
            @endif
            <div class="report-footer">
                <p>Laporan Tracer Study - {{ $generatedAt }}</p>
                <p>Digenerate oleh Sistem Tracer Study & Rekomendasi Karir</p>
            </div>
        </div>
    </body>

    </html>
@endsection
