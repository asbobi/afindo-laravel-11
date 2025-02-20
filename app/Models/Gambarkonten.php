<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\MyModel;

class Gambarkonten extends MyModel
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'gambarkonten';

    protected $fillable = [
        'NoUrut',
        'IDKonten',
        'FileGambar',
        'IsAktif',
        'Keterangan',
    ];
}
