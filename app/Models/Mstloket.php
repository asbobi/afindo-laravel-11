<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\MyModel;

class Mstloket extends MyModel
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mstloket';

    protected $fillable = [
        "IDLoket",
        "NamaLoket",
        "NoLoket",
        "FileAudio",
        "IsAktif",
        "IsAvailable",
        "UserName"
    ];
}
