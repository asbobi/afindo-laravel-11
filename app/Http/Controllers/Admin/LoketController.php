<?php

namespace App\Http\Controllers\Admin;

use App\Models\Mstloket;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Stevebauman\Purify\Facades\Purify;
use Illuminate\Support\Facades\Storage;

class LoketController extends Controller
{
    private $menu = 'Manajemen Loket';
    private $loket;
    public function __construct()
    {
        View::share('menu', $this->menu);
        View::share('title', $this->menu);
        $this->loket = new Mstloket();
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
                "data" => "NamaLoket",
                "name" => "NamaLoket",
                "cetak" => true
            ],
            [
                "data" => "NoLoket",
                "name" => "No Loket",
                "cetak" => true
            ],
            [
                "data" => "TglLoket",
                "name" => "Tgl Loket",
                "cetak" => true
            ],
            [
                "data" => "FileAudio",
                "name" => "File Audio",
                "cetak" => true
            ],
            [
                "data" => "FotoLoket",
                "name" => "Foto Loket",
                "cetak" => true
            ],
            [
                "data" => "IDLoket",
                "name" => "ID Loket",
                "cetak" => true,
                "class" => "text-right"
            ],
            [
                "data" => "UserName",
                "name" => "Username",
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
                "url" => url('admin/loket/create'),
            ];
        }

        ## opsional tambahkan pengecekan akses delete data
        if ($this->akses->DeleteData) {
            $buttons[] = [
                "type" => "delete",
                'param' => ['id', 'no'],
                'url' => url('admin/loket/delete'),
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
                    'url' => url('admin/loket/create'), // contoh url form import
                ]
            ];
            $buttons = array_merge($buttons, $printButton);
        }

        $config = [
            "ajaxUrl" => url('admin/loket/listdata'),
            "columns" => $columns,
            "title" => "Data Loket",
            "buttons" => $buttons,
            "filters" => [
                [
                    'type' => 'select',
                    'id' => 'is-aktif',
                    'label' => '',
                    'options' => [
                        [
                            'value' => '',
                            'label' => 'Semua'
                        ],
                        [
                            'value' => '1',
                            'label' => 'Aktif'
                        ],
                        [
                            'value' => '0',
                            'label' => 'Tidak Aktif'
                        ]
                    ]
                ],
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
            ]
        ];

        return view('admin.loket.index', [
            'config' => $config,
            'ajaxUrl' => url('admin/loket/listdata'),
            'title' => 'Data Loket'
        ]);
    }

    public function xgetListdata(Request $request)
    {
        $params = $request->all();

        $cari = $params['cari'] ?? '';
        unset($params['cari']);
        if ($cari != '') {
            $params['orLike'] = [
                ['NamaLoket', $cari],
                ['NoLoket', $cari],
                ['FileAudio', $cari],
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
            ['TglLoket', '>=', $tglawal],
            ['TglLoket', '<=', $tglakhir],
        ];

        $aktif = $params['is-aktif'] ?? '';
        unset($params['is-aktif']);
        if ($aktif != '') {
            $params['where'][] = ['IsAktif', '=', (int) $aktif];
        }

        $params['pre_datatable'] = function ($datatable) {
            return $datatable->editColumn('IsAvailable', function ($row) {
                return $row->IsAvailable == 1 ? '&#10004;' : '&#x2716;';
            })->addColumn('action', function ($row) {
                $button = $this->akses->EditData ? '<a style="padding:5px;" class="text-warning" href="' . url('admin/loket/create/' . my_encrypt($row->IDLoket)) . '"><i class="feather icon-edit-1"></i></a>
                ' : '';
                return $button;
            })
                ## tambahkan atribut data sesuai param yang digunakan pada deleteButton
                ->setRowData([
                    'data-id' => function ($row) {
                        return my_encrypt($row->IDLoket);
                    },
                    'data-no' => function ($row) {
                        return my_encrypt($row->NoLoket);
                    },
                ])
                ->rawColumns(['action', 'IsAvailable']);
        };

        return $this->loket->getRows($params);
    }

    public function getCreate(Request $request, string $kode = null)
    {
        $x = [];
        if (isset($kode)) {
            ## batasi akses pengguna
            if (!$this->akses->EditData) {
                abort(403, 'Unauthorized access');
            }
            $IDLoket = Purify::clean(my_decrypt($kode));
            $x['data'] = $this->loket->getRow([
                'where' => [
                    'IDLoket' => $IDLoket
                ]
            ])['data'];
        } else {
            ## batasi akses pengguna
            if (!$this->akses->AddData) {
                abort(403, 'Unauthorized access');
            }
        }

        return view('admin.loket.create', $x);
    }

    public function postStore(Request $request)
    {
        $IDLoket = my_decrypt($request->IDLoket);
        $insertdata = Purify::clean(request()->except(['_token']));

        $insertdata['UserName'] = auth()->user()->UserName;

        if ($IDLoket == '') {
            $insertdata['IDLoket'] = $this->loket->createId('LKT', 'IDLoket');
        }
        if ($request->hasFile('FotoLoket')) {
            ## param FotoLoketLama otomatis generate dari component untuk menyimpan nama file lama agar bisa dihapus
            if ($insertdata['FotoLoketLama'] != '') {
                if (Storage::exists('FotoLoket/' . $insertdata['FotoLoketLama'])) {
                    Storage::delete('FotoLoket/' . $insertdata['FotoLoketLama']);
                }
            }
            ## upload file gambar
            $uploadFile = $this->loket->uploadFile(fileName: "FotoLoket", uploadPath: "FotoLoket", allowedFile: ['webp', 'png', 'jpg', 'jpeg'], maxFileSize: 2048, oldFile: $request->FotoLoketLama);
            if (!$uploadFile['status']) {
                return response()->json(['data' => [], 'status' => false, 'message' => $uploadFile['message']], 200);
            }
            $insertdata['FotoLoket'] = $uploadFile['data'];
            unset($insertdata['FotoLoketLama']);
        } else {
            unset($insertdata['FotoLoket']);
            unset($insertdata['FotoLoketLama']);
        }

        if ($request->hasFile('FileAudio')) {
            ## param FileAudioLama otomatis generate dari component untuk menyimpan nama file lama agar bisa dihapus
            if ($insertdata['FileAudioLama'] != '') {
                if (Storage::exists('FileAudio/' . $insertdata['FileAudioLama'])) {
                    Storage::delete('FileAudio/' . $insertdata['FileAudioLama']);
                }
            }
            ## upload file audio
            $uploadFile = $this->loket->uploadFile(fileName: "FileAudio", uploadPath: "FileAudio", allowedFile: ['mp3', 'png', 'jpg', 'jpeg'], maxFileSize: 2048, oldFile: $request->FileAudioLama);
            if (!$uploadFile['status']) {
                return response()->json(['data' => [], 'status' => false, 'message' => $uploadFile['message']], 200);
            }
            $insertdata['FileAudio'] = $uploadFile['data'];
            unset($insertdata['FileAudioLama']);
        } else {
            unset($insertdata['FileAudio']);
            unset($insertdata['FileAudioLama']);
        }

        $insertdata['IsAktif'] = $request->has('IsAktif') ? (int) $request->IsAktif : 0;

        if ($IDLoket == '') {
            ## tambah data
            $pesan = 'tambah data';
            $insertdata['IDLoket'] = $this->loket->createId('LKT', 'IDLoket');
            $result = $this->loket->insertData($insertdata);
        } else {
            ## update data
            $pesan = 'update data';
            unset($insertdata['IDLoket']);
            $result = $this->loket->updateData($insertdata, ['IDLoket' => $IDLoket]);
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
            $idLoket = isset($deleteParams['id']) ? my_decrypt($deleteParams['id']) : null;
            $noLoket = isset($deleteParams['no']) ? my_decrypt($deleteParams['no']) : null;

            if (!$idLoket) {
                return response()->json([
                    'status' => false,
                    'message' => 'IDLoket tidak ditemukan.'
                ]);
            }

            $item = $this->loket::where(['IDLoket' => $idLoket, 'NoLoket' => $noLoket])->first();
            if ($item) {
                $this->loket->updateData(['IsAktif' => 0], ['IDLoket' => $idLoket, 'NoLoket' => $noLoket]);
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
