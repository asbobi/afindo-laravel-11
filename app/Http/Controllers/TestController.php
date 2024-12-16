<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

class TestController extends Controller
{
    public function getIndex()
    {
        $absenData = DB::table('absenmanual')
            ->where('updated', 0)
            //->limit(5000)
            ->get();

        foreach ($absenData as $absen) {
            // Menyiapkan data untuk dikirim
            $data = [
                'kodepegawai' => $absen->kodepegawai,
                'hadir' => $absen->hadir,
                'menitpelanggaran' => $absen->menitpelanggaran,
                'dinasluar' => $absen->dinasluar,
                'keterangan' => '',
                'status' => $absen->status,
                'tanggal' => $absen->tanggal,
                'jamdatang' => $absen->jamdatang,
                'jampulang' => $absen->jampulang,
                'tidakapel' => $absen->tidakapel,
                'ketdinas' => '',
            ];

            // URL endpoint Laravel
            $url = 'https://sitamppan.wonogirikab.go.id/api/AbsensiManual.php';

            // Inisialisasi cURL
            $ch = curl_init();

            // Set options untuk cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Kirim data dalam bentuk x-www-form-urlencoded

            // Mengatur header jika diperlukan
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);

            // Eksekusi cURL dan simpan response
            $response = curl_exec($ch);

            // Periksa jika ada error
            if ($response === false) {
                echo "cURL Error: " . curl_error($ch);
                curl_close($ch);
                continue;
            }

            // Menutup koneksi cURL
            curl_close($ch);

            // Decode response JSON
            $responseData = json_decode($response, true);

            // Cek jika status dalam response adalah true
            if (isset($responseData['status']) && $responseData['status'] === true) {
                // Update kolom 'updated' menjadi 1
                DB::table('absenmanual')
                    ->where('kodepegawai', $absen->kodepegawai)
                    ->update(['updated' => 1]);
            } else {
                // Jika status tidak true, log error atau lakukan tindakan lain
                Log::error("Failed to update absen for kodepegawai: {$absen->kodepegawai}, Response: " . json_encode($responseData));
            }
        }

        return response()->json(['message' => 'Data processing complete']);
    }

    public function getTugas()
    {
        $absenData = DB::table('tugaskhusus')
            ->where('updated', 0)
            ->get();

        foreach ($absenData as $absen) {
            // Menyiapkan data untuk dikirim
            $data = [
                'kodepegawai' => $absen->kodepegawai,
                'tanggal' => $absen->tanggal,
                'jamdatang' => $absen->jamdatang,
                'jampulang' => $absen->jampulang,
            ];

            // URL endpoint Laravel
            $url = 'https://sitamppan.wonogirikab.go.id/api/TugasKhusus.php';

            // Inisialisasi cURL
            $ch = curl_init();

            // Set options untuk cURL
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Kirim data dalam bentuk x-www-form-urlencoded

            // Mengatur header jika diperlukan
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);

            // Eksekusi cURL dan simpan response
            $response = curl_exec($ch);

            // Periksa jika ada error
            if ($response === false) {
                echo "cURL Error: " . curl_error($ch);
                curl_close($ch);
                continue;
            }

            // Menutup koneksi cURL
            curl_close($ch);

            // Decode response JSON
            $responseData = json_decode($response, true);

            // Cek jika status dalam response adalah true
            if (isset($responseData['status']) && $responseData['status'] === true) {
                // Update kolom 'updated' menjadi 1
                DB::table('tugaskhusus')
                    ->where('kodepegawai', $absen->kodepegawai)
                    ->update(['updated' => 1]);
            } else {
                // Jika status tidak true, log error atau lakukan tindakan lain
                Log::error("Failed to update tugas khusus for kodepegawai: {$absen->kodepegawai}, Response: " . json_encode($responseData));
            }
        }

        return response()->json(['message' => 'Data processing complete']);
    }

    public function getCetak()
    {
        $dataList = [];
        for ($i = 1; $i <= 100; $i++) {
            $data = [
                'no' => $i,
                'item' => 'Item ' . $i,
                'kondisi' => 'Bekas',
                'qty' => 1
            ];
            $dataList[] = $data;
        }

        $maxRowsPerPage = 13;

        $pagedData = array_chunk($dataList, $maxRowsPerPage * 2);

        $options = new Options();
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);

        $html = view('packing_service', compact('pagedData'))->render();
        $dompdf->setPaper([0, 0, 330, 210], 'landscape');

        $dompdf->loadHtml($html);

        $dompdf->render();

        return $dompdf->stream('packing_service.pdf', ['Attachment' => false]);
    }
}
