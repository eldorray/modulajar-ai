<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LjkAnswerKey extends Model
{
    protected $fillable = [
        'user_id',
        'ljk_template_id',
        'nama',
        'mata_pelajaran',
        'kelas',
        'jumlah_soal',
        'jumlah_pilihan',
        'kunci_jawaban',
    ];

    protected $casts = [
        'kunci_jawaban' => 'array',
    ];

    /**
     * Get the user that owns the answer key.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the template for this answer key.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(LjkTemplate::class, 'ljk_template_id');
    }

    /**
     * Get the results for this answer key.
     */
    public function results(): HasMany
    {
        return $this->hasMany(LjkResult::class);
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
     * Grade student answers and return results.
     */
    public function gradeAnswers(array $studentAnswers): array
    {
        $correct = 0;
        $wrong = 0;
        $empty = 0;
        $details = [];

        $answerKey = $this->kunci_jawaban ?? [];

        for ($i = 0; $i < $this->jumlah_soal; $i++) {
            $correctAnswer = $answerKey[$i] ?? null;
            $studentAnswer = $studentAnswers[$i] ?? null;

            if (empty($studentAnswer)) {
                $empty++;
                $status = 'empty';
            } elseif (strtoupper($studentAnswer) === strtoupper($correctAnswer)) {
                $correct++;
                $status = 'correct';
            } else {
                $wrong++;
                $status = 'wrong';
            }

            $details[] = [
                'nomor' => $i + 1,
                'kunci' => $correctAnswer,
                'jawaban' => $studentAnswer,
                'status' => $status,
            ];
        }

        $score = $this->jumlah_soal > 0 ? ($correct / $this->jumlah_soal) * 100 : 0;

        return [
            'jumlah_benar' => $correct,
            'jumlah_salah' => $wrong,
            'jumlah_kosong' => $empty,
            'skor' => round($score, 2),
            'details' => $details,
        ];
    }
}
