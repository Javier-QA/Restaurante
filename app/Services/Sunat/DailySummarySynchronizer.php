<?php

namespace App\Services\Sunat;

use App\Models\DailySummary;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DailySummarySynchronizer
{
    public function synchronize(DailySummary $summary): void
    {
        if (! in_array($summary->sunat_status, ['ACCEPTED', 'OBSERVED'], true) || ! $summary->cdr_path) {
            return;
        }

        DB::transaction(function () use ($summary) {
            foreach ($summary->details()->where('operation_status', '1')->get() as $detail) {
                $order = Order::whereKey($detail->order_id)->lockForUpdate()->first();
                if (! $order || $order->document_type !== 'Boleta'
                    || $order->serie !== $detail->serie
                    || (int) $order->correlativo !== (int) $detail->correlativo
                    || $order->sunat_status === 'ACCEPTED') {
                    continue;
                }
                $order->forceFill([
                    'sunat_status' => $summary->sunat_status,
                    'sunat_code' => $summary->sunat_code,
                    'sunat_description' => $summary->sunat_description,
                ])->save();
            }
        });
    }
}
