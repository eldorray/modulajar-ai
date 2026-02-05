<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LjkTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'nama_template',
        'kop_image',
        'jenis_ujian',
        'tahun_ajaran',
        'jumlah_soal',
        'jumlah_pilihan',
        'mata_pelajaran_list',
        'show_essay_lines',
    ];

    protected $casts = [
        'mata_pelajaran_list' => 'array',
        'show_essay_lines' => 'boolean',
    ];

    /**
     * Get the user that owns the template.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the answer keys for this template.
     */
    public function answerKeys(): HasMany
    {
        return $this->hasMany(LjkAnswerKey::class);
    }

    /**
     * Get the kop image URL.
     */
    public function getKopImageUrlAttribute(): ?string
    {
        if ($this->kop_image) {
            return asset('storage/' . $this->kop_image);
        }
        return null;
    }

    /**
     * Get available answer options based on jumlah_pilihan.
     */
    public function getOptionsAttribute(): array
    {
        $options = ['A', 'B', 'C', 'D', 'E'];
        return array_slice($options, 0, $this->jumlah_pilihan);
    }

    /**
     * Default mata pelajaran list.
     */
    public static function defaultMataPelajaranList(): array
    {
        return [
            'BAHASA INDONESIA',
            'PKN',
            'PEND. AGAMA ISLAM',
            'BAHASA INGGRIS',
            'IPS',
            'AL-QUR\'AN HADIST',
            'MATEMATIKA',
            'SENI BUDAYA',
            'INFORMATIKA',
            'IPA',
            'BAHASA SUNDA',
            'AKIDAH AKHLAK',
            'FIQIH',
            'BAHASA ARAB',
            'PJOK',
        ];
    }
}
