<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySummaryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_summary_id',
        'order_id',
        'operation_status',
        'document_type',
        'serie',
        'correlativo',
        'total_amount',
    ];

    protected $casts = [
        'daily_summary_id' => 'integer',
        'order_id' => 'integer',
        'correlativo' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function dailySummary()
    {
        return $this->belongsTo(DailySummary::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}