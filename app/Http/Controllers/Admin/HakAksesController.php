<?php

namespace App\Http\Controllers\Admin;

use App\Models\Akseslevel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Stevebauman\Purify\Facades\Purify;

class HakAksesController extends Controller
{
    private $menu = 'Hak Akses';
    private $akseslevel;
    public function __construct()
    {
        View::share('menu', $this->menu);
        View::share('title', $this->menu);
        $this->akseslevel = new Akseslevel();
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
                "width" => "5%"
            ],
            [
                "data" => "NamaLevel",
                "name" => "Nama Level",
            ],
            [
                "class" => "text-center",
                "data" => "action",
                "name" => "#",
                "width" => "5%"
            ],

        ];

        $config = [
            "ajaxUrl" => url('admin/hak-akses/listdata'),
            "columns" => $columns,
            "title" => "Data Hak Akses Level",
        ];

        return view('admin.hak-akses.index', [
            'config' => $config,
        ]);
    }

    public function xgetListdata(Request $request)
    {
        $params = $request->all();

        $params['pre_datatable'] = function ($datatable) {
            return $datatable->addColumn('action', function ($row) {
                $button = '';
                if ($this->akses->EditData) {
                    $button .= '<a style="padding:5px;" class="text-warning" href="' . url('admin/hak-akses/create/' . my_encrypt($row->KodeLevel)) . '"><i class="feather icon-edit-1"></i></a>';
                }
                return $button;
            })
                ->setRowData([
                    'data-id' => function ($row) {
                        return my_encrypt($row->KodeLevel);
                    },
                ])
                ->rawColumns(['action']);
        };

        return $this->akseslevel->getRows($params);
    }
    public function getCreate(Request $request, string $kode = null)
    {
        $x = [];
        if (isset($kode)) {
            $kodelevel = Purify::clean(my_decrypt($kode));
            $x['data'] = $this->akseslevel->getRow([
                'where' => [
                    'KodeLevel' => $kodelevel
                ]
            ])['data'];

            $x['fitur'] = $this->akseslevel->getRows([
                'from' => 'serverfitur',
                'join' => [
                    [
                        'table' => 'fiturlevel',
                        'on' => ['serverfitur.KodeFitur', 'fiturlevel.KodeFitur'],
                        'param' => 'left'
                    ]
                ],
                'where' => [
                    'fiturlevel.KodeLevel' => $kodelevel,
                    'serverfitur.IsAktif' => 1
                ]
            ])['data'];
        }
        return view('admin.hak-akses.create', $x);
    }

    public function postStore(Request $request)
    {
        $KodeLevel = my_decrypt($request->KodeLevel);
        $insertdata = Purify::clean(request()->except(['_token']));

        $edit = true;
        if ($KodeLevel == '') {
            $edit = false;
            $KodeLevel = $this->akseslevel->createIdInt();
        }

        //data item fiturlevel
        $fiturlevel = [];
        $kodefitur = $insertdata['KodeFitur'];
        $viewdata = $insertdata['ViewData'];
        $addData = $insertdata['AddData'];
        $editData = $insertdata['EditData'];
        $deleteData = $insertdata['DeleteData'];
        $printData = $insertdata['PrintData'];
        foreach ($kodefitur as $key => $value) {
            $fiturlevel[] = [
                'KodeLevel' => $KodeLevel,
                'KodeFitur' => $value,
                'ViewData' => $viewdata[$key] ?? 0,
                'AddData' => $addData[$key] ?? 0,
                'EditData' => $editData[$key] ?? 0,
                'DeleteData' => $deleteData[$key] ?? 0,
                'PrintData' => $printData[$key] ?? 0
            ];
        }
        unset($insertdata['KodeFitur']);
        unset($insertdata['ViewData']);
        unset($insertdata['AddData']);
        unset($insertdata['EditData']);
        unset($insertdata['DeleteData']);
        unset($insertdata['PrintData']);

        if (!$edit) {
            ## tambah data
            $pesan = 'tambah data';
            $insertdata['KodeLevel'] = $KodeLevel;
            $result = $this->akseslevel->insertData($insertdata);
        } else {
            ## update data
            $pesan = 'update data';
            unset($insertdata['KodeLevel']);
            $result = $this->akseslevel->updateData($insertdata, ['KodeLevel' => $KodeLevel]);
        }

        if (count($fiturlevel) > 0) {
            $this->akseslevel->deleteData(['KodeLevel' => $KodeLevel], 'fiturlevel');
            $this->akseslevel->insertData($fiturlevel, 'fiturlevel');
        }

        if ($result !== false) {
            return response()->json(['status' => true, 'message' => "Berhasil $pesan"], 200);
        } else {
            return response()->json(['status' => false, 'message' => "Gagal $pesan"], 200);
        }
    }
}
