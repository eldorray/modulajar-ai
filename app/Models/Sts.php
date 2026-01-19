<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sts extends Model
{
    use HasFactory;

    protected $table = 'sts';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    protected $fillable = [
        'user_id',
        'mata_pelajaran',
        'kelas',
        'fase',
        'topik',
        'tujuan_pembelajaran',
        'jumlah_soal',
        'jumlah_pg',
        'jumlah_pg_kompleks',
        'jumlah_menjodohkan',
        'jumlah_uraian',
        'content_result',
        'status',
    ];

    protected $casts = [
        'content_result' => 'array',
        'jumlah_soal' => 'integer',
        'jumlah_pg' => 'integer',
        'jumlah_pg_kompleks' => 'integer',
        'jumlah_menjodohkan' => 'integer',
        'jumlah_uraian' => 'integer',
    ];

    /**
     * Get the user that owns the STS.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
