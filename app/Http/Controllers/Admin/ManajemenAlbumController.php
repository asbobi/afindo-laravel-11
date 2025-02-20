<?php

namespace App\Http\Controllers\Admin;

use App\Models\Kontenweb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Storage;

class ManajemenAlbumController extends Controller
{
    private $menu = 'Manajemen Album';
    private $album;
    public function __construct()
    {
        View::share('menu', $this->menu);
        View::share('title', $this->menu);
        $this->album = new Kontenweb();
        $this->akses = $this->getAkses();
    }

    public function getIndex(Request $request)
    {
        $config = $this->tableConfig();

        return view('admin.manajemen-album.index', [
            'config' => $config,
            'title' => 'List Album'
        ]);
    }

    private function tableConfig()
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
                "data" => "JudulKonten",
                "name" => "Judul Album",
                "cetak" => true
            ],
            [
                "data" => "TglEntry",
                "name" => "Tanggal Dibuat",
                "cetak" => true
            ],
            [
                "data" => "JumlahFoto",
                "name" => "Jumlah Foto",
                "cetak" => true
            ],
            [
                "data" => "Author",
                "name" => "Pembuat",
                "cetak" => true
            ],
            [
                "data" => "action",
                "name" => "#",
                "cetak" => false,
                "delete" => true
            ],
        ];

        $buttons = [];
        ## opsional tambahkan pengecekan akses add data
        if ($this->akses->AddData) {
            $buttons[] = [
                "type" => "add",
                "url" => url('admin/manajemen-album/create'),
            ];
        }

        ## opsional tambahkan pengecekan akses delete data
        if ($this->akses->DeleteData) {
            $buttons[] = [
                "type" => "delete",
                'param' => ['id', 'no'],
                'url' => url('admin/manajemen-album/delete'),
            ];
        }

        ## opsional tambahkan pengecekan akses print data
        if ($this->akses->PrintData) {
            $printButton = [
                [
                    "type" => "pdf",
                    'url' => "", // jika url kosong maka default print pdf datatable
                ],
                [
                    "type" => "excel",
                    'url' => "", // jika url kosong maka default export excel datatable
                ],
                [
                    "type" => "import",
                    'url' => url('admin/manajemen-album/create'), // contoh url form import
                ]
            ];
            $buttons = array_merge($buttons, $printButton);
        }

        $filters = [
            [
                'type' => 'daterange',
                'id' => 'tanggal',
                'label' => '',
            ],
            [
                'type' => 'text',
                'id' => 'cari',
                'label' => '',
            ],
        ];

        $config = [
            "ajaxUrl" => url('admin/manajemen-album/listdata'),
            "title" => "Data album",
            "columns" => $columns,
            "buttons" => $buttons,
            "filters" => $filters
        ];

        return $config;
    }

    public function xgetListdata(Request $request)
    {
        $params = $request->all();

        $cari = $params['cari'] ?? '';
        unset($params['cari']);
        if ($cari != '') {
            $params['orLike'] = [
                ['JudulKonten', $cari],
                ['TglEntry', $cari],
                ['Username', $cari],
            ];
        }

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
            [DB::raw('DATE(TglEntry)'), '>=', $tglawal],
            [DB::raw('DATE(TglEntry)'), '<=', $tglakhir],
        ];

        $sql = [
            'select' => ['kontenweb.*', DB::raw('COUNT(gambarkonten.NoUrut) as JumlahFoto')],
            'join' => [
                [
                    'table' => 'gambarkonten',
                    'on' => ['gambarkonten.IDKonten', 'kontenweb.IDKonten'],
                    'param' => 'left'
                ]
            ],
            'where' => [
                ['kontenweb.JenisKonten' => 'ALBUM'],
                ['kontenweb.IsAktif' => 1],
            ],
            'groupBy' => 'kontenweb.IDKonten',
        ];

        $params = array_merge_recursive($params, $sql);

        $params['pre_datatable'] = function ($datatable) {
            return $datatable
                ->addColumn('action', function ($row) {
                    $button = $this->akses->EditData ? '<a style="padding:5px;" class="text-warning" href="' . url('admin/manajemen-album/create/' . my_encrypt($row->IDKonten)) . '"><i class="feather icon-edit-1"></i></a>
                ' : '';
                    return $button;
                })
                ## tambahkan atribut data sesuai param yang digunakan pada deleteButton
                ->setRowData([
                    'data-id' => function ($row) {
                        return my_encrypt($row->IDKonten);
                    },
                    'data-no' => function ($row) {
                        return my_encrypt($row->Noalbum);
                    },
                ])
                ->rawColumns(['action', 'IsAvailable']);
        };

        return $this->album->getRows($params);
    }

    public function getCreate(Request $request, $kode = null)
    {
        $x = [];
        if (isset($kode)) {
            ## batasi akses pengguna
            if (!$this->akses->EditData) {
                abort(403, 'Unauthorized access');
            }
            $IDKonten = Purify::clean(my_decrypt($kode));
            $x['data'] = $this->album->getRow([
                'where' => [
                    'IDKonten' => $IDKonten
                ]
            ])['data'];
            $x['gambar'] = $this->album->getRows([
                'from' => 'gambarkonten',
                'where' => [
                    ['IDKonten' => $IDKonten]
                ]
            ])['data'];
        } else {
            ## batasi akses pengguna
            if (!$this->akses->AddData) {
                abort(403, 'Unauthorized access');
            }
        }

        return view('admin.manajemen-album.create', $x);
    }

    public function postStore(Request $request)
    {
        $IDKonten = my_decrypt($request->IDKonten) ?? '';
        $insertdata = Purify::clean(request()->except(['_token']));

        $edit = true;
        if ($IDKonten == '') {
            $edit = false;
            $IDKonten = $this->album->createId('KTN', 'IDKonten');
        }

        $fileGambars = [];
        if ($request->hasFile('FileGambar')) {
            $uploadFile = $this->album->uploadFile(fileName: "FileGambar", uploadPath: "FotoAlbum", allowedFile: ['webp', 'png', 'jpg', 'jpeg'], maxFileSize: 2048);
            if (!$uploadFile['status']) {
                return response()->json(['data' => [], 'status' => false, 'message' => $uploadFile['message']], 200);
            }
            $fileGambars = collect($uploadFile['data'])->map(function ($f) {
                return 'FotoAlbum/' . $f;
            });
        }
        if ($request->FileGambarLama) {
            //file gambar lama dikirim dengan format "file1||file2||file3"
            $fileGambarLamas = explode('||', $request->FileGambarLama);
            foreach ($fileGambarLamas as $gambarLama) {
                if (file_exists(public_path('storage/' . $gambarLama))) {
                    unlink(public_path('storage/' . $gambarLama));
                }
            }
        }

        unset($insertdata['FileGambar']);
        unset($insertdata['FileGambarLama']);

        // data yang akan diinsert ke tabel gambarkonten
        $dataGambarKonten = [];
        $lastNoUrut = $this->album->getRow([
            'from' => 'gambarkonten',
            'where' => [
                'IDKonten' => $IDKonten
            ],
            'orderBy' => 'NoUrut DESC'
        ])['data']->NoUrut ?? 0;

        foreach ($fileGambars as $gambar) {
            $dataGambarKonten[] = [
                'NoUrut' => ++$lastNoUrut,
                'IDKonten' => $IDKonten,
                'FileGambar' => $gambar,
                'IsAktif' => 1,
                'Keterangan' => ''
            ];
        }

        //berlaku untuk edit dan tambah
        $insertdata['JenisKonten'] = 'ALBUM';

        if (!$edit) {
            ## tambah data
            $pesan = 'tambah data';
            $insertdata['IDKonten'] = $IDKonten;
            $insertdata['TglEntry'] = date('Y-m-d H:i:s');
            $insertdata['UserName'] = auth()->user()->UserName;
            $insertdata['IsAktif'] = 1;
            $result = $this->album->insertData($insertdata);
        } else {
            ## update data
            $pesan = 'update data';
            unset($insertdata['IDKonten']);
            $result = $this->album->updateData($insertdata, ['IDKonten' => $IDKonten]);
        }

        if ($request->FileGambarLama != '') {
            $whereInValue = '';
            //file gambar lama dikirim dengan format "file1||file2||file3"
            $fileGambarLamas = explode('||', $request->FileGambarLama);
            foreach ($fileGambarLamas as $gambarLama) {
                $whereInValue .= '("' . $IDKonten . '", "' . $gambarLama . '"), ';
            }
            if($whereInValue){
                $whereInValue = rtrim($whereInValue, ', ');
            }
            $whereInValue = Purify::clean($whereInValue);

            DB::table('gambarkonten')
                ->whereRaw('(IDKonten, FileGambar) IN (' . $whereInValue . ')')
                ->delete();
        }
        if (count($dataGambarKonten)) {
            $this->album->insertData($dataGambarKonten, 'gambarkonten');
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
            $IDKonten = isset($deleteParams['id']) ? my_decrypt($deleteParams['id']) : null;
            $noalbum = isset($deleteParams['no']) ? my_decrypt($deleteParams['no']) : null;

            if (!$IDKonten) {
                return response()->json([
                    'status' => false,
                    'message' => 'IDKonten tidak ditemukan.'
                ]);
            }

            $item = $this->album::where(['IDKonten' => $IDKonten, 'Noalbum' => $noalbum])->first();
            if ($item) {
                $this->album->updateData(['IsAktif' => 0], ['IDKonten' => $IDKonten, 'Noalbum' => $noalbum]);
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
