<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiSetting extends Model
{
    protected $fillable = [
        'api_key',
        'model',
        'endpoint',
        'temperature',
        'max_tokens',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
        'temperature' => 'float',
        'max_tokens' => 'integer',
    ];

    /**
     * Baris tunggal pengaturan AI.
     */
    public static function getSettings(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }

    /**
     * Nilai efektif: DB dulu, fallback ke config/.env.
     * Aman dipanggil sebelum migrasi (tabel belum ada → pakai config).
     */
    public static function resolved(): array
    {
        $row = rescue(fn () => self::getSettings(), null, false);

        return [
            'api_key' => $row?->api_key ?: config('deepseek.api_key'),
            'model' => $row?->model ?: config('deepseek.model', 'deepseek-chat'),
            'endpoint' => $row?->endpoint ?: config('deepseek.endpoint'),
            'temperature' => $row?->temperature ?? 0.7,
            'max_tokens' => $row?->max_tokens ?: 8192,
        ];
    }

    /**
     * Tampilan aman API key untuk UI (tanpa membocorkan isinya).
     */
    public function maskedApiKey(): ?string
    {
        $key = $this->api_key;

        return $key ? str_repeat('•', 8).substr($key, -4) : null;
    }
}
