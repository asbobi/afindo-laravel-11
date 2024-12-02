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
                "name" => "DT_RowIndex",
                "orderable" => false,
                "searchable" => false,
                "name" => "No",
            ],
            ["data" => "NamaLoket", "name" => "NamaLoket", "name" => "Nama Loket"],
            ["data" => "NoLoket", "name" => "NoLoket", "name" => "No Loket"],
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
                    'label' => 'Is Aktif',
                ],
                [
                    'type' => 'daterange',
                    'name' => 'TanggalSampai',
                    'id' => 'tanggal',
                    'label' => 'Is Aktif',
                ]
            ]
        ];

        return view('home.index', [
            'config' => $config,
            'ajaxUrl' => url('admin/home/listdata'),
            'title' => 'Data Loket',
            'addButton' => true
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
