<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'no_telp',
        'role',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
    public function superAdmin()
    {
        return $this->hasOne(SuperAdmin::class);
    }
    public function student()
    {
        return $this->hasOne(Students::class);
    }
    public function operator()
    {
        return $this->hasOne(Operators::class);
    }
    public function DataBelumKerja()
    {
        return $this->hasOne(DataBelumKerja::class);
    }
    public function DataKerja()
    {
        return $this->hasOne(DataKerja::class);
    }
    public function Datakuliah()
    {
        return $this->hasOne(DataKuliah::class);
    }
}
