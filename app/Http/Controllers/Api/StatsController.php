<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DataKerja;
use App\Models\Students;
use App\Models\DataKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class StatsController extends Controller
{
    public function getStudentStats()
    {
        // Existing student stats logic...
        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => Students::count(),
                'percentage_change' => 5.2, // This should be calculated dynamically
            ]
        ]);
    }
    
    public function getTracerStats(Request $request)
    {
        try {
            // Log the beginning of the function for debugging
            Log::info('Starting tracer stats calculation');
            
            // Get request parameters
            $type = $request->input('type', 'overview');
            $year = $request->input('year');
            $department = $request->input('department');
            $status = $request->input('status');
            
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
            
            // If the request type is 'all', delegate to VisualizationController
            if ($type === 'all') {
                $visualizationController = new \App\Http\Controllers\BE\VisualizationController();
                return $visualizationController->getData($request);
            }
            
            // Get basic summary stats
            $totalStudents = (clone $query)->count();
            Log::info("Total students: $totalStudents");
            
            $working = (clone $query)->where('status_setelah_lulus', 'kerja')->count();
            $studying = (clone $query)->where('status_setelah_lulus', 'kuliah')->count();
            $unemployed = (clone $query)->where('status_setelah_lulus', 'belum_kerja')->count();
            
            Log::info("Status counts - Working: $working, Studying: $studying, Unemployed: $unemployed");
            
            // Get job types - Query from DataKerja model instead
            $jobTypes = DataKerja::select('jenis_pekerjaan', DB::raw('count(*) as total'))
                ->whereNotNull('jenis_pekerjaan')
                ->groupBy('jenis_pekerjaan')
                ->pluck('total', 'jenis_pekerjaan')
                ->toArray();
            
            // Get education majors
            $eduMajors = DataKuliah::select('jurusan', DB::raw('count(*) as total'))
                ->groupBy('jurusan')
                ->pluck('total', 'jurusan')
                ->toArray();
            
            // Get salary ranges - Update to use DataKerja model
            $salaryRanges = [
                'Kurang dari 3 juta' => DataKerja::whereNotNull('gaji')
                    ->where('gaji', '<', 3000000)
                    ->count(),
                '3-5 juta' => DataKerja::whereNotNull('gaji')
                    ->whereBetween('gaji', [3000000, 5000000])
                    ->count(),
                '5-10 juta' => DataKerja::whereNotNull('gaji')
                    ->whereBetween('gaji', [5000000, 10000000])
                    ->count(),
                'Lebih dari 10 juta' => DataKerja::whereNotNull('gaji')
                    ->where('gaji', '>', 10000000)
                    ->count(),
            ];
            
            // Get job match statistics - Update to use DataKerja model
            $jobMatch = [
                'match' => DataKerja::where('sesuai_jurusan', 'ya')->count(),
                'notMatch' => DataKerja::where('sesuai_jurusan', 'tidak')->count(),
            ];
            
            // Get 5-year trend data
            $currentYear = date('Y');
            $years = [];
            $workingTrend = [];
            $studyingTrend = [];
            $unemployedTrend = [];
            
            for ($i = 4; $i >= 0; $i--) {
                $year = $currentYear - $i;
                $years[] = $year;
                
                // Make sure we're using the correct column name for graduation date
                $columnName = 'tahun_lulus'; // Default to the most common column name
                
                if (Schema::hasColumn('students', 'tanggal_lulus')) {
                    $columnName = 'tanggal_lulus';
                } elseif (Schema::hasColumn('students', 'tahun_lulus')) {
                    $columnName = 'tahun_lulus';
                }
                
                // Log the column name we're using
                Log::info("Using column for graduation date: $columnName");
                
                // For trend data, filter by the year component of the date
                $workingTrend[] = Students::where('status_setelah_lulus', 'kerja')
                    ->whereRaw("YEAR($columnName) = ?", [$year])
                    ->count();
                    
                $studyingTrend[] = Students::where('status_setelah_lulus', 'kuliah')
                    ->whereRaw("YEAR($columnName) = ?", [$year])
                    ->count();
                    
                $unemployedTrend[] = Students::where('status_setelah_lulus', 'belum_kerja')
                    ->whereRaw("YEAR($columnName) = ?", [$year])
                    ->count();
            }
            
            // Log trend data
            Log::info("Trend data - Years: " . json_encode($years));
            Log::info("Trend data - Working: " . json_encode($workingTrend));
            Log::info("Trend data - Studying: " . json_encode($studyingTrend));
            Log::info("Trend data - Unemployed: " . json_encode($unemployedTrend));
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'summary' => [
                        'total' => $totalStudents,
                        'working' => $working,
                        'study' => $studying,
                        'unemployed' => $unemployed,
                    ],
                    'details' => [
                        'jobTypes' => $jobTypes,
                        'eduMajors' => $eduMajors,
                        'salaryRanges' => $salaryRanges,
                        'jobMatch' => $jobMatch,
                    ],
                    'trends' => [
                        'years' => $years,
                        'working' => $workingTrend,
                        'study' => $studyingTrend,
                        'unemployed' => $unemployedTrend,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTracerStats: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve tracer stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getDashboardStats()
    {
        try {
            // Get counts for the dashboard
            $operatorCount = \App\Models\Operators::count();
            $studentCount = \App\Models\Students::count();
            $alumniCount = \App\Models\Students::where('status_lulus', 'lulus')->count();
            
            // Calculate percentages
            $operatorPercentage = 0;
            $studentPercentage = 0;
            $alumniPercentage = 0;
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'operator' => [
                        'count' => $operatorCount,
                        'percentage' => $operatorPercentage
                    ],
                    'student' => [
                        'count' => $studentCount,
                        'percentage' => $studentPercentage
                    ],
                    'alumni' => [
                        'count' => $alumniCount,
                        'percentage' => $alumniPercentage
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDashboardStats: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
