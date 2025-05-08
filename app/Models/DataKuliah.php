<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataKuliah extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'nama_pt', 'jurusan', 'jenjang', 'tahun_masuk', 'status_beasiswa', 'nama_beasiswa', 'prestasi_akademik'];

    public function student()
    {
        return $this->belongsTo(Students::class);
    }
}
