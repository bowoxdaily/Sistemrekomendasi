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
use Illuminate\Support\Facades\Schema;

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
    public function showQuestionnaire(Request $request)
    {
        // Ambil siswa yang login
        $student = Students::where('user_id', Auth::id())->first();
        if (!$student) {
            return redirect()->route('student.profile.edit')
                ->with('error', 'Profil siswa tidak ditemukan.');
        }

        // Check if student status is eligible for questionnaire (only belum_kerja)
        if ($student->status_setelah_lulus !== 'belum_kerja') {
            return redirect()->route('student.dashboard')
                ->with('info', 'Maaf, kuesioner hanya tersedia untuk alumni yang belum bekerja.');
        }

        // Cek apakah siswa memiliki respon kuesioner
        $response = QuestionnaireResponse::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Check if student has completed questionnaire and not requesting to retake
        if ($response && $student->has_completed_questionnaire && !$request->has('retake')) {
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

        // If retaking, pass this information to view
        $isRetake = $request->has('retake') && $student->has_completed_questionnaire;

        return view('dashboard.siswa.rekomendasi.quisioner', compact('questionnaire', 'student', 'isRetake'));
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
                'is_retake' => 'boolean',
            ]);

            $student = Students::where('user_id', Auth::id())->firstOrFail();
            $questionnaire = Questionnaire::findOrFail($request->questionnaire_id);
            $isRetake = $request->boolean('is_retake', false);

            DB::beginTransaction();

            // If this is a retake, handle previous responses
            if ($isRetake && $student->has_completed_questionnaire) {
                try {
                    // Check if archived column exists before trying to use it
                    if (Schema::hasColumn('questionnaire_responses', 'archived')) {
                        // Mark previous responses as archived
                        QuestionnaireResponse::where('student_id', $student->id)
                            ->update(['archived' => true]);
                    } else {
                        // If column doesn't exist, just log it - we'll proceed anyway
                        Log::warning("The 'archived' column does not exist in questionnaire_responses table. Run the migration first.");
                    }

                    // Log that user is retaking the questionnaire
                    Log::info("Student ID {$student->id} is retaking the questionnaire");
                } catch (\Exception $e) {
                    // Just log the exception and continue - don't let this stop the process
                    Log::error("Error updating archived status: " . $e->getMessage());
                }
            }
            // If not retake, but user already completed questionnaire, show error
            else if (!$isRetake && $student->has_completed_questionnaire) {
                return back()->with('error', 'Anda sudah pernah mengisi kuesioner ini. Silakan gunakan fitur "Ambil Ulang Kuesioner" untuk mengisi ulang.');
            }

            // Create response with properties we know exist
            $responseData = [
                'questionnaire_id' => $request->questionnaire_id,
                'student_id' => $student->id,
                'completion_date' => now(),
                'recommendation_result' => null, // Will be updated after calculation
            ];

            // Only add archived field if the column exists
            if (Schema::hasColumn('questionnaire_responses', 'archived')) {
                $responseData['archived'] = false;
            }

            $response = QuestionnaireResponse::create($responseData);

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

            // Mark student as completed questionnaire and profile as complete
            Log::info('Marking student ID ' . $student->id . ' as completed questionnaire and profile complete');
            $updateResult = $student->update([
                'has_completed_questionnaire' => true,
                'is_profile_complete' => true
            ]);

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

            // Get top 3 for main display
            $topRecommendations = array_slice($recommendations, 0, 3);

            // Get all job IDs for both main and alternative recommendations
            $jobIds = array_column($recommendations, 'job_id');
            $jobDetails = JobRecommendation::whereIn('id', $jobIds)->get()->keyBy('id');

            if ($jobDetails->isEmpty()) {
                throw new \Exception('Data pekerjaan tidak ditemukan.');
            }

            // Pass both main recommendations and all recommendations for alternatives
            return view('dashboard.siswa.rekomendasi.rekomendasi', compact(
                'student',
                'response',
                'recommendations', // All recommendations for dropdown
                'jobDetails'
            ));
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
        if (!$question) {
            return 0;
        }

        switch ($question->question_type) {
            case 'scale':
                // Use the scale value directly (1-5)
                return min(5, max(1, (float) $answerValue));

            case 'multiple_choice':
                // For multiple choice, find the corresponding value from options
                if (!is_array($question->options)) {
                    return 1; // Default if no options defined
                }

                foreach ($question->options as $option) {
                    // Match by text (what the user selected)
                    if (is_array($option) && isset($option['text']) && $option['text'] == $answerValue) {
                        return isset($option['value']) ? min(5, max(1, (float)$option['value'])) : 3;
                    }
                }

                // If no match found (shouldn't happen), return middle value
                return 3;

            case 'text':
                // Text answers aren't directly used in calculations
                return 0;

            default:
                return 0;
        }
    }
}
