<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $order->document_type }} {{ $order->full_number }}</title>

    <style>
        @page {
            size: A4;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8pt;
    color: #000;
    margin: 0;
    padding: 0;
    background: #fff;
}

        .sheet {
    width: 190mm;
    max-width: 190mm;
    min-height: 277mm;
    margin: 0 auto;
    padding: 4mm;
}

        .actions {
            text-align: center;
            margin-bottom: 12px;
        }

        .actions button {
            padding: 8px 18px;
            border: 1px solid #000;
            background: #000;
            color: #fff;
            cursor: pointer;
            border-radius: 3px;
        }

        @media print {
            .actions {
                display: none;
            }
        }

        /* CABECERA */

        .header {
    display: table;
    width: 100%;
    border-bottom: 1px solid #000;
    padding-bottom: 7px;
}

        .h-left,
        .h-right {
    display: table-cell;
    width: 36%;
    text-align: center;
    vertical-align: top;
    border: 1px solid #000;
    padding: 8px 6px;
}

        .h-left {
    display: table-cell;
    width: 64%;
    padding-right: 12px;
    vertical-align: top;
}

        .h-right {
    display: table-cell;
    width: 36%;
    text-align: center;
    vertical-align: top;
    border: 1px solid #000;
    padding: 8px 6px;
}

        .commercial-name {
            font-size: 9pt;
            margin-bottom: 7px;
            font-weight: normal;
        }

        .company-name {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 7px;
            text-transform: uppercase;
        }

        .address {
            font-size: 8pt;
            line-height: 1.4;
        }

        .ruc {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .document-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .document-number {
            font-size: 11pt;
            font-weight: bold;
        }

        /* DATOS */

        .information {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }

        .information td {
            padding: 3px 2px;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .information .label {
            font-weight: bold;
            width: 18%;
            white-space: nowrap;
        }

        .information .value {
            width: 32%;
        }

        /* DETALLE */

        table.items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
    font-size: 8pt;
    table-layout: fixed;
}

        table.items th {
    background: #000;
    color: #fff;
    border: 1px solid #000;
    padding: 5px 4px;
    text-align: center;
    font-weight: bold;
    font-size: 7.5pt;
}

        table.items td {
    border: 1px solid #aaa;
    padding: 5px 4px;
    height: 23px;
    font-size: 7.8pt;
}

        table.items td.center {
            text-align: center;
        }

        table.items td.num {
            text-align: right;
            white-space: nowrap;
        }

        /* TOTALES */

        .bottom {
            display: table;
            width: 100%;
            margin-top: 12px;
        }

        .amount-words,
        .totals {
            display: table-cell;
            vertical-align: top;
        }

        .amount-words {
            width: 62%;
            padding-top: 8px;
            padding-right: 20px;
        }

        .amount-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .amount-text {
            text-transform: uppercase;
            font-weight: bold;
        }

        .totals {
            width: 38%;
        }

        .totals table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        .totals td {
            padding: 4px 6px;
            border-bottom: 1px solid #d0d0d0;
        }

        .totals td:first-child {
            text-align: right;
        }

        .totals td:last-child {
            text-align: right;
            white-space: nowrap;
        }

        .totals .total td {
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            background: #e8e8e8;
            font-size: 10pt;
            font-weight: bold;
        }

        /* PIE */

        .footer {
            margin-top: 15px;
            border-top: 1px solid #000;
            padding-top: 10px;
            display: table;
            width: 100%;
        }

        .qr {
            display: table-cell;
            width: 130px;
            text-align: center;
            vertical-align: top;
        }

        .qr img {
            width: 100px;
            height: 100px;
        }

        .qr-text {
            font-size: 7pt;
            margin-top: 3px;
        }

        .legend {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
            font-size: 8pt;
            line-height: 1.5;
        }

        .sunat-status {
            margin-top: 7px;
            font-weight: bold;
        }

        .cdr-info {
            margin-top: 8px;
            padding: 6px;
            border: 1px dashed #888;
            font-size: 7pt;
            overflow-wrap: anywhere;
        }

        .legal {
            margin-top: 12px;
            padding-top: 5px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 7pt;
            font-style: italic;
        }

        .legal-small {
            text-align: center;
            font-size: 7pt;
            margin-top: 3px;
        }
    </style>
</head>

<body>

<div class="sheet">

    <div class="actions">
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>

    {{-- CABECERA --}}
    <div class="header">

        <div class="h-left">

            @if($company['nombre_comercial'] !== $company['razon_social'])
                <div class="commercial-name">
                    {{ $company['nombre_comercial'] }}
                </div>
            @endif

            <div class="company-name">
                {{ $company['razon_social'] }}
            </div>

            <div class="address">
                {{ $company['direccion'] }}
            </div>

        </div>

        <div class="h-right">

            <div class="document-title">
                {{ strtoupper($order->document_type) }} ELECTRÓNICA
            </div>

            <div class="ruc">
                RUC: {{ $company['ruc'] }}
            </div>

            <div class="document-number">
                {{ $order->full_number }}
            </div>

        </div>

    </div>

    {{-- INFORMACIÓN DEL COMPROBANTE --}}
    <table class="information">
        <tr>
            <td class="label">Formato de Pago</td>
            <td class="value">
                :
                @php
                    $paymentLabels = [
                        'cash' => 'Efectivo',
                        'card' => 'Tarjeta',
                        'yape' => 'Yape',
                        'plin' => 'Plin',
                    ];
                @endphp
                {{ $paymentLabels[$order->payment_method] ?? strtoupper($order->payment_method ?? '—') }}
            </td>

            <td class="label">Tipo de Moneda</td>
            <td class="value">: SOLES</td>
        </tr>

        <tr>
            <td class="label">Fecha de Emisión</td>
            <td class="value">
                : {{ ($order->paid_at ?? $order->created_at)->format('d/m/Y H:i') }}
            </td>

            <td class="label">
                {{ $order->document_type === 'Factura' ? 'RUC' : 'DNI' }}
            </td>
            <td class="value">
                : {{ $order->client_document ?: '—' }}
            </td>
        </tr>

        <tr>
            <td class="label">Señor(es)</td>
            <td colspan="3">
                : {{ $order->client_name ?: 'Cliente Varios' }}
            </td>
        </tr>

        <tr>
            <td class="label">Dirección</td>
            <td colspan="3">
                : {{ $company['direccion'] }}
            </td>
        </tr>
    </table>

    {{-- DETALLE --}}
    <table class="items">

        <thead>
            <tr>
                <th style="width: 8%;">Cantidad</th>
                <th style="width: 13%;">Unidad</th>
                <th style="width: 10%;">Código</th>
                <th>Descripción</th>
                <th style="width: 14%;">Valor Unitario</th>
                <th style="width: 14%;">Importe</th>
            </tr>
        </thead>

        <tbody>

            @foreach($documentLines as $d)

                <tr>

                    <td class="center">
                        {{ number_format($d->getCantidad(), 2) }}
                    </td>

                    <td class="center">
                        UNIDAD
                    </td>

                    <td class="center">
                        {{ $d->getCodProducto() }}
                    </td>

                    <td>
                        {{ $d->getDescripcion() }}
                    </td>

                    <td class="num">
                        S/ {{ number_format($d->getMtoPrecioUnitario(), 2) }}
                    </td>

                    <td class="num">
                        S/ {{ number_format(($d->getMtoValorVenta() + $d->getIgv()), 2) }}
                    </td>

                </tr>

            @endforeach

            @if(count($documentLines) < 8)

                @for($i = 0; $i < 8 - count($documentLines); $i++)

                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>

                @endfor

            @endif

        </tbody>

    </table>

    {{-- TOTALES --}}
    <div class="bottom">

        <div class="amount-words">

            <div class="amount-title">
                SON:
            </div>

            <div class="amount-text">
                {{ strtoupper(\App\Services\Sunat\NumeroLetras::convert($order->collected_total)) }}
                SOLES
            </div>

        </div>

        <div class="totals">

            <table>

                <tr>
                    <td>Subtotal Ventas :</td>
                    <td>
                        S/ {{ number_format($order->total_gravada, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Descuentos :</td>
                    <td>
                        S/ {{ number_format($order->discount ?? 0, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>Valor Venta :</td>
                    <td>
                        S/ {{ number_format($order->total_gravada, 2) }}
                    </td>
                </tr>

                <tr>
                    <td>IGV :</td>
                    <td>
                        S/ {{ number_format($order->igv, 2) }}
                    </td>
                </tr>

                <tr class="total">

                    <td>Importe Total :</td>

                    <td>
                        S/ {{ number_format($order->collected_total, 2) }}
                    </td>

                </tr>

            </table>

        </div>

    </div>

    {{-- QR Y ESTADO SUNAT --}}
    <div class="footer">

        <div class="qr">

            <img src="{{ $qrBase64 }}" alt="QR SUNAT">

            <div class="qr-text">
                Código QR de comprobante electrónico
            </div>

        </div>

        <div class="legend">

            <strong>Representación impresa del comprobante electrónico.</strong>

            @if($order->sunat_status === 'ACCEPTED')

                <div class="sunat-status">
                    Aceptado por SUNAT
                    @if($order->sunat_code)
                        — Código {{ $order->sunat_code }}
                    @endif
                </div>

            @elseif($order->sunat_status === 'OBSERVED')

                <div class="sunat-status">
                    Aceptado con observaciones
                </div>

                @if($order->sunat_description)
                    <div>
                        {{ $order->sunat_description }}
                    </div>
                @endif

            @else

                <div class="sunat-status">
                    Estado SUNAT: {{ $order->sunat_status }}
                </div>

            @endif

        </div>

    </div>

    @if($order->hash)

        <div class="cdr-info">
            Resumen XML (DigestValue):
            <strong>{{ $order->hash }}</strong>
        </div>

    @endif

    <div class="legal">
        Esta es una representación impresa del comprobante electrónico
        {{ $order->full_number }}, generada en el Sistema de SUNAT.
    </div>

    <div class="legal-small">
        Puede verificarla utilizando su clave SOL.
    </div>

</div>

</body>
</html>