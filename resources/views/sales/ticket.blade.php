<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Precuenta #{{ $order->id }}</title>
    <style>
        /* CONFIGURACIÓN EXACTA PARA IMPRESORA TÉRMICA */
        @page {
            margin: 0;
            padding: 0;
            size: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace; /* Fuente monoespaciada para alinear columnas */
            font-size: 12px;
            margin: 20px auto;
            padding: 5px;
            width: 78mm; /* Ancho estándar de papel térmico (80mm menos márgenes de seguridad) */
            color: #000;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        
        .header { margin-bottom: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px; }
        .footer { margin-top: 10px; border-top: 1px dashed #000; padding-top: 5px; font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        td, th { vertical-align: top; padding: 2px 0; }
        
        /* Columnas */
        .qty { width: 10%; text-align: left; }
        .desc { width: 60%; text-align: left; }
        .price { width: 30%; text-align: right; }
        
        .totals { margin-top: 10px; border-top: 1px solid #000; padding-top: 5px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 2px; }
        
        /* Ocultar botones al imprimir */
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 0; }
        }
    </style>

<style>
html,
body {
    min-height: 100%;
}

body {
    width: auto !important;
    min-height: 100vh;
    margin: 0 !important;
    padding: 80px 20px 40px !important;
    background: #eef5fb !important;
    box-sizing: border-box;
}

.ticket-preview {
    width: 78mm;
    margin: 0 auto;
    padding: 18px 14px;
    background: #ffffff;
    color: #000;
    box-shadow: 0 12px 35px rgba(0,0,0,.12);
    border-radius: 8px;
    box-sizing: border-box;
}

.preview-toolbar {
    position: fixed;
    top: 18px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 1000;
}

.preview-btn {
    min-width: 105px;
    padding: 9px 16px;
    border-radius: 8px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    text-align: center;
    cursor: pointer;
}

.preview-btn-back {
    background: #ffffff;
    color: #475569;
    border: 1px solid #cbd5e1;
}

.preview-btn-back:hover {
    background: #f8fafc;
    color: #1e293b;
}

.preview-btn-print {
    background: #ff8c00;
    color: #ffffff;
    border: 1px solid #ff8c00;
}

.preview-btn-print:hover {
    background: #e07b00;
    border-color: #e07b00;
}

.header {
    padding-bottom: 10px;
    margin-bottom: 10px;
}

.header img {
    max-width: 60px !important;
}

.totals {
    margin-top: 14px;
}

.footer {
    margin-top: 16px;
}

@media print {

    body {
        width: 78mm !important;
        min-height: auto !important;
        margin: 0 !important;
        padding: 5px !important;
        background: #ffffff !important;
    }

    .no-print {
        display: none !important;
    }

    .ticket-preview {
        width: 100%;
        margin: 0;
        padding: 0;
        box-shadow: none;
        border-radius: 0;
    }
}
</style>

<style>
/* IMPRESION TERMICA 80MM */
@media print {

    @page {
        size: 80mm auto;
        margin: 2mm;
    }

    html,
    body {
        width: 80mm !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
    }

    .ticket-preview {
        width: 78mm !important;
        margin: 0 auto !important;
        padding: 3mm 2mm !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        box-sizing: border-box !important;
    }

    .no-print,
    .preview-toolbar {
        display: none !important;
    }

    table {
        width: 100% !important;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
</head>
<body> <div class="no-print" style="position: fixed; top: 0; right: 0; padding: 10px; background: white; border: 1px solid #ccc; z-index: 1000;">
        <button onclick="window.print()" style="padding: 10px 18px; font-weight: bold; cursor: pointer; background: #198754; color: white; border: 0; border-radius: 6px;">IMPRIMIR PRECUENTA</button>
    </div>

    <div class="header text-center">
        @if(isset($settings['company_logo']))
            <img src="{{ asset('storage/'.$settings['company_logo']) }}" style="max-width: 50px; filter: grayscale(100%); margin-bottom: 5px;">
            <br>
        @endif
        
        <div class="fw-bold fs-5 uppercase" style="font-size: 14px;">{{ $settings['company_name'] ?? 'MI RESTAURANTE' }}</div>
        <div>{{ $settings['company_address'] ?? 'Dirección del Local' }}</div>
        <div>Tel: {{ $settings['company_phone'] ?? '---' }}</div>
        <div style="margin-top: 5px;">{{ now()->format('d/m/Y H:i') }}</div>
        <div class="fw-bold" style="margin-top:5px;">PRECUENTA #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
        
        @if($order->client_name && $order->client_name != 'Público')
            <div style="margin-top: 3px; font-weight: bold;">Cli: {{ Str::limit($order->client_name, 20) }}</div>
        @endif
        
        <div class="fw-bold" style="margin-top: 3px; font-size: 13px;">MESA: {{ $order->table->name ?? 'BARRA' }}</div>
    </div>

    <table>
        <thead>
            <tr style="border-bottom: 1px solid #000;">
                <th class="qty">C.</th>
                <th class="desc">DESCRIPCION</th>
                <th class="price">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->details as $detail)
                <tr>
                    <td class="qty">{{ $detail->quantity }}</td>
                    <td class="desc">
                        {{ $detail->product->name }}
                        @if($detail->note) 
                            <br><i style="font-size: 10px;">({{ $detail->note }})</i> 
                        @endif
                    </td>
                    <td class="price">{{ number_format($detail->quantity * $detail->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Subtotal:</span>
            <span>{{ $settings['currency_symbol'] ?? 'S/' }} {{ number_format($order->total - ($order->tip ?? 0) + ($order->discount ?? 0), 2) }}</span>
        </div>
        
        @if($order->discount > 0)
        <div class="row">
            <span>Descuento:</span>
            <span>-{{ number_format($order->discount, 2) }}</span>
        </div>
        @endif

        @if($order->tip > 0)
        <div class="row">
            <span>Propina:</span>
            <span>{{ number_format($order->tip, 2) }}</span>
        </div>
        @endif

        <div class="row fw-bold" style="font-size: 16px; margin-top: 5px; border-top: 1px dashed #000; padding-top: 5px;">
            <span>TOTAL A PAGAR:</span>
            <span>{{ $settings['currency_symbol'] ?? 'S/' }} {{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    <div class="footer text-center">
        {{ $settings['ticket_footer'] ?? '¡Gracias por su preferencia!' }}
        <br><br>
        .
    </div>

</div>

</body>
</html>