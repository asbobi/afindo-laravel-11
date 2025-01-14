<?php

namespace App\Models;

use App\MyModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TugasKhusus extends MyModel
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tugaskhusus';

    protected $fillable = [
        "kodepegawai",
        "jamdatang",
        "jampulang",
        "tanggal",
        "updated"
    ];
}
