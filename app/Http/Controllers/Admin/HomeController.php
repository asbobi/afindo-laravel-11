<?php

namespace App\Http\Controllers\Admin;

use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Models\Mstloket;

class HomeController extends Controller
{
    private $menu = 'Manajemen Loket';
    public function __construct()
    {
        View::share('menu', $this->menu);
        View::share('title', $this->menu);
        $this->loket = new Mstloket();
    }

    public function index(Request $request)
    {
        $columns = [
            [
                "data" => "DT_RowIndex",
                "orderable" => false,
                "searchable" => false,
                "name" => "No",
                "cetak" => true
            ],
            ["data" => "NamaLoket", "name" => "NamaLoket", "name" => "Nama Loket", "cetak" => true],
            [
                "data" => "NoLoket",
                "name" => "No Loket 1",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket 2",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket 3",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket 4",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket 5",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket 6",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket 7",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket 8",
                "cetak" => true
            ],
        ];

        $config = [
            "ajaxUrl" => url('admin/home/listdata'),
            "columns" => $columns,
            "title" => "Data Loket",
            "addButton" => url('admin/home/tambah'),
            "excelButton" => true,
            "pdfButton" => true,
            "filters" => [
                [
                    'type' => 'select',
                    'name' => 'IsAktif',
                    'id' => 'is-aktif',
                    'label' => '',
                ],
                [
                    'type' => 'daterange',
                    'name' => 'TanggalSampai',
                    'id' => 'tanggal',
                    'label' => '',
                ],
                [
                    'type' => 'text',
                    'name' => 'Cari',
                    'id' => 'cari',
                    'label' => '',
                ],
                [
                    'type' => 'text',
                    'name' => 'Cari',
                    'id' => 'cari',
                    'label' => '',
                ],
                [
                    'type' => 'text',
                    'name' => 'Cari',
                    'id' => 'cari',
                    'label' => '',
                ]
            ]
        ];

        return view('home.index', [
            'config' => $config,
            'ajaxUrl' => url('admin/home/listdata'),
            'title' => 'Data Loket'
        ]);
    }

    public function getListdata(Request $request)
    {
        $params = $request->all();
        $params['pre_datatable'] = function ($datatable) {
            return $datatable->editColumn('IsAvailable', function ($row) {
                return $row->IsAvailable == 1 ? '&#10004;' : '&#x2716;';
            })
                ->addColumn('action', function ($row) {
                    $button = '<a class="text-warning " href="' . url('manajemen-loket/create/' . my_encrypt($row->IDLoket)) . '"><i class="feather icon-edit-1"></i></a>

                    <a href="#" class="text-danger delete-btn" wire:click="$dispatch(\'confirm-delete\', { id: \'' . my_encrypt($row->IDLoket) . '\' })"><i class="feather icon-trash-2"></i></a>';
                    return $button;
                })
                ->rawColumns(['action', 'IsAvailable']);
        };
        return $this->loket->getRows($params);
    }
}
