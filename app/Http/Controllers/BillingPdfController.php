<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Sunat\SunatConfig;
use App\Services\Sunat\SunatService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Genera la representación gráfica imprimible del comprobante.
 * Usa una vista Blade (A4 o ticket 80mm) con QR SUNAT embebido en base64.
 */
class BillingPdfController extends Controller
{
    public function show(Order $order)
    {
        abort_unless(in_array($order->document_type, ['Boleta', 'Factura']), 404);
        $order->load('details.product');

        return view('billing.pdf.a4', [
            'order'    => $order,
            'config'   => new SunatConfig(),
            'qrBase64' => $this->buildQr($order),
            'company'  => $this->companyData(),
        ]);
    }

    public function ticket(Order $order)
    {
        abort_unless(in_array($order->document_type, ['Boleta', 'Factura']), 404);
        $order->load('details.product');

        return view('billing.pdf.ticket', [
            'order'    => $order,
            'config'   => new SunatConfig(),
            'qrBase64' => $this->buildQr($order),
            'company'  => $this->companyData(),
        ]);
    }

    /**
     * Construye el payload QR según especificación SUNAT y lo devuelve como data URI.
     */
    private function buildQr(Order $order): string
    {
        $payload = (new SunatService())->buildQrPayload($order);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($payload)
            ->encoding(new Encoding('UTF-8'))
            ->size(220)
            ->margin(4)
            ->build();

        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    private function companyData(): array
    {
        $config = new SunatConfig();
        return [
            'ruc'              => $config->ruc(),
            'razon_social'     => $config->razonSocial(),
            'nombre_comercial' => $config->nombreComercial(),
            'direccion'        => $config->direccion(),
        ];
    }
}
