<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisasi Data Tracer Study</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #222;
            margin: 0;
            padding: 24px 24px 12px 24px;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 28px;
            padding-bottom: 10px;
            border-bottom: 2px solid #2a80b9;
        }

        .header h1 {
            margin: 0;
            color: #2a80b9;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .header p {
            margin: 8px 0 0;
            color: #666;
            font-size: 15px;
        }

        .section {
            margin-bottom: 32px;
        }

        .section-title {
            margin-top: 0;
            margin-bottom: 16px;
            font-size: 20px;
            color: #2a80b9;
            border-bottom: 1.5px solid #eee;
            padding-bottom: 5px;
            letter-spacing: 0.5px;
        }

        .summary-box {
            background: #f4faff;
            border: 1.5px solid #2a80b9;
            border-radius: 8px;
            padding: 18px 20px 10px 20px;
            margin-bottom: 20px;
        }

        .summary-stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
            padding: 10px 18px;
            margin-bottom: 10px;
            border-right: 1.5px solid #e0e0e0;
            flex: 1;
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .stat-value {
            font-size: 26px;
            font-weight: bold;
            margin: 6px 0 2px 0;
            color: #2a80b9;
        }

        .stat-percent {
            font-size: 13px;
            color: #888;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 10px 0;
            font-size: 14px;
        }

        table th,
        table td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1.5px solid #e0e0e0;
        }

        table th {
            background-color: #eaf6fb;
            color: #2a80b9;
            font-weight: bold;
            border-top: 1.5px solid #2a80b9;
        }

        table tfoot td {
            font-weight: bold;
            background: #f4faff;
            color: #2a80b9;
        }

        .footer {
            text-align: right;
            font-size: 12px;
            color: #888;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1.5px solid #2a80b9;
        }

        .highlight {
            background: #eaf6fb;
            color: #2a80b9;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Visualisasi Data Tracer Study</h1>
        <p>Laporan Data Alumni & Status Setelah Lulus</p>
    </div>

    <div class="section">
        <h2 class="section-title">Informasi Filter</h2>
        <p>
            Tahun: <strong>{{ $filters['year'] ?? 'Semua Tahun' }}</strong> |
            Jurusan: <strong>{{ $filters['department'] ?? 'Semua Jurusan' }}</strong>
        </p>
    </div>

    <div class="section">
        <h2 class="section-title">Ringkasan Status Alumni</h2>
        <div class="summary-box">
            <div class="summary-stats">
                <div class="stat-item">
                    <p class="stat-label">Total Alumni</p>
                    <p class="stat-value">{{ $overview['total'] }}</p>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Bekerja</p>
                    <p class="stat-value">{{ $overview['working'] }}</p>
                    <p class="stat-percent">{{ $overview['workingPercentage'] }}%</p>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Kuliah</p>
                    <p class="stat-value">{{ $overview['studying'] }}</p>
                    <p class="stat-percent">{{ $overview['studyingPercentage'] }}%</p>
                </div>
                <div class="stat-item">
                    <p class="stat-label">Belum Bekerja</p>
                    <p class="stat-value">{{ $overview['unemployed'] }}</p>
                    <p class="stat-percent">{{ $overview['unemployedPercentage'] }}%</p>
                </div>
            </div>
        </div>

        <!-- Status Distribution Table -->
        <h3 style="margin-bottom:8px; color:#2a80b9;">Distribusi Status</h3>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bekerja</td>
                    <td>{{ $overview['working'] }}</td>
                    <td>{{ $overview['workingPercentage'] }}%</td>
                </tr>
                <tr>
                    <td>Kuliah</td>
                    <td>{{ $overview['studying'] }}</td>
                    <td>{{ $overview['studyingPercentage'] }}%</td>
                </tr>
                <tr>
                    <td>Belum Bekerja</td>
                    <td>{{ $overview['unemployed'] }}</td>
                    <td>{{ $overview['unemployedPercentage'] }}%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="highlight">Total</td>
                    <td class="highlight">{{ $overview['total'] }}</td>
                    <td class="highlight">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Distribusi Alumni per Jurusan</h2>
        <table>
            <thead>
                <tr>
                    <th>Jurusan</th>
                    <th>Jumlah Alumni</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($departments as $dept)
                    <tr>
                        <td>{{ is_array($dept) ? $dept['department'] : $dept->department }}</td>
                        <td>{{ is_array($dept) ? $dept['total'] : $dept->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (!empty($trends['labels']))
        <div class="section">
            <h2 class="section-title">Tren Status Alumni Per Tahun</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tahun</th>
                        <th>Bekerja</th>
                        <th>Kuliah</th>
                        <th>Belum Bekerja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trends['labels'] as $index => $year)
                        <tr>
                            <td>{{ $year }}</td>
                            <td>{{ $trends['working'][$index] }}</td>
                            <td>{{ $trends['studying'][$index] }}</td>
                            <td>{{ $trends['unemployed'][$index] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if (!empty($salary))
        <div class="section">
            <h2 class="section-title">Distribusi Rentang Gaji Alumni</h2>
            <table>
                <thead>
                    <tr>
                        <th>Rentang Gaji</th>
                        <th>Jumlah Alumni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($salary as $range => $count)
                        <tr>
                            <td>{{ $range }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if (!empty($education))
        <div class="section">
            <h2 class="section-title">Distribusi Jenjang Pendidikan</h2>
            <table>
                <thead>
                    <tr>
                        <th>Jenjang</th>
                        <th>Jumlah Alumni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($education as $level => $count)
                        <tr>
                            <td>{{ $level }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat pada {{ $generatedAt }}</p>
    </div>
</body>

</html>
