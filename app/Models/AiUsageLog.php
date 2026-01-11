<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rpp_id',
        'provider',
        'model_name',
        'input_tokens',
        'output_tokens',
        'total_tokens',
    ];

    /**
     * Get the user that owns this log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the RPP associated with this log.
     */
    public function rpp(): BelongsTo
    {
        return $this->belongsTo(Rpp::class);
    }
}
