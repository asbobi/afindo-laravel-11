<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\MyModel;

class Kontenweb extends MyModel
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'kontenweb';

    protected $fillable = [
        'IDKonten',
        'JudulKonten',
        'TglKonten',
        'TglEntry',
        'Author',
        'IsiKonten',
        'IsAktif',
        'UserName',
        'JenisKonten',
    ];
}
