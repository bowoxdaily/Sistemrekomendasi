<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;
    
    protected $fillable = ['kode', 'nama', 'deskripsi'];

    // Relasi ke tabel students
    public function students()
    {
        return $this->hasMany(Students::class);
    }
}
