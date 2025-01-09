<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\MyModel;

class Mstlayanan extends MyModel
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mstlayanan';

    protected $fillable = [
        "IDLayanan",
        "NamaLayanan",
        "IsAktif"
    ];
}
