<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\JobRecommendation;
use Illuminate\Http\Request;

class JobRecommendataionController extends Controller
{
    public function index()
    {
        $jobs = JobRecommendation::orderBy('created_at', 'desc')->get();
        return view('dashboard.operator.jobs.index', compact('jobs'));
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
            JobRecommendation::create($request->all());
            return response()->json([
                'message' => 'Pekerjaan berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, JobRecommendation $job)
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
            $job->update($request->all());
            return response()->json([
                'message' => 'Pekerjaan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(JobRecommendation $job)
    {
        try {
            $job->delete();
            return response()->json([
                'message' => 'Pekerjaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
