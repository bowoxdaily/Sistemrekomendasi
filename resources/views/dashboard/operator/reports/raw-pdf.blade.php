<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raw Data Tracer Study</title>
    <style>
        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        
        /* Layout containers */
        .container {
            padding: 20px;
        }
        
        /* Header styling */
        .report-header {
            position: relative;
            padding-bottom: 15px;
            margin-bottom: 25px;
            border-bottom: 3px solid #3f51b5;
            text-align: center;
        }
        
        .report-header h1 {
            color: #3f51b5;
            font-size: 24px;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .report-header p {
            color: #666;
            margin: 5px 0;
            font-size: 12px;
        }
        
        /* Summary section */
        .summary-box {
            background: #f5f7ff;
            border: 1px solid #dbe1ff;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
        }
        
        .summary-box h2 {
            color: #3f51b5;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dbe1ff;
        }
        
        .summary-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }
        
        .summary-item {
            flex: 1;
            min-width: 150px;
            margin-bottom: 10px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        
        .summary-value {
            color: #333;
            font-weight: bold;
            font-size: 16px;
        }
        
        /* Student card styling */
        .student-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 30px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            page-break-inside: avoid;
        }
        
        .student-header {
            background-color: #3f51b5;
            color: white;
            padding: 10px 15px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        
        .student-header h2 {
            margin: 0;
            font-size: 16px;
        }
        
        .student-content {
            padding: 15px;
        }
        
        .data-section {
            margin-bottom: 20px;
        }
        
        .data-section:last-child {
            margin-bottom: 0;
        }
        
        .data-title {
            font-weight: bold;
            margin-bottom: 10px;
            padding: 5px 10px;
            background-color: #f0f6ff;
            border-left: 4px solid #3f51b5;
            color: #3f51b5;
        }
        
        /* Table styling */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px dotted #eee;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 180px;
            color: #555;
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
            color: white;
        }
        
        .badge-success {
            background-color: #4CAF50;
        }
        
        .badge-info {
            background-color: #2196F3;
        }
        
        .badge-warning {
            background-color: #FFC107;
            color: #333;
        }
        
        /* Footer styling */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #777;
        }
        
        /* Utilities */
        .text-center {
            text-align: center;
        }
        
        .text-muted {
            color: #666;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Report Header -->
        <div class="report-header">
            <h1>DATA LENGKAP TRACER STUDY ALUMNI</h1>
            <p>Digenerate pada: {{ $generatedAt }}</p>
        </div>
        
        <!-- Summary Section -->
        <div class="summary-box">
            <h2>RINGKASAN DATA</h2>
            
            <div class="summary-row">
                <div class="summary-item">
                    <span class="summary-label">Total Alumni</span>
                    <div class="summary-value">{{ count($students) }}</div>
                </div>
                
                <div class="summary-item">
                    <span class="summary-label">Bekerja</span>
                    <div class="summary-value">{{ $students->where('status_setelah_lulus', 'kerja')->count() }}</div>
                </div>
                
                <div class="summary-item">
                    <span class="summary-label">Kuliah</span>
                    <div class="summary-value">{{ $students->where('status_setelah_lulus', 'kuliah')->count() }}</div>
                </div>
                
                <div class="summary-item">
                    <span class="summary-label">Belum Bekerja</span>
                    <div class="summary-value">{{ $students->where('status_setelah_lulus', 'belum_kerja')->count() }}</div>
                </div>
            </div>
        </div>
        
        <!-- Student Cards -->
        <h2 style="color: #3f51b5; font-size: 18px; margin-bottom: 20px;">DATA LENGKAP ALUMNI ({{ count($students) }} orang)</h2>
        
        @foreach($students as $student)
        <div class="student-card">
            <div class="student-header">
                <h2>{{ $student->nama_lengkap }} (ID: {{ $student->id }})</h2>
            </div>
            
            <div class="student-content">
                <!-- Basic Information -->
                <div class="data-section">
                    <div class="data-title">Informasi Dasar</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">NISN:</td>
                            <td>{{ $student->nisn ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin:</td>
                            <td>{{ ucfirst($student->jenis_kelamin ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Lahir:</td>
                            <td>{{ $student->tanggal_lahir ? date('d-m-Y', strtotime($student->tanggal_lahir)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Alamat:</td>
                            <td>{{ $student->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">No. Telepon:</td>
                            <td>{{ $student->nomor_telepon ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jurusan:</td>
                            <td>
                                @php
                                    $jurusan = \App\Models\Jurusan::find($student->jurusan_id);
                                @endphp
                                {{ $jurusan ? $jurusan->nama : '-' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Lulus:</td>
                            <td>{{ $student->tanggal_lulus ? date('d-m-Y', strtotime($student->tanggal_lulus)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status Setelah Lulus:</td>
                            <td>
                                @if($student->status_setelah_lulus == 'kerja')
                                    <span class="status-badge badge-success">Bekerja</span>
                                @elseif($student->status_setelah_lulus == 'kuliah')
                                    <span class="status-badge badge-info">Kuliah</span>
                                @else
                                    <span class="status-badge badge-warning">Belum Bekerja</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Employment Data -->
                @if($student->status_setelah_lulus == 'kerja' && $student->dataKerja)
                <div class="data-section">
                    <div class="data-title">Data Pekerjaan</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nama Perusahaan:</td>
                            <td>{{ $student->dataKerja->nama_perusahaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Posisi:</td>
                            <td>{{ $student->dataKerja->posisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Pekerjaan:</td>
                            <td>{{ $student->dataKerja->jenis_pekerjaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Mulai:</td>
                            <td>{{ isset($student->dataKerja->tanggal_mulai) ? date('d-m-Y', strtotime($student->dataKerja->tanggal_mulai)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Gaji:</td>
                            <td>{{ isset($student->dataKerja->gaji) ? 'Rp ' . number_format($student->dataKerja->gaji, 0, ',', '.') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Sesuai Jurusan:</td>
                            <td>{{ ucfirst($student->dataKerja->sesuai_jurusan ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kompetensi Dibutuhkan:</td>
                            <td>{{ $student->dataKerja->kompetensi_dibutuhkan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                @endif
                
                <!-- Education Data -->
                @if($student->status_setelah_lulus == 'kuliah' && $student->dataKuliah)
                <div class="data-section">
                    <div class="data-title">Data Pendidikan</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nama Perguruan Tinggi:</td>
                            <td>{{ $student->dataKuliah->nama_pt ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jurusan:</td>
                            <td>{{ $student->dataKuliah->jurusan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenjang:</td>
                            <td>{{ $student->dataKuliah->jenjang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tahun Masuk:</td>
                            <td>{{ $student->dataKuliah->tahun_masuk ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Status Beasiswa:</td>
                            <td>{{ ucfirst($student->dataKuliah->status_beasiswa ?? '-') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nama Beasiswa:</td>
                            <td>{{ $student->dataKuliah->nama_beasiswa ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Prestasi Akademik:</td>
                            <td>{{ $student->dataKuliah->prestasi_akademik ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                @endif
            </div>
        </div>
        
        @if(!$loop->last)
        <div style="page-break-after: always;"></div>
        @endif
        
        @endforeach
        
        <!-- Footer -->
        <div class="report-footer">
            <p>Laporan Raw Data Tracer Study - {{ $generatedAt }}</p>
            <p>Digenerate oleh Sistem Tracer Study & Rekomendasi Karir</p>
        </div>
    </div>
</body>
</html>
