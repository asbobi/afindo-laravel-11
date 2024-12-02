<?php

namespace Database\Factories;

use App\Models\TrKunjungan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TrKunjungan>
 */
class TrKunjunganFactory extends Factory
{
    protected $model = TrKunjungan::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $IDLoket = [
            '19640',
            '22236',
            '24854',
            '28782',
            '59696',
            '61520',
            '74701',
            '8201',
            '94161',
            '98566',
            'LOKET-0000001',
            'LOKET-0000002',
            'LOKET-0000003'
        ];
        $IDLayanan = [
            'LAY-0000024',
            'LAY-0000025',
            'LAY-0000026',
            'LAY-0000027',
        ];
        static $idKunjunganCounter = 1;
        return [
            "IDKunjungan" => $idKunjunganCounter++,  // Nilai acak untuk ID kunjungan
            "TanggalJam" => $this->faker->dateTimeThisYear()->format('Y-m-d'), // Tanggal dan waktu acak tahun ini
            "JamDilayani" => $this->faker->time($format = 'H:i:s'), // Waktu acak dengan format jam
            "NoAntrian" => $this->faker->randomNumber(3), // Nomor antrian acak
            "StatusAntrian" => $this->faker->randomElement(['tunggu', 'proses', 'lewati', 'selesai']), // Status antrian acak
            "IDLoket" => $this->faker->randomElement($IDLoket), // Loket acak dari array
            "UserName" => 'admin', // Tetap, sesuai permintaan
            "IDUser" => null, // Dikosongkan, sesuai permintaan
            "NilaiSPM" => $this->faker->randomFloat(2, 1, 10), // Nilai SPM acak antara 1 dan 10 dengan 2 desimal
            "IDLayanan" => $this->faker->randomElement($IDLayanan), // Layanan acak dari array
        ];
    }
}
