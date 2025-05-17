<?php

namespace App\Services;

use App\Models\QuestionnaireResponse;
use App\Models\JobRecommendation;
use Illuminate\Support\Facades\Log;

class SawRecommendationService
{
    public function calculateRecommendations(QuestionnaireResponse $response)
    {
        $answers = $response->answers()->with('question')->get();
        $jobs = JobRecommendation::all();

        if ($jobs->isEmpty()) {
            return [];
        }

        // Get student answers and weights
        $criteria = [];
        foreach ($answers as $answer) {
            $question = $answer->question;
            if (in_array($question->question_type, ['scale', 'multiple_choice'])) {
                $criteria[$question->id] = [
                    'value' => $answer->answer_value,
                    'weight' => $question->weight ?? 1,
                    'type' => $question->criteria_type ?? 'benefit'
                ];
            }
        }

        $jobScores = [];
        foreach ($jobs as $job) {
            $totalScore = 0;
            $maxScore = 0;

            foreach ($criteria as $id => $criterion) {
                $jobValue = $job->criteria_values[$id] ?? 0;
                $studentValue = $criterion['value'];
                $weight = $criterion['weight'];

                // Calculate similarity score (0-1)
                $similarity = 1 - (abs($studentValue - $jobValue) / 5);
                $similarityScore = max(0, min(1, $similarity));

                $totalScore += $similarityScore * $weight;
                $maxScore += $weight;
            }

            // Calculate final normalized score
            $finalScore = $maxScore > 0 ? ($totalScore / $maxScore) : 0;

            $jobScores[] = [
                'job_id' => $job->id,
                'name' => $job->name,
                'score' => round($finalScore, 4),
                'match_percentage' => round($finalScore * 100, 1),
                'salary' => $job->average_salary,
                'description' => $job->description,
                'skills_needed' => $job->skills_needed,
                'industry_type' => $job->industry_type
            ];
        }

        // Sort by match percentage in descending order
        usort($jobScores, function ($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });

        // Return top 5 matches
        return array_slice($jobScores, 0, 5);
    }
}
