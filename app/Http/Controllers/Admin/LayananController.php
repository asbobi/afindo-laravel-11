<?php

namespace App\Http\Controllers\Admin;

use App\Models\Mstlayanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    private $menu = 'Manajemen Layanan';
    private $layanan;

    public function __construct()
    {
        View::share('menu', $this->menu);
        View::share('title', $this->menu);
        $this->layanan = new Mstlayanan();
        $this->middleware('auth');
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
                "data" => "NamaLayanan",
                "name" => "NamaLayanan",
                "cetak" => true
            ],
            [
                "data" => "IsAktif",
                "name" => "IsAktif",
                "cetak" => true
            ],
            [
                "data" => "action",
                "name" => "#",
                "cetak" => false,
                "delete" => true
            ],
        ];

        $config = [
            "ajaxUrl" => url('admin/layanan/listdata'),
            "columns" => $columns,
            "title" => "Data Layanan",
            "deleteButton" => [
                'status' => true,
                'param' => ['id', 'no'],
                'url' => url('admin/layanan/delete')
            ],
            "addButton" => url('admin/layanan/create'),
            "excelButton" => true,
            "pdfButton" => true,
            "filters" => [
                [
                    'type' => 'text',
                    'id' => 'cari',
                    'label' => '',
                ],
            ]
        ];

        return view('admin.layanan.index', [
            'config' => $config,
            'title' => 'Data Layanan'
        ]);
    }

    public function xgetListdata(Request $request)
    {
        $params = $request->all();

        $cari = $params['cari'] ?? '';
        unset($params['cari']);
        if ($cari != '') {
            $params['orLike'] = [
                ['NamaLayanan', $cari],
            ];
        }

        $params['where'] = [
            ['IsAktif', '=', 1],
        ];

        $params['pre_datatable'] = function ($datatable) {
            return $datatable->editColumn('IsAvailable', function ($row) {
                return $row->IsAvailable == 1 ? '&#10004;' : '&#x2716;';
            })->addColumn('action', function ($row) {
                $button = '<a style="padding:5px;" class="text-warning" href="' . url('admin/layanan/create/' . my_encrypt($row->IDLayanan)) . '"><i class="feather icon-edit-1"></i></a>

                    ';
                return $button;
            })
                ->setRowData([
                    'data-id' => function ($row) {
                        return my_encrypt($row->IDLayanan);
                    },
                ])
                ->rawColumns(['action', 'IsAvailable']);
        };

        return $this->layanan->getRows($params);
    }

    public function getCreate(Request $request, string $kode = null)
    {
        $x = [];
        if (isset($kode)) {
            $IDLayanan = Purify::clean(my_decrypt($kode));
            $x['data'] = $this->layanan->getRow([
                'where' => [
                    'IDLayanan' => $IDLayanan
                ]
            ])['data'];

            $item_detail = $this->layanan->getRows([
                'from' => 'itemlayanan',
                'where' => [
                    'IDLayanan' => $IDLayanan
                ]
            ])['data'];
        }

        //bisa dari array biasa atau dari DB dengan query 
        $jenisItem = collect([ 
            [
                'id' => '1',
                'text' => 'Item 1',
                'jenis' => '1',
                'deskripsi' => 'Deskripsi Item 1',
            ],
            [
                'id' => '2',
                'text' => 'Item 2',
                'jenis' => '2',
                'deskripsi' => 'Deskripsi Item 2',
            ]
        ]);


        $config = [
            'label' => 'Item Narasi',
            'items' => $item_detail ?? [],
            'columns' => [
                [
                    'header' => 'Narasi Item',
                    'data' => 'Narasi',
                    'editable' => false
                ],
                [
                    'header' => 'Jenis Item',
                    'data' => 'JenisItem',
                    'editable' => false,
                    'type' => 'select',
                    'options' => $jenisItem->pluck('id', 'text')->toArray()
                ]
            ]
        ];
        $x['table_input_config'] = $config;
        return view('admin.layanan.create', $x);
    }

    public function postStore(Request $request)
    {
        $IDLayanan = my_decrypt($request->IDLayanan);
        $insertdata = Purify::clean(request()->except(['_token']));

        $edit = true;
        if ($IDLayanan == '') {
            $edit = false;
            $IDLayanan = $this->layanan->createId('LYN', 'IDLayanan');
        }

        $insertdata['IsAktif'] = $request->has('IsAktif') ? (int) $request->IsAktif : 0;

        //data item layanan
        $itemLayanan = [];
        $narasi = $insertdata['Narasi'];
        $jenisitem = $insertdata['JenisItem'];
        foreach ($narasi as $key => $value) {
            if($value == '') continue;
            $itemLayanan[] = [
                'NoUrut' => $key + 1,
                'IDLayanan' => $IDLayanan,
                'Narasi' => $value,
                'JenisItem' => $jenisitem[$key],
                'IsAktif' => 1
            ];
        }
        unset($insertdata['Narasi']);
        unset($insertdata['JenisItem']);

        if (!$edit) {
            ## tambah data
            $pesan = 'tambah data';
            $insertdata['IDLayanan'] = $IDLayanan;
            $result = $this->layanan->insertData($insertdata);
        } else {
            ## update data
            $pesan = 'update data';
            unset($insertdata['IDLayanan']);
            $result = $this->layanan->updateData($insertdata, ['IDLayanan' => $IDLayanan]);
        }

        if (count($itemLayanan)) {
            $this->layanan->deleteData(['IDLayanan' => $IDLayanan], 'itemlayanan');
            $this->layanan->insertData($itemLayanan, 'itemlayanan');
        }

        if ($result !== false) {
            return response()->json(['status' => true, 'message' => "Berhasil $pesan"], 200);
        } else {
            return response()->json(['status' => false, 'message' => "Gagal $pesan"], 200);
        }
    }

    public function postDelete(Request $request)
    {
        try {
            $deleteParams = Purify::clean($request->all());

            ## tuliskan param yang ada pada deleteButton
            $idLayanan = isset($deleteParams['id']) ? my_decrypt($deleteParams['id']) : null;

            if (!$idLayanan) {
                return response()->json([
                    'status' => false,
                    'message' => 'IDLayanan tidak ditemukan.'
                ]);
            }

            $item = $this->layanan::where(['IDLayanan' => $idLayanan])->first();
            if ($item) {
                $this->layanan->updateData(['IsAktif' => 0], ['IDLayanan' => $idLayanan]);
                return response()->json([
                    'status' => true,
                    'message' => 'Item berhasil dihapus.'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Item tidak ditemukan.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
