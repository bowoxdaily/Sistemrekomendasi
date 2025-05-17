<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\JobRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobRecommendataionController extends Controller
{
    public function index()
    {
        $jobs = JobRecommendation::orderBy('created_at', 'desc')->get();
        return view('dashboard.operator.jobs.index', compact('jobs'));
    }

    public function getdata(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $search = $request->input('search', '');
        $industry = $request->input('industry', '');

        $query = JobRecommendation::query();

        // Add search functionality
        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('industry_type', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Add industry filter
        if (!empty($industry)) {
            $query->where('industry_type', $industry);
        }

        // Get paginated results
        $jobs = $query->paginate($perPage);

        // Get distinct industry types for the filter dropdown
        $industries = JobRecommendation::distinct()->pluck('industry_type')->toArray();

        // Return results with industries for the filter
        return response()->json([
            'data' => $jobs->items(),
            'total' => $jobs->total(),
            'per_page' => $jobs->perPage(),
            'current_page' => $jobs->currentPage(),
            'last_page' => $jobs->lastPage(),
            'from' => $jobs->firstItem(),
            'to' => $jobs->lastItem(),
            'industries' => $industries
        ]);
    }
    public function getById(Request $request, $id)
    {
        // Find the job recommendation by ID
        $job = JobRecommendation::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job recommendation not found'
            ], 404);
        }

        // Get distinct industry types for the filter dropdown
        $industries = JobRecommendation::distinct()->pluck('industry_type')->toArray();

        // Return results with the same structure as getData
        return response()->json([
            'data' => [$job],  // Wrap in array to match getData format
            'total' => 1,
            'per_page' => 1,
            'current_page' => 1,
            'last_page' => 1,
            'from' => 1,
            'to' => 1,
            'industries' => $industries
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|array',
            'requirements.*' => 'required|string',
            'skills_needed' => 'required|array',
            'skills_needed.*' => 'required|string',
            'average_salary' => 'required|numeric|min:0',
            'industry_type' => 'required|string|max:255',
            'criteria_values' => 'required|array',
            'criteria_values.*' => 'required|numeric|min:1|max:5',
        ]);

        try {
            DB::beginTransaction();

            // Pastikan data yang akan disimpan sudah sesuai
            $job = new JobRecommendation();
            $job->name = $request->name;
            $job->description = $request->description;
            $job->requirements = $request->requirements;
            $job->skills_needed = $request->skills_needed;
            $job->average_salary = $request->average_salary;
            $job->industry_type = $request->industry_type;
            $job->criteria_values = $request->criteria_values;
            $job->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pekerjaan berhasil ditambahkan',
                'data' => $job
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $job = JobRecommendation::find($id);

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Pekerjaan tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|array',
            'requirements.*' => 'required|string',
            'skills_needed' => 'required|array',
            'skills_needed.*' => 'required|string',
            'average_salary' => 'required|numeric|min:0',
            'industry_type' => 'required|string|max:255',
            'criteria_values' => 'required|array',
            'criteria_values.*' => 'required|numeric|min:1|max:5',
        ]);

        try {
            DB::beginTransaction();

            $job->name = $request->name;
            $job->description = $request->description;
            $job->requirements = $request->requirements;
            $job->skills_needed = $request->skills_needed;
            $job->average_salary = $request->average_salary;
            $job->industry_type = $request->industry_type;
            $job->criteria_values = $request->criteria_values;
            $job->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pekerjaan berhasil diperbarui',
                'data' => $job
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $job = JobRecommendation::findOrFail($id);
            $job->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pekerjaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
