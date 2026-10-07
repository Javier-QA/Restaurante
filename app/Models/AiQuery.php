<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiQuery extends Model
{
    protected $fillable = [
        'user_id',
        'question',
        'sql_query',
        'result_count',
        'chart_type',
        'is_favorite',
    ];

    protected $casts = [
        'result_count' => 'integer',
        'is_favorite' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}