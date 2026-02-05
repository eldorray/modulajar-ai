<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LjkResult extends Model
{
    protected $fillable = [
        'ljk_answer_key_id',
        'user_id',
        'nama_peserta',
        'nomor_peserta',
        'kelas',
        'jawaban_siswa',
        'jumlah_benar',
        'jumlah_salah',
        'jumlah_kosong',
        'skor',
        'scan_image',
    ];

    protected $casts = [
        'jawaban_siswa' => 'array',
        'skor' => 'decimal:2',
    ];

    /**
     * Get the answer key for this result.
     */
    public function answerKey(): BelongsTo
    {
        return $this->belongsTo(LjkAnswerKey::class, 'ljk_answer_key_id');
    }

    /**
     * Get the user (teacher) who graded.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the scan image URL.
     */
    public function getScanImageUrlAttribute(): ?string
    {
        if ($this->scan_image) {
            return asset('storage/' . $this->scan_image);
        }
        return null;
    }

    /**
     * Get grade letter based on score.
     */
    public function getGradeAttribute(): string
    {
        $score = $this->skor;

        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'E';
    }

    /**
     * Get comparison details with answer key.
     */
    public function getComparisonAttribute(): array
    {
        $answerKey = $this->answerKey;
        if (!$answerKey) return [];

        return $answerKey->gradeAnswers($this->jawaban_siswa ?? [])['details'];
    }
}
