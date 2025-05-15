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

        // Jika siswa sudah mengisi kuesioner, tampilkan hasil rekomendasinya
        if ($student->has_completed_questionnaire) {
            $response = QuestionnaireResponse::where('student_id', $student->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($response) {
                return redirect()->route('student.recommendation.show')
                    ->with('info', 'Anda sudah mengisi kuesioner. Berikut adalah rekomendasi pekerjaan untuk Anda.');
            }
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
        // Validasi input
        $request->validate([
            'questionnaire_id' => 'required|exists:questionnaires,id',
            'answers' => 'required|array',
            'answers.*' => 'required',
        ]);

        // Ambil data siswa
        $student = Students::where('user_id', Auth::id())->first();
        if (!$student) {
            return redirect()->route('student.profile.edit')
                ->with('error', 'Profil siswa tidak ditemukan.');
        }

        // Ambil kuesioner dan pertanyaannya
        $questionnaire = Questionnaire::findOrFail($request->questionnaire_id);
        $questions = $questionnaire->questions;

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Buat respons kuesioner baru
            $response = QuestionnaireResponse::create([
                'questionnaire_id' => $questionnaire->id,
                'student_id' => $student->id,
                'completion_date' => now(),
                'recommendation_result' => null, // Akan diisi setelah perhitungan SAW
            ]);

            // Simpan jawaban untuk setiap pertanyaan
            foreach ($request->answers as $questionId => $answerValue) {
                $question = $questions->where('id', $questionId)->first();

                if (!$question) {
                    continue;
                }

                // Konversi nilai jawaban sesuai dengan tipe pertanyaan
                $normalizedValue = $this->normalizeAnswerValue($question, $answerValue);

                QuestionnaireAnswer::create([
                    'questionnaire_response_id' => $response->id,
                    'questionnaire_question_id' => $questionId,
                    'answer_value' => $normalizedValue,
                    'answer_text' => $answerValue,
                ]);
            }

            // Hitung rekomendasi pekerjaan menggunakan metode SAW
            $recommendationResults = $this->sawService->calculateRecommendations($response);

            // Update respons dengan hasil rekomendasi
            $response->update([
                'recommendation_result' => $recommendationResults
            ]);

            // Update status siswa
            $student->update([
                'has_completed_questionnaire' => true
            ]);

            DB::commit();

            return redirect()->route('student.recommendation.show')
                ->with('success', 'Terima kasih! Kuesioner berhasil diisi. Berikut adalah rekomendasi pekerjaan untuk Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Tampilkan hasil rekomendasi pekerjaan
     */
    public function showRecommendation()
    {
        // Ambil siswa yang login
        $student = Students::where('user_id', Auth::id())->first();
        if (!$student || !$student->has_completed_questionnaire) {
            return redirect()->route('student.kuis')
                ->with('info', 'Anda belum mengisi kuesioner.');
        }

        // Ambil respons kuesioner terbaru
        $response = QuestionnaireResponse::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$response) {
            return redirect()->route('student.kuis')
                ->with('info', 'Rekomendasi tidak ditemukan. Silahkan isi kuesioner terlebih dahulu.');
        }

        // Ambil 5 rekomendasi pekerjaan dengan nilai tertinggi
        $recommendations = $response->recommendation_result ?? [];

        // Ambil detail pekerjaan
        $jobDetails = [];
        if (!empty($recommendations)) {
            $jobIds = array_column($recommendations, 'job_id');
            $jobDetails = JobRecommendation::whereIn('id', $jobIds)->get()
                ->keyBy('id');
        }

        return view('dashboard.siswa.rekomendasi.rekomendasi', compact('student', 'response', 'recommendations', 'jobDetails'));
    }

    /**
     * Normalisasi nilai jawaban untuk perhitungan SAW
     */
    private function normalizeAnswerValue($question, $answerValue)
    {
        switch ($question->question_type) {
            case 'scale':
                // Skala 1-5 atau 1-10, digunakan langsung
                return (float) $answerValue;

            case 'multiple_choice':
                // Untuk pilihan ganda, nilai harus dipetakan
                $options = $question->options ?? [];
                foreach ($options as $index => $option) {
                    if (isset($option['text']) && $option['text'] == $answerValue) {
                        return isset($option['value']) ? (float) $option['value'] : $index + 1;
                    }
                }
                return 1; // Nilai default

            case 'text':
                // Untuk jawaban teks, tidak digunakan dalam perhitungan SAW
                return 0;

            default:
                return 0;
        }
    }
}
