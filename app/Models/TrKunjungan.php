<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\MyModel;

class TrKunjungan extends MyModel
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'trkunjungan';

    protected $fillable = [
        "IDKunjungan", "TanggalJam", "JamDilayani", "NoAntrian", "StatusAntrian", "IDLoket", "UserName", "IDUser", "NilaiSPM", "IDLayanan"
    ];
}
