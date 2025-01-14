<?php

namespace App\Http\Controllers\Admin;

use App\Models\TugasKhusus;
use App\Exports\TugasExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TugasController extends Controller
{
    private $menu = 'Lap. Tugas';
    private $tugas;
    public function __construct()
    {
        View::share('menu', $this->menu);
        View::share('title', $this->menu);
        $this->tugas = new TugasKhusus();
        $this->akses = $this->getAkses();
    }

    public function getIndex(Request $request)
    {
        $columns = [
            [
                "data" => "DT_RowIndex",
                "orderable" => false,
                "searchable" => false,
                "name" => "No",
                "cetak" => true
            ],
            [
                "data" => "kodepegawai",
                "name" => "Kode Peg.",
                "cetak" => true
            ],
            [
                "data" => "tanggal",
                "name" => "Tanggal",
                "cetak" => true
            ],
            [
                "data" => "jamdatang",
                "name" => "Jam Datang",
                "cetak" => true
            ],
            [
                "data" => "jampulang",
                "name" => "Jam Pulang",
                "cetak" => true
            ],
        ];

        $buttons = [];
        ## opsional tambahkan pengecekan akses print data
        if ($this->akses->PrintData) {
            $printButton = [
                [
                    "type" => "pdf",
                    'url' => url('admin/tugas/print'), // jika url diisi maka buatkan custom view pdf
                ],
                [
                    "type" => "excel",
                    'url' => url('admin/tugas/export'), // jika url diisi maka buatkan custom view excel
                ],
            ];
            $buttons = array_merge($buttons, $printButton);
        }

        $config = [
            ## digunakan untuk menghilangkan pagination, biasanya untuk format laporan
            "paginate" => false,
            "ajaxUrl" => url('admin/tugas/listdata'),
            "columns" => $columns,
            "buttons" => $buttons,
            "title" => "Data Tugas",
            "filters" => [
                [
                    'type' => 'daterange',
                    'id' => 'tanggal',
                    'label' => '',
                ],
            ]
        ];

        return view('admin.tugas.index', [
            'config' => $config,
            'ajaxUrl' => url('admin/tugas/listdata'),
            'title' => 'Data Tugas'
        ]);
    }

    public function xgetListdata(Request $request)
    {
        $params = $request->all();

        ## param disini harus disamakan dengan id filters pada fungsi getIndex
        $tgl = $params['tanggal'] ?? '';
        unset($params['tanggal']);
        if ($tgl != '') {
            $tgl = explode(" - ", $tgl);
            $tglawal = date('Y-m-d', strtotime($tgl[0]));
            $tglakhir = date('Y-m-d', strtotime($tgl[1]));
        } else {
            $tglawal = date("Y-m-d", strtotime('-1 month'));
            $tglakhir = date('Y-m-d');
        }

        $params['where'] = [
            ['tanggal', '>=', $tglawal],
            ['tanggal', '<=', $tglakhir],
        ];

        $params['select'] = [
            'kodepegawai',
            'tanggal',
            'jamdatang',
            'jampulang',
        ];

        return $this->tugas->getRows($params);
    }

    public function getExport(Request $request)
    {
        $params = $request->all();
        $tgl = $params['tanggal'] ?? '';
        unset($params['tanggal']);
        if ($tgl != '') {
            $tgl = explode(" - ", $tgl);
            $tglawal = date('Y-m-d', strtotime($tgl[0]));
            $tglakhir = date('Y-m-d', strtotime($tgl[1]));
        } else {
            $tglawal = date("Y-m-d", strtotime('-1 month'));
            $tglakhir = date('Y-m-d');
        }
        return Excel::download(new TugasExport($tglawal, $tglakhir), 'tugas.xlsx');
    }

    public function getPrint(Request $request)
    {
        $params = $request->all();
        $tgl = $params['tanggal'] ?? '';
        unset($params['tanggal']);
        if ($tgl != '') {
            $tgl = explode(" - ", $tgl);
            $tglawal = date('Y-m-d', strtotime($tgl[0]));
            $tglakhir = date('Y-m-d', strtotime($tgl[1]));
        } else {
            $tglawal = date("Y-m-d", strtotime('-1 month'));
            $tglakhir = date('Y-m-d');
        }
        $datas = $this->tugas->getRows(
            [
                'where' => [
                    ['tanggal', '>=', $tglawal],
                    ['tanggal', '<=', $tglakhir],
                ]
            ]
        )['data'];
        $pdf = Pdf::loadView('pdf.tugaskhusus', ['datas' => $datas]);
        return $pdf->stream('tugas.pdf', array("Attachment" => false));
    }
}
