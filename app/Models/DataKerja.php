<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKerja extends Model
{
    use HasFactory;

    protected $table = 'data_kerjas';
    
    protected $fillable = [
        'student_id', // Make sure this is 'student_id' not 'students_id'
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
        return $this->belongsTo(Students::class, 'student_id'); // Use 'student_id' here too
    }
}
