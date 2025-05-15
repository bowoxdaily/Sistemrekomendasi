<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireAnswer extends Model
{
    use HasFactory;
    protected $fillable = [
        'questionnaire_response_id',
        'questionnaire_question_id',
        'answer_value',  // Nilai jawaban (untuk perhitungan SAW)
        'answer_text'    // Teks jawaban (untuk jawaban pilihan ganda atau teks)
    ];

    // Relasi dengan respons kuesioner
    public function response()
    {
        return $this->belongsTo(QuestionnaireResponse::class, 'questionnaire_response_id');
    }

    // Relasi dengan pertanyaan kuesioner
    public function question()
    {
        return $this->belongsTo(QuestionnaireQuestion::class, 'questionnaire_question_id');
    }
}
