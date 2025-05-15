<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'question_text',
        'question_type', // 'multiple_choice', 'scale', 'text'
        'options',       // JSON field untuk pilihan jawaban
        'weight',        // Bobot untuk perhitungan SAW
        'criteria_type'  // 'benefit' atau 'cost' untuk perhitungan SAW
    ];

    protected $casts = [
        'options' => 'array',
        'weight' => 'float'
    ];

    // Relasi dengan kuesioner
    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    // Relasi dengan jawaban
    public function answers()
    {
        return $this->hasMany(QuestionnaireAnswer::class);
    }
}
