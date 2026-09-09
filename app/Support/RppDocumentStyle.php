<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Batas kepercayaan untuk desain & tema dokumen.
 *
 * Nilai desain/tema bisa datang dari query string saat mengunduh, dan key-nya
 * dipakai untuk menyusun nama partial serta lookup config. Semua nilai WAJIB
 * lewat sini: hanya key yang benar-benar ada di config yang diloloskan, sisanya
 * jatuh ke nilai dokumen lalu ke default. Pilihan desain tidak boleh pernah
 * menjadi jalur gagal — paling buruk dokumen tampil dengan desain default.
 */
final class RppDocumentStyle
{
    public const DEFAULT_DESIGN = 'klasik';

    public const DEFAULT_THEME = 'merah';

    public static function design(?string $requested, ?string $stored): string
    {
        return self::resolve('rpp_designs', $requested, $stored, self::DEFAULT_DESIGN);
    }

    public static function theme(?string $requested, ?string $stored): string
    {
        return self::resolve('rpp_themes', $requested, $stored, self::DEFAULT_THEME);
    }

    private static function resolve(string $configKey, ?string $requested, ?string $stored, string $fallback): string
    {
        $available = config($configKey, []);

        foreach ([$requested, $stored, $fallback] as $candidate) {
            if (is_string($candidate) && $candidate !== '' && array_key_exists($candidate, $available)) {
                return $candidate;
            }
        }

        // Config kosong atau key default terhapus: kembalikan key pertama yang ada.
        return (string) (array_key_first($available) ?? $fallback);
    }
}
