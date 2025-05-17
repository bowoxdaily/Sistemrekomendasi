<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\JobRecommendation;
use App\Models\Questionnaire;
use App\Models\QuestionnaireQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return view('dashboard.operator.kuisioner.edit', compact('questionnaire', 'jobs'));
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
        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,scale,text',
            'options' => 'required_if:question_type,multiple_choice|array',
            'weight' => 'required|numeric|min:0|max:1',
            'criteria_type' => 'required|in:benefit,cost',
        ]);

        try {
            $question = new QuestionnaireQuestion([
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'options' => $request->options,
                'weight' => $request->weight,
                'criteria_type' => $request->criteria_type,
            ]);

            $questionnaire->questions()->save($question);

            return response()->json([
                'success' => true,
                'message' => 'Pertanyaan berhasil ditambahkan',
                'data' => $question
            ]);
        } catch (\Exception $e) {
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
