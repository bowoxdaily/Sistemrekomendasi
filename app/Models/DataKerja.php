<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'nama_perusahaan',
        'posisi',
        'jenis_pekerjaan',
        'tanggal_mulai',
        'gaji',
        'sesuai_jurusan',
        'kompetensi_dibutuhkan',
    ];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }
}
