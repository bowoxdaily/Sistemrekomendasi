<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operators extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'jenis_kelamin',
        'alamat',
        'nip',
        'jabatan',

        // Add other fields as needed
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
