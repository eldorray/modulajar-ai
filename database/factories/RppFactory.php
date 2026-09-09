<?php

namespace Database\Factories;

use App\Models\Rpp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Rpp> */
class RppFactory extends Factory
{
    protected $model = Rpp::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_guru' => fake()->name(),
            'mata_pelajaran' => 'IPA',
            'fase' => 'D',
            'topik' => 'Ekosistem',
            'alokasi_waktu' => '2 JP',
            'jumlah_pertemuan' => 1,
            'jenis_asesmen' => 'Formatif, Sumatif',
            'kurikulum' => 'Kurikulum Merdeka',
            'tema' => 'merah',
            'status' => 'processing',
        ];
    }
}
