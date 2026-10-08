<?php

namespace App\Services\Sunat;

use App\Models\DailySummary;
use App\Models\DailySummaryDetail;
use App\Models\Order;
use Carbon\CarbonInterface;
use Greenter\Model\Company\Address;
use Greenter\Model\Company\Company;
use Greenter\Model\Summary\Summary;
use Greenter\Model\Summary\SummaryDetail;
use Illuminate\Support\Facades\DB;

/**
 * Construye un Resumen Diario de Boletas (RC) para SUNAT.
 *
 * El RC se usa para comunicar a SUNAT en bloque las boletas emitidas un día.
 * Las boletas (03) y sus notas (07) van como SummaryDetail con tipoOperacion = 1.
 */
class DailySummaryBuilder
{
    public function __construct(private SunatConfig $config) {}

    /**
     * @return array{summary: Summary, model: DailySummary}
     */
    public function build(CarbonInterface $referenceDate, ?int $userId = null): array
    {
        // 1. Boletas (03) de la fecha que aún no han sido comunicadas
        // como "Adicionar" en un Resumen Diario enviado a SUNAT.
        $orders = Order::where('document_type', 'Boleta')
            ->where('status', 'completed')
            ->whereDate('paid_at', $referenceDate->toDateString())
            ->whereNotNull('serie')
            ->whereNotNull('correlativo')
            // Excluye boletas históricas que ya obtuvieron CDR por el flujo individual anterior.
            ->whereNull('cdr_path')
            ->whereDoesntHave('dailySummaryDetails', function ($query) {
                $query->where('operation_status', '1')
                    ->whereHas('dailySummary', function ($summaryQuery) {
                        $summaryQuery->whereIn('sunat_status', [
                            'TICKET',
                            'ACCEPTED',
                            'OBSERVED',
                        ]);
                    });
            })
            ->orderBy('correlativo')
            ->get();

        if ($orders->isEmpty()) {
            throw new \RuntimeException('No hay boletas para incluir en el resumen.');
        }

        // 2. Correlativo del día (RC-yyyymmdd-N)
        $today = now();
        $existing = DailySummary::where('generation_date', $today->toDateString())->count();
        $correlNum = $existing + 1;
        $identifier = sprintf('RC-%s-%d', $today->format('Ymd'), $correlNum);

        // 3. Detalles
        $details = [];
        $totalAmount = 0.0;
        foreach ($orders as $o) {
            $detail = (new SummaryDetail)
                ->setTipoDoc('03')
                ->setSerieNro($o->full_number)
                ->setEstado('1')                 // 1 = Agregar, 2 = Modificar, 3 = Anular
                ->setClienteTipo($this->guessDocType($o->client_document))
                ->setClienteNro($o->client_document ?: '-')
                ->setTotal((float) $o->total)
                ->setMtoOperGravadas((float) $o->total_gravada ?: ($o->total ? round($o->total / (1 + $this->config->igvFactor()), 2) : 0))
                ->setMtoOperExoneradas((float) $o->total_exonerada)
                ->setMtoOperInafectas((float) $o->total_inafecta)
                ->setMtoOperGratuitas((float) $o->total_gratuita)
                ->setMtoIGV((float) $o->igv ?: round((float) $o->total - ((float) $o->total / (1 + $this->config->igvFactor())), 2));

            $details[] = $detail;
            $totalAmount += (float) $o->total;
        }

        // 4. Cabecera Summary
        $summary = (new Summary)
            ->setFecGeneracion($referenceDate)
            ->setFecResumen($today)
            ->setCorrelativo(str_pad((string) $correlNum, 3, '0', STR_PAD_LEFT))
            ->setCompany($this->buildCompany())
            ->setDetails($details);

        // 5. Guardar el Resumen Diario y las boletas incluidas.
        // Todo se registra en una sola transacción para mantener la trazabilidad.
        $model = DB::transaction(function () use (
            $referenceDate,
            $today,
            $correlNum,
            $identifier,
            $orders,
            $totalAmount,
            $userId
        ) {
            $model = DailySummary::create([
                'reference_date' => $referenceDate->toDateString(),
                'generation_date' => $today->toDateString(),
                'correlativo' => $correlNum,
                'identifier' => $identifier,
                'total_documents' => $orders->count(),
                'total_amount' => $totalAmount,
                'sunat_status' => 'PENDING',
                'user_id' => $userId,
            ]);

            foreach ($orders as $order) {
                DailySummaryDetail::create([
                    'daily_summary_id' => $model->id,
                    'order_id' => $order->id,
                    'operation_status' => '1',
                    'document_type' => '03',
                    'serie' => $order->serie,
                    'correlativo' => $order->correlativo,
                    'total_amount' => $order->total,
                ]);
            }

            return $model;
        });

        return ['summary' => $summary, 'model' => $model];
    }

    private function guessDocType(?string $doc): string
    {
        $doc = trim((string) $doc);
        if ($doc === '' || $doc === '-') {
            return '0';
        }
        if (strlen($doc) === 8) {
            return '1';
        }   // DNI
        if (strlen($doc) === 11) {
            return '6';
        }  // RUC

        return '0';                            // Sin documento / otros
    }

    private function buildCompany(): Company
    {
        $address = (new Address)
            ->setUbigueo($this->config->ubigeo())
            ->setDepartamento($this->config->departamento())
            ->setProvincia($this->config->provincia())
            ->setDistrito($this->config->distrito())
            ->setUrbanizacion($this->config->urbanizacion())
            ->setCodLocal('0000')
            ->setDireccion($this->config->direccion())
            ->setCodigoPais($this->config->codigoPais());

        return (new Company)
            ->setRuc($this->config->ruc())
            ->setRazonSocial($this->config->razonSocial())
            ->setNombreComercial($this->config->nombreComercial())
            ->setAddress($address);
    }
}
