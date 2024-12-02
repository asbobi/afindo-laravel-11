<?php

namespace Database\Factories;

use App\Models\Mstloket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mstloket>
 */
class MstLoketFactory extends Factory
{
    protected $model = Mstloket::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'FileAudio' => $this->faker->fileExtension . '.mp3',
            'IDLoket' => $this->faker->randomNumber(5),
            'IsAktif' => 1,
            'IsAvailable' => 1,
            'NamaLoket' => $this->faker->company,
            'NoLoket' => $this->faker->randomNumber(5),
            'UserName' => 'admin',
        ];
    }
}
