<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    /** Unit sekolah yang didukung. */
    public const JENJANG = ['MI', 'SMP'];

    protected $fillable = [
        'jenjang',
        'logo',
        'nama_sekolah',
        'nsm',
        'npsn',
        'alamat',
        'kop_surat',
        'logo_kanan',
    ];

    /**
     * Get the settings for a school unit (MI/SMP), creating the row if needed.
     */
    public static function getSettings(?string $jenjang = null): self
    {
        return self::firstOrCreate(['jenjang' => self::normalizeJenjang($jenjang)]);
    }

    /**
     * Fall back to MI for null/unknown values.
     */
    public static function normalizeJenjang(?string $jenjang): string
    {
        return in_array($jenjang, self::JENJANG, true) ? $jenjang : 'MI';
    }
}
