<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'is_active',
        'created_by',
    ];

    // Relasi dengan pembuat kuesioner (operator)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi dengan pertanyaan kuesioner
    public function questions()
    {
        return $this->hasMany(QuestionnaireQuestion::class);
    }

    // Relasi dengan siswa yang telah mengisi kuesioner
    public function responses()
    {
        return $this->hasMany(QuestionnaireResponse::class);
    }
}
