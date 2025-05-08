<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBelumKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'alasan',
        'keterampilan',
        'minat',
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }
}
