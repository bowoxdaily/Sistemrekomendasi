<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'nomor_telepon',
        'foto',
        'is_profile_complete',
        // Add other fields as needed
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if student profile is complete
     */
    public function isProfileComplete()
    {
        // Define required fields for a complete profile
        $requiredFields = [
            'nama_lengkap',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
            'nomor_telepon'
        ];

        // Check if all required fields are filled
        foreach ($requiredFields as $field) {
            if (empty($this->{$field})) {
                return false;
            }
        }

        return true;
    }
}
