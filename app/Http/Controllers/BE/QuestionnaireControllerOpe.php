<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\JobRecommendation;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionnaireControllerOpe extends Controller
{
    public function index()
    {
        $questionnaires = Questionnaire::where('created_by', Auth::id())
            ->orWhere('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.operator.kuisioner.show', compact('questionnaires'));
    }

    public function getQuestionnaires()
    {
        $questionnaires = Questionnaire::where('created_by', Auth::id())
            ->orWhere('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $questionnaires
        ]);
    }

    /**
     * Tampilkan form untuk membuat kuesioner baru
     */

    /**
     * Simpan kuesioner baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'boolean',
        ]);

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Buat kuesioner baru
            $questionnaire = Questionnaire::create([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
                'created_by' => Auth::id(),
            ]);

            // Jika kuesioner ini diaktifkan, nonaktifkan kuesioner lain
            if ($request->has('is_active')) {
                Questionnaire::where('id', '!=', $questionnaire->id)
                    ->update(['is_active' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kuesioner berhasil dibuat',
                'data' => $questionnaire
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tampilkan form untuk mengedit kuesioner
     */
    public function edit(Questionnaire $questionnaire)
    {
        // Load pertanyaan terkait
        $questionnaire->load('questions');

        // Dapatkan daftar pekerjaan untuk rekomendasi
        $jobs = JobRecommendation::all();
        
        // Extract unique criteria types from job recommendations
        $criteriaTypes = [];
        foreach ($jobs as $job) {
            if (isset($job->criteria_values) && is_array($job->criteria_values)) {
                foreach ($job->criteria_values as $key => $value) {
                    $criteriaTypes[$key] = ucfirst($key);
                }
            }
        }
        
        // If no criteria found in jobs, use default ones
        if (empty($criteriaTypes)) {
            $criteriaTypes = [
                'education' => 'Pendidikan (Education)',
                'experience' => 'Pengalaman (Experience)',
                'technical' => 'Teknis (Technical)'
            ];
        }

        return view('dashboard.operator.kuisioner.edit', compact('questionnaire', 'jobs', 'criteriaTypes'));
    }

    /**
     * Update kuesioner
     */
    public function update(Request $request, Questionnaire $questionnaire)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'is_active' => 'required|in:true,false,0,1', // Terima berbagai format boolean
        ]);

        DB::beginTransaction();
        try {
            // Convert to boolean properly
            $isActive = filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN);

            // Update kuesioner
            $questionnaire->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $isActive,
            ]);

            // If this questionnaire is being activated, deactivate others
            if ($isActive) {
                Questionnaire::where('id', '!=', $questionnaire->id)
                    ->update(['is_active' => false]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kuesioner berhasil diperbarui' . ($isActive ? ' dan diaktifkan' : ''),
                'data' => $questionnaire->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Hapus kuesioner
     */
    public function destroy(Questionnaire $questionnaire)
    {
        try {
            // Periksa apakah kuesioner sudah memiliki respons
            if ($questionnaire->responses()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kuesioner tidak dapat dihapus karena sudah memiliki respons dari siswa.'
                ]);
            }

            $questionnaire->questions()->delete();
            $questionnaire->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kuesioner berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tambah pertanyaan ke kuesioner
     */
    public function addQuestion(Request $request, Questionnaire $questionnaire)
    {
        // Get valid criteria types from job recommendations
        $jobs = JobRecommendation::all();
        $validCriteriaTypes = ['education', 'experience', 'technical']; // Default fallback
        
        foreach ($jobs as $job) {
            if (isset($job->criteria_values) && is_array($job->criteria_values)) {
                $validCriteriaTypes = array_merge($validCriteriaTypes, array_keys($job->criteria_values));
            }
        }
        $validCriteriaTypes = array_unique($validCriteriaTypes);
        
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,scale',
            'weight' => 'required|numeric|min:1|max:5',
            'criteria_type' => 'required|in:' . implode(',', $validCriteriaTypes),
            'options' => 'required_if:question_type,multiple_choice|array',
        ]);

        try {
            Log::info('Request data for add question:', ['data' => $request->all()]);
            
            $options = [];
            if ($request->question_type === 'multiple_choice') {
                // Process options
                $rawOptions = $request->input('options');
                
                if (is_array($rawOptions)) {
                    foreach ($rawOptions as $option) {
                        if (is_array($option) && isset($option['text'])) {
                            // Make sure we have a valid value
                            $value = isset($option['value']) ? (int) $option['value'] : 1;
                            
                            // Make sure value is between 1-5
                            $value = max(1, min(5, $value));
                            
                            $options[] = [
                                'text' => trim($option['text']),
                                'value' => $value
                            ];
                        }
                    }
                }
                
                // Log processed options for debugging
                Log::info('Processed options:', ['options' => $options]);
                
                if (empty($options)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pertanyaan pilihan ganda harus memiliki minimal satu opsi'
                    ], 422);
                }
            }

            $question = new QuestionnaireQuestion([
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'options' => $options,
                'weight' => $request->weight,
                'criteria_type' => $request->criteria_type,
            ]);

            $questionnaire->questions()->save($question);
            
            Log::info('Saved question:', [
                'id' => $question->id,
                'type' => $question->question_type,
                'options' => $question->options
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan',
                'data' => $question
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding question: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus pertanyaan dari kuesioner
     */
    public function removeQuestion(QuestionnaireQuestion $question)
    {
        $questionnaire = $question->questionnaire;

        try {
            // Periksa apakah pertanyaan sudah memiliki jawaban
            if ($question->answers()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pertanyaan tidak dapat dihapus karena sudah memiliki jawaban dari siswa.'
                ]);
            }

            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lihat hasil kuesioner
     */
    public function results(Questionnaire $questionnaire)
    {
        $questionnaire->load(['responses.student', 'responses.answers']);

        return response()->json([
            'success' => true,
            'data' => $questionnaire
        ]);
    }
}
