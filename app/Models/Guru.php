<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nip',
        'jabatan',
        'jenis_kelamin',
        'alamat',
        // Add other fields as needed
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
