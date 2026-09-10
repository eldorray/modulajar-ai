<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    /** Unit sekolah yang didukung. */
    public const JENJANG = ['MI/SD', 'SMP/MTs', 'SMA/SMK'];

    public const DEFAULT_JENJANG = 'MI/SD';

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
     * Get the settings for a school unit, creating the row if needed.
     */
    public static function getSettings(?string $jenjang = null): self
    {
        $normalized = self::normalizeJenjang($jenjang);
        $settings = self::where('jenjang', $normalized)->first();

        if ($settings) {
            return $settings;
        }

        $legacyUnits = match ($normalized) {
            'MI/SD' => ['MI', 'SD'],
            'SMP/MTs' => ['SMP', 'MTs'],
            'SMA/SMK' => ['SMA', 'SMK'],
        };

        $settings = self::whereIn('jenjang', $legacyUnits)->first();

        if ($settings) {
            $settings->update(['jenjang' => $normalized]);

            return $settings->refresh();
        }

        return self::create(['jenjang' => $normalized]);
    }

    /**
     * Normalize current and legacy school-unit values.
     */
    public static function normalizeJenjang(?string $jenjang): string
    {
        return match ($jenjang) {
            'MI', 'SD', 'MI/SD' => 'MI/SD',
            'SMP', 'MTs', 'SMP/MTs' => 'SMP/MTs',
            'SMA', 'SMK', 'SMA/SMK' => 'SMA/SMK',
            default => self::DEFAULT_JENJANG,
        };
    }
}
