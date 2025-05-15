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
}
