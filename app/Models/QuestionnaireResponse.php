<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'student_id',
        'completion_date',
        'recommendation_result', // JSON field untuk menyimpan hasil rekomendasi SAW
    ];

    protected $casts = [
        'completion_date' => 'datetime',
        'recommendation_result' => 'array'
    ];

    // Relasi dengan kuesioner
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    // Relasi dengan siswa
    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    // Relasi dengan jawaban detail
    public function answers()
    {
        return $this->hasMany(QuestionnaireAnswer::class);
    }
    
    /**
     * Check if this response has recommendations
     * 
     * @return bool
     */
    public function hasRecommendations()
    {
        return !empty($this->recommendation_result);
    }
    
    /**
     * Get the recommendations with job details
     * 
     * @return array
     */
    public function getFormattedRecommendations()
    {
        if (!$this->hasRecommendations()) {
            return [];
        }
        
        $recommendations = $this->recommendation_result;
        $jobIds = array_column($recommendations, 'job_id');
        $jobs = JobRecommendation::whereIn('id', $jobIds)->get()->keyBy('id');
        
        $formattedRecommendations = [];
        
        foreach ($recommendations as $recommendation) {
            $jobId = $recommendation['job_id'];
            if (isset($jobs[$jobId])) {
                $formattedRecommendations[] = [
                    'job' => $jobs[$jobId],
                    'match_percentage' => $recommendation['match_percentage'] ?? 0,
                    'criteria_scores' => $recommendation['criteria_scores'] ?? []
                ];
            }
        }
        
        return $formattedRecommendations;
    }
    
    /**
     * Get the top N recommendations
     * 
     * @param int $limit Number of recommendations to return
     * @return array
     */
    public function getTopRecommendations($limit = 3)
    {
        $recommendations = $this->getFormattedRecommendations();
        
        // Sort by match percentage in descending order
        usort($recommendations, function($a, $b) {
            return $b['match_percentage'] <=> $a['match_percentage'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Get a formatted recommendation badge for display
     * 
     * @param bool $includeIcon Whether to include an icon in the badge
     * @return string HTML badge element
     */
    public function getRecommendationBadge($includeIcon = true)
    {
        if (!$this->hasRecommendations()) {
            return '<span class="badge bg-warning text-dark">' . 
                   ($includeIcon ? '<i class="fas fa-clock mr-1"></i> ' : '') . 
                   'Belum Ada Rekomendasi</span>';
        }
        
        $recommendations = $this->getFormattedRecommendations();
        $count = count($recommendations);
        
        if ($count === 0) {
            return '<span class="badge bg-secondary">' . 
                   ($includeIcon ? '<i class="fas fa-exclamation-circle mr-1"></i> ' : '') . 
                   'Data Tidak Lengkap</span>';
        }
        
        // Get highest match percentage
        $topMatch = 0;
        foreach ($recommendations as $rec) {
            if (($rec['match_percentage'] ?? 0) > $topMatch) {
                $topMatch = $rec['match_percentage'];
            }
        }
        
        // Choose color based on highest match percentage
        $badgeClass = 'primary';
        $icon = 'star';
        
        if ($topMatch >= 80) {
            $badgeClass = 'success';
            $icon = 'check-circle';
        } elseif ($topMatch >= 60) {
            $badgeClass = 'info';
            $icon = 'thumbs-up';
        } elseif ($topMatch >= 40) {
            $badgeClass = 'primary';
            $icon = 'star';
        } else {
            $badgeClass = 'secondary';
            $icon = 'star-half';
        }
        
        return '<span class="badge bg-' . $badgeClass . '">' .
               ($includeIcon ? '<i class="fas fa-' . $icon . ' mr-1"></i> ' : '') .
               $count . ' Rekomendasi' . ($topMatch > 0 ? ' (' . $topMatch . '%)' : '') . '</span>';
    }
}
