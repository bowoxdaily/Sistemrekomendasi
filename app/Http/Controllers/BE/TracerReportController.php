<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Students;
use App\Models\Jurusan;
use App\Models\DataKerja;
use App\Models\DataKuliah;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TracerStudyExport;

class TracerReportController extends Controller
{
    /**
     * Display the reports dashboard
     */
    public function index()
    {
        // Summary statistics
        $summary = [
            'total' => Students::count(),
            'working' => Students::where('status_setelah_lulus', 'kerja')->count(),
            'studying' => Students::where('status_setelah_lulus', 'kuliah')->count(),
            'unemployed' => Students::where('status_setelah_lulus', 'belum_kerja')->count(),
        ];

        // Get yearly trends (last 5 years)
        $currentYear = Carbon::now()->year;
        $yearRange = range($currentYear - 4, $currentYear);

        $trends = [
            'labels' => $yearRange,
            'working' => [],
            'studying' => [],
            'unemployed' => [],
        ];

        foreach ($yearRange as $year) {
            // Get statistics for each year based on graduation date's year
            $trends['working'][] = Students::where('status_setelah_lulus', 'kerja')
                ->whereYear('tanggal_lulus', $year)
                ->count();

            $trends['studying'][] = Students::where('status_setelah_lulus', 'kuliah')
                ->whereYear('tanggal_lulus', $year)
                ->count();

            $trends['unemployed'][] = Students::where('status_setelah_lulus', 'belum_kerja')
                ->whereYear('tanggal_lulus', $year)
                ->count();
        }

        // Get department/major statistics from Students model directly
        $departments = DB::table('students')
            ->join('jurusans', 'students.jurusan_id', '=', 'jurusans.id')
            ->select('jurusans.nama as jurusan', DB::raw('count(students.id) as total'))
            ->groupBy('jurusans.nama')
            ->orderBy('total', 'desc')
            ->get();

        // Get waiting time statistics (average time to get a job)
        $waitingTime = DataKerja::select(
            DB::raw('TIMESTAMPDIFF(MONTH, students.tanggal_lulus, data_kerjas.tanggal_mulai) as waiting_months'),
            DB::raw('count(*) as count')
        )
            ->join('students', 'data_kerjas.student_id', '=', 'students.id')
            ->whereNotNull('students.tanggal_lulus')
            ->whereNotNull('data_kerjas.tanggal_mulai') // This already checks for non-null tanggal_mulai
            ->groupBy('waiting_months')
            ->orderBy('waiting_months')
            ->get()
            ->groupBy(function ($item) {
                // Group by ranges: 0-3 months, 3-6 months, 6-12 months, >12 months
                if ($item->waiting_months <= 3) return '0-3 bulan';
                if ($item->waiting_months <= 6) return '3-6 bulan';
                if ($item->waiting_months <= 12) return '6-12 bulan';
                return '> 12 bulan';
            })
            ->map(function ($group) {
                return $group->sum('count');
            });

        // Salary ranges for employed graduates
        $salaryRanges = DataKerja::select(
            DB::raw('CASE 
                    WHEN gaji <= 3000000 THEN "< 3 juta" 
                    WHEN gaji <= 5000000 THEN "3-5 juta"
                    WHEN gaji <= 10000000 THEN "5-10 juta"
                    ELSE "> 10 juta"
                END as salary_range'),  // Changed column alias to avoid reserved keyword
            DB::raw('count(*) as count')
        )
            ->whereNotNull('gaji')
            ->groupBy('salary_range')  // Use the new column alias
            ->get()
            ->pluck('count', 'salary_range')  // Update pluck with new column name
            ->toArray();

        // Education level distribution for those pursuing higher education
        $educationLevels = DataKuliah::select('jenjang', DB::raw('count(*) as count'))
            ->groupBy('jenjang')
            ->get()
            ->pluck('count', 'jenjang')
            ->toArray();

        return view('dashboard.operator.reports.index', compact(
            'summary',
            'trends',
            'departments',
            'waitingTime',
            'salaryRanges',
            'educationLevels'
        ));
    }

    /**
     * Generate detailed report based on filters
     */
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer',
            'status' => 'nullable|in:kerja,kuliah,belum_kerja',
            'department' => 'nullable|string',
            'report_type' => 'required|in:employment,education,unemployment,general',
            'format' => 'required|in:web,pdf,excel',
        ]);

        // Build query based on filters
        $query = Students::query();

        if ($request->filled('year')) {
            $query->whereYear('tanggal_lulus', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status_setelah_lulus', $request->status);
        }

        if ($request->filled('department')) {
            // Look up the jurusan_id using the department name
            $jurusan = Jurusan::where('nama', $request->department)->first();
            if ($jurusan) {
                $query->where('jurusan_id', $jurusan->id);
            }
        }

        // Get students based on filters
        $students = $query->get();

        // Get additional data based on report type
        $reportData = [];

        switch ($request->report_type) {
            case 'employment':
                $studentIds = $students->where('status_setelah_lulus', 'kerja')->pluck('id');
                $reportData = DataKerja::whereIn('student_id', $studentIds)->get();
                break;

            case 'education':
                $studentIds = $students->where('status_setelah_lulus', 'kuliah')->pluck('id');
                $reportData = DataKuliah::whereIn('student_id', $studentIds)->get();
                break;

            case 'unemployment':
                // For unemployment, we'll just use the base student data
                $reportData = $students->where('status_setelah_lulus', 'belum_kerja');
                break;

            case 'general':
            default:
                // For general report, we'll use all student data
                $reportData = $students;
                break;
        }

        // Generate report based on format
        switch ($request->format) {
            case 'pdf':
                return $this->generatePdfReport($request->report_type, $students, $reportData);

            case 'excel':
                return $this->generateExcelReport($request->report_type, $students, $reportData);

            case 'web':
            default:
                return view('dashboard.operator.reports.detail', [
                    'reportType' => $request->report_type,
                    'students' => $students,
                    'reportData' => $reportData,
                    'filters' => $request->only(['year', 'status', 'department']),
                ]);
        }
    }

    /**
     * Generate PDF report
     */
    private function generatePdfReport($reportType, $students, $reportData)
    {
        $pdf = PDF::loadView('dashboard.operator.reports.pdf', [
            'reportType' => $reportType,
            'students' => $students,
            'reportData' => $reportData,
            'generatedAt' => now()->format('d M Y H:i:s'),
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');

        // Generate filename with timestamp
        $timestamp = now()->format('Y-m-d_H-i-s');
        $reportTypeName = ucfirst(str_replace('_', '-', $reportType));
        $filename = "Tracer-Study-{$reportTypeName}-{$timestamp}.pdf";

        return $pdf->download($filename);
    }

    /**
     * Generate Excel report
     */
    private function generateExcelReport($reportType, $students, $reportData)
    {
        // Generate filename with timestamp
        $timestamp = now()->format('Y-m-d_H-i-s');
        $reportTypeName = ucfirst(str_replace('_', '-', $reportType));
        $filename = "Tracer-Study-{$reportTypeName}-{$timestamp}.xlsx";

        return Excel::download(
            new TracerStudyExport($reportType, $students, $reportData),
            $filename
        );
    }

    /**
     * Export all raw data
     */
    public function exportRawData(Request $request)
    {
        $format = $request->input('format', 'excel');
        $timestamp = now()->format('Y-m-d_H-i-s');

        if ($format === 'excel') {
            $filename = "Tracer-Study-Data-Mentah-{$timestamp}.xlsx";
            return Excel::download(new TracerStudyExport('raw'), $filename);
        } else {
            // Generate PDF with all raw data
            $students = Students::with(['dataKerja', 'dataKuliah'])->get();

            $pdf = PDF::loadView('dashboard.operator.reports.raw-pdf', [
                'students' => $students,
                'generatedAt' => now()->format('d M Y H:i:s'),
            ]);

            // Set paper size and orientation
            $pdf->setPaper('A4', 'landscape'); // Landscape for more columns

            $filename = "Tracer-Study-Data-Mentah-{$timestamp}.pdf";
            return $pdf->download($filename);
        }
    }

    /**
     * Get data for API requests (used by AJAX)
     */
    public function getReportData(Request $request)
    {
        // Similar logic as the index method, but return JSON
        // This can be used for dynamic chart updates

        $reportType = $request->input('type', 'summary');
        $year = $request->input('year');
        $department = $request->input('department');

        $data = [];

        switch ($reportType) {
            case 'summary':
                // Create a base query for filters
                $baseQuery = Students::query();

                if ($year) {
                    $baseQuery->whereYear('tanggal_lulus', $year);
                }

                if ($department) {
                    // Look up the jurusan_id using the department name
                    $jurusan = Jurusan::where('nama', $department)->first();
                    if ($jurusan) {
                        $baseQuery->where('jurusan_id', $jurusan->id);
                    }
                }

                // Use clone to create separate query instances for each count
                // This prevents conditions from stacking and affecting each other
                $data = [
                    'total' => (clone $baseQuery)->count(),
                    'working' => (clone $baseQuery)->where('status_setelah_lulus', 'kerja')->count(),
                    'studying' => (clone $baseQuery)->where('status_setelah_lulus', 'kuliah')->count(),
                    'unemployed' => (clone $baseQuery)->where('status_setelah_lulus', 'belum_kerja')->count(),
                ];
                break;

            // Add more case handlers for other report types

            default:
                $data = ['error' => 'Unknown report type'];
                break;
        }

        return response()->json($data);
    }
}
