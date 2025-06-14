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

class VisualizationController extends Controller
{
    /**
     * Display the visualization dashboard
     */
    public function index()
    {
        // Get list of departments for filter
        $departments = Jurusan::pluck('nama', 'id');
        
        // Get years (last 10 years) for filter
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 9, $currentYear);
        
        return view('dashboard.superadmin.visualizations.index', compact('departments', 'years'));
    }
      /**
     * Get visualization data for AJAX requests
     */
    public function getData(Request $request)
    {
        $type = $request->input('type', 'overview');
        $year = $request->input('year');
        $department = $request->input('department');
        $status = $request->input('status');
        
        // Get current year for trends
        $currentYear = Carbon::now()->year;
        
        // Build base query with filters
        $query = Students::query();
        
        if ($year) {
            $query->whereYear('tanggal_lulus', $year);
        }
        
        if ($department) {
            $query->where('jurusan_id', $department);
        }
        
        if ($status) {
            $query->where('status_setelah_lulus', $status);
        }
        
        // Handle different request types
        switch ($type) {
            case 'summary':
            case 'overview':
                // Get basic summary statistics
                $data = [
                    'total' => (clone $query)->count(),
                    'working' => (clone $query)->where('status_setelah_lulus', 'kerja')->count(),
                    'studying' => (clone $query)->where('status_setelah_lulus', 'kuliah')->count(),
                    'unemployed' => (clone $query)->where('status_setelah_lulus', 'belum_kerja')->count(),
                ];
                
                // Calculate percentages
                $total = $data['total'] > 0 ? $data['total'] : 1; // Avoid division by zero
                $data['workingPercentage'] = round(($data['working'] / $total) * 100, 1);
                $data['studyingPercentage'] = round(($data['studying'] / $total) * 100, 1);
                $data['unemployedPercentage'] = round(($data['unemployed'] / $total) * 100, 1);
                
                break;
                
            case 'trend':
                // Get yearly trends (last 5 years)
                $trendYears = $year ? [$year] : range($currentYear - 4, $currentYear);
                
                $data = [
                    'labels' => $trendYears,
                    'working' => [],
                    'studying' => [],
                    'unemployed' => [],
                ];
                
                foreach ($trendYears as $trendYear) {
                    $yearQuery = clone $query;
                    $yearQuery->whereYear('tanggal_lulus', $trendYear);
                    
                    $data['working'][] = (clone $yearQuery)->where('status_setelah_lulus', 'kerja')->count();
                    $data['studying'][] = (clone $yearQuery)->where('status_setelah_lulus', 'kuliah')->count();
                    $data['unemployed'][] = (clone $yearQuery)->where('status_setelah_lulus', 'belum_kerja')->count();
                }
                break;
                
            case 'departments':
                // Get statistics by department
                $departmentsQuery = DB::table('students')
                    ->join('jurusans', 'students.jurusan_id', '=', 'jurusans.id');
                    
                if ($year) {
                    $departmentsQuery->whereYear('tanggal_lulus', $year);
                }
                
                if ($status) {
                    $departmentsQuery->where('status_setelah_lulus', $status);
                }
                
                $data = $departmentsQuery->select('jurusans.nama as department', DB::raw('count(students.id) as total'))
                    ->groupBy('jurusans.nama')
                    ->orderBy('total', 'desc')
                    ->get()
                    ->toArray();
                break;
                
            case 'salary':
                // Get salary distribution
                $salaryQuery = DataKerja::join('students', 'data_kerjas.student_id', '=', 'students.id');
                
                if ($year) {
                    $salaryQuery->whereYear('students.tanggal_lulus', $year);
                }
                
                if ($department) {
                    $salaryQuery->where('students.jurusan_id', $department);
                }
                  $data = $salaryQuery->select(
                        DB::raw('CASE 
                            WHEN gaji < 2000000 THEN "< 2 juta" 
                            WHEN gaji BETWEEN 2000000 AND 4000000 THEN "2-4 juta"
                            WHEN gaji BETWEEN 4000001 AND 8000000 THEN "4-8 juta"
                            ELSE "> 8 juta"
                        END as `salary_range`'),
                        DB::raw('count(*) as count')
                    )
                    ->whereNotNull('gaji')
                    ->groupBy('salary_range')
                    ->get()
                    ->pluck('count', 'salary_range')
                    ->toArray();
                break;
                
            case 'education':
                // Get education level distribution
                $educationQuery = DataKuliah::join('students', 'data_kuliahs.student_id', '=', 'students.id');
                
                if ($year) {
                    $educationQuery->whereYear('students.tanggal_lulus', $year);
                }
                
                if ($department) {
                    $educationQuery->where('students.jurusan_id', $department);
                }
                
                $data = $educationQuery->select('jenjang', DB::raw('count(*) as count'))
                    ->groupBy('jenjang')
                    ->get()
                    ->pluck('count', 'jenjang')
                    ->toArray();
                break;
                
            case 'waitingTime':
                // Get waiting time distribution
                $waitingTimeQuery = DataKerja::join('students', 'data_kerjas.student_id', '=', 'students.id');
                
                if ($year) {
                    $waitingTimeQuery->whereYear('students.tanggal_lulus', $year);
                }
                
                if ($department) {
                    $waitingTimeQuery->where('students.jurusan_id', $department);
                }
                // Hitung waktu tunggu dalam bulan antara tanggal lulus dan tanggal_mulai
                $data = $waitingTimeQuery->select(
                        DB::raw('CASE 
                            WHEN TIMESTAMPDIFF(MONTH, students.tanggal_lulus, data_kerjas.tanggal_mulai) < 3 THEN "< 3 bulan" 
                            WHEN TIMESTAMPDIFF(MONTH, students.tanggal_lulus, data_kerjas.tanggal_mulai) BETWEEN 3 AND 6 THEN "3-6 bulan"
                            WHEN TIMESTAMPDIFF(MONTH, students.tanggal_lulus, data_kerjas.tanggal_mulai) BETWEEN 7 AND 12 THEN "6-12 bulan"
                            ELSE "> 12 bulan"
                        END as `time_range`'),
                        DB::raw('count(*) as count')
                    )
                    ->whereNotNull('data_kerjas.tanggal_mulai')
                    ->groupBy('time_range')
                    ->get()
                    ->pluck('count', 'time_range')
                    ->toArray();
                break;
                
            case 'alumni':
                // Get alumni details
                $alumniQuery = Students::select('students.*', 'jurusans.nama as jurusan')
                    ->join('jurusans', 'students.jurusan_id', '=', 'jurusans.id');
                
                if ($year) {
                    $alumniQuery->whereYear('students.tanggal_lulus', $year);
                }
                
                if ($department) {
                    $alumniQuery->where('students.jurusan_id', $department);
                }
                
                if ($status) {
                    $alumniQuery->where('students.status_setelah_lulus', $status);
                }
                
                $data = $alumniQuery->select(
                        'students.nama_lengkap', 
                        'jurusans.nama as jurusan', 
                        DB::raw('YEAR(students.tanggal_lulus) as tahun_lulus'),
                        'students.status_setelah_lulus as status'
                    )
                    ->limit(100) // Limit to prevent large responses
                    ->get()
                    ->toArray();
                break;
                
            case 'all':
                // Get all data for multiple charts at once
                $summaryData = $this->getSummaryData($query);
                $statusData = $this->getStatusData($query);
                $trendData = $this->getTrendData($query, $year, $currentYear);
                $departmentsData = $this->getDepartmentsData($query, $year, $department, $status);
                $waitingTimeData = $this->getWaitingTimeData($year, $department);
                $salaryData = $this->getSalaryData($year, $department);
                $educationData = $this->getEducationData($year, $department);
                $alumniData = $this->getAlumniData($year, $department, $status);
                
                $data = [
                    'total' => $summaryData['total'],
                    'working' => $summaryData['working'],
                    'studying' => $summaryData['studying'],
                    'unemployed' => $summaryData['unemployed'],
                    'workingPercentage' => $summaryData['workingPercentage'],
                    'studyingPercentage' => $summaryData['studyingPercentage'],
                    'unemployedPercentage' => $summaryData['unemployedPercentage'],
                    'status' => $statusData,
                    'trend' => $trendData,
                    'departments' => $departmentsData,
                    'waitingTime' => $waitingTimeData,
                    'salary' => $salaryData,
                    'education' => $educationData,
                    'alumni' => $alumniData
                ];
                break;
                
            default:
                $data = ['error' => 'Unknown visualization type'];
                break;
        }
        
        return response()->json([
            'status' => 'success',
            'type' => $type,
            'data' => $data
        ]);
    }
    
    /**
     * Get summary data for dashboard
     */
    private function getSummaryData($query)
    {
        $data = [
            'total' => (clone $query)->count(),
            'working' => (clone $query)->where('status_setelah_lulus', 'kerja')->count(),
            'studying' => (clone $query)->where('status_setelah_lulus', 'kuliah')->count(),
            'unemployed' => (clone $query)->where('status_setelah_lulus', 'belum_kerja')->count(),
        ];
        
        // Calculate percentages
        $total = $data['total'] > 0 ? $data['total'] : 1; // Avoid division by zero
        $data['workingPercentage'] = round(($data['working'] / $total) * 100, 1);
        $data['studyingPercentage'] = round(($data['studying'] / $total) * 100, 1);
        $data['unemployedPercentage'] = round(($data['unemployed'] / $total) * 100, 1);
        
        return $data;
    }
    
    /**
     * Get status data for pie chart
     */
    private function getStatusData($query)
    {
        return [
            'working' => (clone $query)->where('status_setelah_lulus', 'kerja')->count(),
            'studying' => (clone $query)->where('status_setelah_lulus', 'kuliah')->count(),
            'unemployed' => (clone $query)->where('status_setelah_lulus', 'belum_kerja')->count()
        ];
    }
    
    /**
     * Get trend data for line chart
     */
    private function getTrendData($query, $year, $currentYear)
    {
        $trendYears = $year ? [$year] : range($currentYear - 4, $currentYear);
        
        $data = [
            'labels' => $trendYears,
            'working' => [],
            'studying' => [],
            'unemployed' => []
        ];
        
        foreach ($trendYears as $trendYear) {
            $yearQuery = clone $query;
            $yearQuery->whereYear('tanggal_lulus', $trendYear);
            
            $data['working'][] = (clone $yearQuery)->where('status_setelah_lulus', 'kerja')->count();
            $data['studying'][] = (clone $yearQuery)->where('status_setelah_lulus', 'kuliah')->count();
            $data['unemployed'][] = (clone $yearQuery)->where('status_setelah_lulus', 'belum_kerja')->count();
        }
        
        return $data;
    }
    
    /**
     * Get departments data for bar chart
     */
    private function getDepartmentsData($query, $year, $department, $status)
    {
        $departmentsQuery = DB::table('students')
            ->join('jurusans', 'students.jurusan_id', '=', 'jurusans.id');
            
        if ($year) {
            $departmentsQuery->whereYear('tanggal_lulus', $year);
        }
        
        if ($status) {
            $departmentsQuery->where('status_setelah_lulus', $status);
        }
        
        return $departmentsQuery->select('jurusans.nama as department', DB::raw('count(students.id) as total'))
            ->groupBy('jurusans.nama')
            ->orderBy('total', 'desc')
            ->get()
            ->toArray();
    }
    
    /**
     * Get waiting time data for pie chart
     */    private function getWaitingTimeData($year, $department)
    {
        $waitingTimeQuery = DataKerja::join('students', 'data_kerjas.student_id', '=', 'students.id');
        
        if ($year) {
            $waitingTimeQuery->whereYear('students.tanggal_lulus', $year);
        }
        
        if ($department) {
            $waitingTimeQuery->where('students.jurusan_id', $department);
        }
        // Hitung waktu tunggu dalam bulan antara tanggal lulus dan tanggal_mulai
        return $waitingTimeQuery->select(
                DB::raw('CASE 
                    WHEN TIMESTAMPDIFF(MONTH, students.tanggal_lulus, data_kerjas.tanggal_mulai) < 3 THEN "< 3 bulan" 
                    WHEN TIMESTAMPDIFF(MONTH, students.tanggal_lulus, data_kerjas.tanggal_mulai) BETWEEN 3 AND 6 THEN "3-6 bulan"
                    WHEN TIMESTAMPDIFF(MONTH, students.tanggal_lulus, data_kerjas.tanggal_mulai) BETWEEN 7 AND 12 THEN "6-12 bulan"
                    ELSE "> 12 bulan"
                END as `time_range`'),
                DB::raw('count(*) as count')
            )
            ->whereNotNull('data_kerjas.tanggal_mulai')
            ->groupBy('time_range')
            ->get()
            ->pluck('count', 'time_range')
            ->toArray();
    }
    
    /**
     * Get salary data for doughnut chart
     */    private function getSalaryData($year, $department)
    {
        $salaryQuery = DataKerja::join('students', 'data_kerjas.student_id', '=', 'students.id');
        
        if ($year) {
            $salaryQuery->whereYear('students.tanggal_lulus', $year);
        }
        
        if ($department) {
            $salaryQuery->where('students.jurusan_id', $department);
        }
        
        return $salaryQuery->select(
                DB::raw('CASE 
                    WHEN gaji < 2000000 THEN "< 2 juta" 
                    WHEN gaji BETWEEN 2000000 AND 4000000 THEN "2-4 juta"
                    WHEN gaji BETWEEN 4000001 AND 8000000 THEN "4-8 juta"
                    ELSE "> 8 juta"
                END as `salary_range`'),
                DB::raw('count(*) as count')
            )
            ->whereNotNull('gaji')
            ->groupBy('salary_range')
            ->get()
            ->pluck('count', 'salary_range')
            ->toArray();
    }
    
    /**
     * Get education data for polarArea chart
     */
    private function getEducationData($year, $department)
    {
        $educationQuery = DataKuliah::join('students', 'data_kuliahs.student_id', '=', 'students.id');
        
        if ($year) {
            $educationQuery->whereYear('students.tanggal_lulus', $year);
        }
        
        if ($department) {
            $educationQuery->where('students.jurusan_id', $department);
        }
        
        return $educationQuery->select('jenjang', DB::raw('count(*) as count'))
            ->groupBy('jenjang')
            ->get()
            ->pluck('count', 'jenjang')
            ->toArray();
    }
    
    /**
     * Get alumni data for table
     */
    private function getAlumniData($year, $department, $status)
    {
        $alumniQuery = Students::select('students.*', 'jurusans.nama as jurusan')
            ->join('jurusans', 'students.jurusan_id', '=', 'jurusans.id');
        
        if ($year) {
            $alumniQuery->whereYear('students.tanggal_lulus', $year);
        }
        
        if ($department) {
            $alumniQuery->where('students.jurusan_id', $department);
        }
        
        if ($status) {
            $alumniQuery->where('students.status_setelah_lulus', $status);
        }
        
        return $alumniQuery->select(
                'students.nama_lengkap', 
                'jurusans.nama as jurusan', 
                DB::raw('YEAR(students.tanggal_lulus) as tahun_lulus'),
                'students.status_setelah_lulus as status'
            )
            ->limit(100) // Limit to prevent large responses
            ->get()
            ->toArray();
    }
    
    /**
     * Export visualization data to PDF
     */
    public function exportPdf(Request $request)
    {
        // Get data for all visualization types
        $overview = $this->getDataForType('overview', $request);
        $trends = $this->getDataForType('trend', $request);
        $departments = $this->getDataForType('departments', $request);
        $salary = $this->getDataForType('salary', $request);
        $education = $this->getDataForType('education', $request);
        
        // Set up PDF with all charts
        $pdf = PDF::loadView('dashboard.superadmin.visualizations.pdf', [
            'overview' => $overview,
            'trends' => $trends,
            'departments' => $departments,
            'salary' => $salary,
            'education' => $education,
            'filters' => $request->only(['year', 'department']),
            'generatedAt' => now()->format('d M Y H:i:s')
        ]);
        
        return $pdf->download('tracer-study-visualizations.pdf');
    }
    
    /**
     * Export visualization data to Excel
     */
    public function exportExcel(Request $request)
    {
        $type = $request->input('type', 'general');
        $query = Students::with(['dataKerja', 'dataKuliah']);
        if ($request->input('year')) {
            $query->whereYear('tanggal_lulus', $request->input('year'));
        }
        if ($request->input('department')) {
            $query->where('jurusan_id', $request->input('department'));
        }
        // Filter status sesuai tipe export
        if ($type === 'employment') {
            $query->where('status_setelah_lulus', 'kerja');
        } elseif ($type === 'education') {
            $query->where('status_setelah_lulus', 'kuliah');
        } elseif ($type === 'unemployment') {
            $query->where('status_setelah_lulus', 'belum_kerja');
        }
        $students = $query->get();
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\TracerStudyExport($type, $students),
            'tracer-study-visualizations.xlsx'
        );
    }
    
    /**
     * Helper method to get data for a specific visualization type
     */
    private function getDataForType($type, $request)
    {
        $response = $this->getData(new Request([
            'type' => $type,
            'year' => $request->input('year'),
            'department' => $request->input('department')
        ]));
        
        return json_decode($response->getContent(), true)['data'] ?? [];
    }
}
