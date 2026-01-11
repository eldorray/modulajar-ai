<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'logo',
        'nama_sekolah',
        'nsm',
        'npsn',
        'alamat',
    ];

    /**
     * Get the first (and only) settings record, or create one if it doesn't exist.
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }
}
