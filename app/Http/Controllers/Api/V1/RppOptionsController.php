<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class RppOptionsController extends Controller
{
    public function show()
    {
        return api_response()->success([
            'phases' => ['A', 'B', 'C', 'D', 'E', 'F', 'RA', 'MI Rendah', 'MI Tinggi', 'MTs', 'MA'],
            'semesters' => ['Ganjil', 'Genap'],
            'assessments' => ['Diagnostik Kognitif', 'Diagnostik Non-Kognitif', 'Formatif', 'Sumatif'],
            'curricula' => ['Kurikulum Merdeka', 'Kurikulum Merdeka Deep Learning', 'Kurikulum Berbasis Cinta'],
            'themes' => array_keys(config('rpp_themes')),
            'meetings' => range(1, 10),
            'roles' => ['admin', 'guru'],
            'guru_statuses' => ['aktif', 'nonaktif'],
            'genders' => ['L', 'P'],
        ]);
    }
}
