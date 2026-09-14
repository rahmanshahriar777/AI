<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'model',
        'prompt_tokens',
        'completion_tokens',
        'total_tokens',
        'latency_ms',
        'status',
        'error_type',
        'error_message',
        'module',
    ];

    protected $casts = [
        'prompt_tokens' => 'integer',
        'completion_tokens' => 'integer',
        'total_tokens' => 'integer',
        'latency_ms' => 'float',
    ];

    /**
     * Get count of requests served by a provider today.
     */
    public static function getTodayUsageCount(string $provider): int
    {
        return static::where('provider', $provider)
            ->where('status', 'success')
            ->whereDate('created_at', today())
            ->count();
    }
}
