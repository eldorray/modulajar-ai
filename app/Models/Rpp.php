<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rpp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_guru',
        'kepala_sekolah',
        'nip_kepala_sekolah',
        'kota',
        'tanggal',
        'mata_pelajaran',
        'fase',
        'kelas',
        'semester',
        'target_peserta_didik',
        'topik',
        'alokasi_waktu',
        'jumlah_pertemuan',
        'kompetensi_awal',
        'kata_kunci',
        'model_pembelajaran',
        'jenis_asesmen',
        'kurikulum',
        'content_result',
        'status',
    ];

    protected $casts = [
        'content_result' => 'array',
        'tanggal' => 'date',
    ];

    /**
     * Get the user that owns the RPP.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the AI usage logs for this RPP.
     */
    public function aiUsageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class);
    }

    /**
     * Scope for completed RPPs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for user's RPPs.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
