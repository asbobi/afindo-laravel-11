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
        $this->akses = $this->getAkses();
    }

    public function getIndex(Request $request)
    {
        $columns = [
            ## untuk menambahkan checkbox pada datatable maka buatlah data dengan name => "checkbox" dan tambahkan idcheckbox pada value datatable
            [
                "data" => null,
                "orderable" => false,
                "searchable" => false,
                "name" => "checkbox",
                "width" => "5%"
            ],
            [
                "data" => "DT_RowIndex",
                "orderable" => false,
                "searchable" => false,
                "name" => "No",
                "class" => "text-center",
                "cetak" => true,
                "width" => "5%"
            ],
            [
                "data" => "NamaLayanan",
                "name" => "NamaLayanan",
                "cetak" => true
            ],
            [
                "orderable" => false,
                "class" => "text-center",
                "data" => "action",
                "name" => "#",
                "cetak" => false,
                "delete" => true,
                "width" => "5%"
            ],
        ];

        $buttons = [];
        ## opsional tambahkan pengecekan akses add data
        if ($this->akses->AddData) {
            $buttons[] = [
                "type" => "add",
                "label" => "Tambah Data",
                "url" => url('admin/layanan/create'),
            ];
        }

        ## opsional tambahkan pengecekan akses delete data
        if ($this->akses->DeleteData) {
            $buttons[] = [
                "type" => "delete",
                'param' => ['id', 'no'],
                'url' => url('admin/layanan/delete'),
            ];
        }

        ## opsional tambahkan pengecekan akses print data
        if ($this->akses->PrintData) {
            $printButton = [
                [
                    "type" => "pdf",
                    "label" => "Cetak Pdf",
                    'url' => "", // jika url kosong maka default print pdf datatable
                ],
                [
                    "type" => "excel",
                    "label" => "Export Xlsx",
                    'url' => "", // jika url kosong maka default export excel datatable
                ],
                [
                    "type" => "import",
                    "label" => "Import Xlsx",
                    'url' => url('admin/layanan/create'), // contoh url form import
                ]
            ];
            $buttons = array_merge($buttons, $printButton);
        }

        ## opsional tambahkan pengecekan akses edit data
        if ($this->akses->EditData) {
            $buttons[] = [
                "type" => "action",
                "label" => "Aksi",
                ## tambahkan options jika ingin menampilkan button dropdowns
                'options' => [
                    [
                        'label' => 'contoh aksi get',
                        'url' => url('admin/layanan/aksi'),
                        ## tambakan method jika ingin menggunakan opsi checkbox pada datatable
                        'method' => 'get'
                    ],
                    [
                        'label' => 'contoh aksi post',
                        'url' => url('admin/layanan/aksi'),
                        ## tambakan method jika ingin menggunakan opsi checkbox pada datatable
                        'method' => 'post'
                    ]
                ],
                'url' => '',
                'method' => 'get'
            ];
        }

        $config = [
            "ajaxUrl" => url('admin/layanan/listdata'),
            "columns" => $columns,
            "buttons" => $buttons,
            "title" => "Data Layanan",
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
            return $datatable->addColumn('action', function ($row) {
                $button = '';
                if ($this->akses->EditData) {
                    $button .= '<a style="padding:5px;" class="text-warning" href="' . url('admin/layanan/create/' . my_encrypt($row->IDLayanan)) . '"><i class="feather icon-edit-1"></i></a>';
                }
                return $button;
            })
                /* ->addColumn('idcheckbox', function ($row) {
                    return my_encrypt($row->IDLayanan);
                }) */

                ## jika menambahkan checkbox pada datatable maka wajib menyertakan kolom "idcheckbox" sebagai id ketika checkbox di klik
                ->setRowAttr([
                    'data-id' => function ($row) {
                        return my_encrypt_aday($row->IDLayanan);
                        //return ($row->IDLayanan);
                    },
                ])->rawColumns(['action']);
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
                    'editable' => true,
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

    public function getAksi(Request $request)
    {
        ## batasi akses pengguna
        if (!$this->akses->EditData) {
            abort(403, 'Unauthorized access');
        }
        $insertdata = Purify::clean(request()->all());
        foreach ($insertdata['ids'] as $key) {
            $data = my_decrypt_aday($key);
            echo $data . '<br>';
        }
    }

    public function postAksi(Request $request)
    {
        ## batasi akses pengguna
        if (!$this->akses->EditData) {
            abort(403, 'Unauthorized access');
        }
        $insertdata = Purify::clean(request()->except(['_token']));
        foreach ($insertdata['ids'] as $key) {
            $data = my_decrypt_aday($key);
        }
        return response()->json(['status' => true, 'message' => "Berhasil kirim data."], 200);
    }
}
