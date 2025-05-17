<?php

namespace App\Http\Controllers\BE;

use App\Http\Controllers\Controller;
use App\Models\Questionnaire;
use App\Models\QuestionnaireResponse;
use App\Models\QuestionnaireAnswer;
use App\Models\Students;
use App\Models\JobRecommendation;
use App\Services\SawRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionnaireControllerSiswa extends Controller
{
    protected $sawService;

    public function __construct(SawRecommendationService $sawService)
    {
        $this->sawService = $sawService;
    }

    /**
     * Tampilkan kuesioner yang aktif untuk siswa
     */
    public function showQuestionnaire()
    {
        // Ambil siswa yang login
        $student = Students::where('user_id', Auth::id())->first();
        if (!$student) {
            return redirect()->route('student.profile.edit')
                ->with('error', 'Profil siswa tidak ditemukan.');
        }

        // Cek apakah siswa memiliki respon kuesioner
        $response = QuestionnaireResponse::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($response && $student->has_completed_questionnaire) {
            return redirect()->route('student.recommendation.show')
                ->with('info', 'Anda sudah mengisi kuesioner. Berikut adalah rekomendasi pekerjaan untuk Anda.');
        }

        // Ambil kuesioner aktif
        $questionnaire = Questionnaire::where('is_active', true)
            ->with('questions')
            ->first();

        if (!$questionnaire) {
            return back()->with('info', 'Belum ada kuesioner aktif saat ini.');
        }

        return view('dashboard.siswa.rekomendasi.quisioner', compact('questionnaire', 'student'));
    }

    /**
     * Proses jawaban kuesioner siswa dan hitung rekomendasi dengan metode SAW
     */
    public function submitQuestionnaire(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'questionnaire_id' => 'required|exists:questionnaires,id',
                'answers' => 'required|array',
                'answers.*' => 'required',
            ]);

            $student = Students::where('user_id', Auth::id())->firstOrFail();
            $questionnaire = Questionnaire::findOrFail($request->questionnaire_id);

            DB::beginTransaction();

            // Create response
            $response = QuestionnaireResponse::create([
                'questionnaire_id' => $request->questionnaire_id,
                'student_id' => $student->id,
                'completion_date' => now(),
                'recommendation_result' => null // Will be updated after calculation
            ]);

            // Save all answers first
            foreach ($request->answers as $questionId => $answer) {
                QuestionnaireAnswer::create([
                    'questionnaire_response_id' => $response->id,
                    'questionnaire_question_id' => $questionId,
                    'answer_value' => $this->normalizeAnswerValue($questionnaire->questions->find($questionId), $answer),
                    'answer_text' => $answer,
                ]);
            }

            // Calculate recommendations
            $recommendations = $this->sawService->calculateRecommendations($response);

            if (empty($recommendations)) {
                throw new \Exception('Tidak dapat menghasilkan rekomendasi. Silahkan coba lagi.');
            }

            // Update response with recommendations
            $response->update([
                'recommendation_result' => $recommendations
            ]);

            // Mark student as completed
            $student->update(['has_completed_questionnaire' => true]);

            DB::commit();

            // Use absolute URL and ensure session data is saved
            session()->flash('success', 'Kuesioner berhasil disubmit.');
            return redirect()->route('student.recommendation.show');
        } catch (\Exception $e) {
            Log::error('Questionnaire submission error: ' . $e->getMessage());
            DB::rollBack();

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Tampilkan hasil rekomendasi pekerjaan
     */
    public function showRecommendation()
    {
        try {
            $student = Students::where('user_id', Auth::id())->firstOrFail();

            $response = QuestionnaireResponse::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$response || empty($response->recommendation_result)) {
                return redirect()->route('student.kuis')
                    ->with('error', 'Rekomendasi tidak ditemukan. Silahkan isi kuesioner terlebih dahulu.');
            }

            $recommendations = $response->recommendation_result;
            $jobIds = array_column($recommendations, 'job_id');
            $jobDetails = JobRecommendation::whereIn('id', $jobIds)->get()->keyBy('id');

            if ($jobDetails->isEmpty()) {
                throw new \Exception('Data pekerjaan tidak ditemukan.');
            }

            return view('dashboard.siswa.rekomendasi.rekomendasi', compact('student', 'response', 'recommendations', 'jobDetails'));
        } catch (\Exception $e) {
            Log::error('Show recommendation error: ' . $e->getMessage());
            return redirect()->route('student.kuis')
                ->with('error', 'Terjadi kesalahan saat menampilkan rekomendasi.');
        }
    }

    /**
     * Normalisasi nilai jawaban untuk perhitungan SAW
     */
    private function normalizeAnswerValue($question, $answerValue)
    {
        switch ($question->question_type) {
            case 'scale':
                // Use the scale value directly (1-5)
                return (float) $answerValue;

            case 'multiple_choice':
                // For multiple choice, look up the mapped value
                $options = $question->options ?? [];
                foreach ($options as $option) {
                    if (isset($option['text']) && $option['text'] == $answerValue) {
                        return isset($option['value']) ? (float) $option['value'] : 5;
                    }
                }
                return 1; // Default value

            case 'text':
                // Text answers aren't used in calculation
                return 0;

            default:
                return 0;
        }
    }
}
