<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'requirements',
        'skills_needed',
        'average_salary',
        'industry_type',
        'criteria_values' // JSON field untuk menyimpan nilai kriteria untuk perhitungan SAW
    ];

    protected $casts = [
        'criteria_values' => 'array',
        'requirements' => 'array',
        'skills_needed' => 'array'
    ];

    // Relasi dengan rekomendasi hasil kuesioner
    public function questionnaireResults()
    {
        return $this->belongsToMany(QuestionnaireResponse::class, 'job_recommendation_results')
            ->withPivot('score', 'rank')
            ->withTimestamps();
    }
}
