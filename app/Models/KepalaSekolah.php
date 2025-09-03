<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KepalaSekolah extends Model
{
    use HasFactory;

    protected $table = 'kepala_sekolahs';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'jenis_kelamin',
        'alamat',
        'nip',
        'jabatan',
    ];

    /**
     * Relasi dengan User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
