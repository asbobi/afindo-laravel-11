<?php

namespace App\Exports;

use App\Models\TugasKhusus;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TugasExport implements FromView
{
    public $tglAwal;
    public $tglAkhir;
    public $tugas;

    public function __construct($tglAwal, $tglAkhir)
    {
        $this->tglAwal = $tglAwal;
        $this->tglAkhir = $tglAkhir;
        $this->tugas = new TugasKhusus();
    }
    public function view(): View
    {

        $datas = $this->tugas->getRows(
            [
                'where' => [
                    ['tanggal', '>=', $this->tglAwal],
                    ['tanggal', '<=', $this->tglAkhir],
                ]
            ]
        )['data'];
        return view('exports.tugaskhusus', ['datas' => $datas]);
    }
}
