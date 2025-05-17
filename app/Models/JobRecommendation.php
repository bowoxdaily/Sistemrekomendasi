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
        'criteria_values'
    ];

    protected $casts = [
        'requirements' => 'array',
        'skills_needed' => 'array',
        'criteria_values' => 'array'
    ];

    public function getFormattedSalaryAttribute()
    {
        return 'Rp ' . number_format($this->average_salary, 0, ',', '.');
    }

    public function getCriteriaValuesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setCriteriaValuesAttribute($value)
    {
        $this->attributes['criteria_values'] = json_encode($value);
    }
}
