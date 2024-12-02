<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\MyModel;
use Illuminate\Support\Facades\DB;

class Akseslevel extends MyModel
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'akseslevel';
    protected $fillable = [
        "KodeLevel",
        "NamaLevel",
        "IsAktif"
    ]; 

    public function get_fitur($kodelevel = '')
    {
        $data = DB::table('fiturlevel')
        ->selectRaw('fiturlevel.KodeFitur, fiturlevel.ViewData, serverfitur.NamaFitur, serverfitur.KelompokFitur, serverfitur.Icon, serverfitur.Url, serverfitur.Slug')
        ->leftJoin('serverfitur', function ($join) {
            $join->on('serverfitur.KodeFitur', '=', 'fiturlevel.KodeFitur');
        })->where('fiturlevel.KodeLevel', $kodelevel)
        ->where('fiturlevel.ViewData', 1)
        ->where('serverfitur.IsAktif', 1)
        ->orderBy('fiturlevel.KodeFitur');
        return $data->get();
    }

    public function get_kode()
    {
        $data = self::select("KodeLevel AS kode")->orderByRaw("KodeLevel DESC")->first();
        if ($data) {
            $kode = $data->kode;
        } else {
            $kode =  0;
        }
        return ($kode + 1);
    }
}
