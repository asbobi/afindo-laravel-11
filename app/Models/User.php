<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'mstuserlogin';
    protected $primaryKey = 'UserName'; // or null
    public $timestamps = false;

    public $incrementing = false;
    
    protected $keyType = 'string';

    protected $fillable = [
        'UserName',
        'NamaLengkap',
        'NoHP',
        'IsAktif',
        'KodeLevel',
        'IsPetugasLoket',
        'IsAdmin'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'Password',
        //'remember_token',
    ];
    public function getAuthPassword()
    {
        return $this->Password;
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
}
