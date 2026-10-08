<?php

use App\Models\DailySummary;
use App\Services\Sunat\DailySummarySynchronizer;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DailySummary::whereIn('sunat_status', ['ACCEPTED', 'OBSERVED'])
            ->whereNotNull('cdr_path')->chunkById(100, function ($summaries) {
                foreach ($summaries as $summary) {
                    app(DailySummarySynchronizer::class)->synchronize($summary);
                }
            });
    }

    public function down(): void
    {
        // La aceptación de SUNAT no se revierte al deshacer una migración.
    }
};
