@extends('layouts.app')

@section('content')

<style>
    /* =========================================================
       SELECTOR DE TEMAS
    ========================================================= */

    .theme-section {
        background: linear-gradient(135deg, #f8fbff 0%, #ffffff 100%);
        border: 1px solid #e4edf5;
        border-radius: 20px;
        padding: 22px;
    }

    .theme-option {
        display: block;
        width: 100%;
        cursor: pointer;
        margin: 0;
    }

    .theme-radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .theme-card {
        height: 100%;
        min-height: 168px;
        padding: 17px;
        background: white;
        border: 2px solid #e7edf3;
        border-radius: 17px;
        position: relative;
        overflow: hidden;
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            border-color .2s ease;
    }

    .theme-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(15, 50, 80, .10);
    }

    .theme-radio:checked + .theme-card {
        border-color: #ff8c00;
        box-shadow:
            0 0 0 3px rgba(255, 140, 0, .12),
            0 10px 25px rgba(15, 50, 80, .10);
    }

    .theme-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }

    .theme-name {
        font-size: .92rem;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 3px;
    }

    .theme-description {
        font-size: .71rem;
        line-height: 1.4;
        color: #64748b;
    }

    .theme-check {
        width: 27px;
        height: 27px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #e9eef3;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transform: scale(.8);
        transition: .2s ease;
    }

    .theme-radio:checked + .theme-card .theme-check {
        background: #ff8c00;
        opacity: 1;
        transform: scale(1);
    }

    .theme-preview {
        display: grid;
        grid-template-columns: 70px 1fr;
        height: 73px;
        border-radius: 11px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, .06);
        margin-bottom: 12px;
    }

    .theme-preview-sidebar {
        position: relative;
        padding: 10px 7px;
    }

    .theme-preview-sidebar::before,
    .theme-preview-sidebar::after {
        content: '';
        display: block;
        height: 6px;
        background: rgba(255,255,255,.45);
        border-radius: 10px;
        margin-bottom: 7px;
    }

    .theme-preview-sidebar::before {
        width: 80%;
    }

    .theme-preview-sidebar::after {
        width: 55%;
    }

    .theme-preview-main {
        padding: 9px;
        position: relative;
    }

    .theme-preview-top {
        width: 100%;
        height: 9px;
        border-radius: 8px;
        background: rgba(255,255,255,.90);
        margin-bottom: 8px;
    }

    .theme-preview-cards {
        display: flex;
        gap: 5px;
    }

    .theme-preview-mini {
        flex: 1;
        height: 32px;
        background: white;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,.05);
        border-top: 4px solid transparent;
    }

    .theme-colors {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .theme-color {
        width: 24px;
        height: 24px;
        display: inline-block;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 0 0 1px #d6dde5;
    }

    .theme-current-label {
        margin-left: auto;
        font-size: .67rem;
        font-weight: 700;
        color: #ff8c00;
        display: none;
    }

    .theme-radio:checked + .theme-card .theme-current-label {
        display: inline-block;
    }

    /* =========================================================
       VISTA PREVIA GRANDE
    ========================================================= */

    .theme-live-preview {
        margin-top: 20px;
        border-radius: 17px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .theme-live-preview-header {
        padding: 12px 16px;
        background: white;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .theme-live-preview-screen {
        min-height: 150px;
        display: flex;
        transition: background .25s ease;
    }

    .theme-live-sidebar {
        width: 105px;
        padding: 17px 10px;
        transition: background .25s ease;
    }

    .theme-live-logo {
        width: 34px;
        height: 34px;
        margin: 0 auto 17px;
        border-radius: 10px;
        transition: background .25s ease;
    }

    .theme-live-menu {
        height: 7px;
        border-radius: 10px;
        background: rgba(255,255,255,.40);
        margin-bottom: 9px;
    }

    .theme-live-menu.active {
        height: 23px;
        border-radius: 7px;
        transition: background .25s ease;
    }

    .theme-live-content {
        flex: 1;
        padding: 15px;
    }

    .theme-live-navbar {
        height: 25px;
        background: white;
        border-radius: 8px;
        margin-bottom: 12px;
        box-shadow: 0 2px 7px rgba(0,0,0,.05);
    }

    .theme-live-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 9px;
    }

    .theme-live-stat {
        height: 54px;
        background: white;
        border-radius: 10px;
        border-top: 5px solid;
        box-shadow: 0 2px 7px rgba(0,0,0,.05);
        transition: border-color .25s ease;
    }

    @media (max-width: 768px) {
        .theme-live-preview-screen {
            min-height: 120px;
        }

        .theme-live-sidebar {
            width: 75px;
        }
    }
</style>



<style>
/* =========================================================
   CONFIGURACION - DISEÑO DEL SISTEMA
   ========================================================= */

.settings-page-header {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 22px;
}

.settings-page-header > i {
    margin-top: 4px;
    color:var(--text-main);
    font-size: 1.22rem;
}

.settings-page-title {
    margin: 0;
    color: var(--text-main);
    font-size: 1.5rem;
    font-weight: 800;
    letter-spacing: -.35px;
}

.settings-page-subtitle {
    margin: 4px 0 0;
    color: var(--text-muted);
    font-size: .8rem;
}


/* =========================================================
   TARJETA PRINCIPAL
   ========================================================= */

.settings-page-header + .card {
    border: 1px solid var(--border-soft) !important;
    border-radius: 15px !important;
    background: var(--card-bg) !important;
    box-shadow: var(--shadow-soft) !important;
    overflow: hidden;
}

.settings-page-header + .card > .card-body {
    padding: 24px !important;
}


/* =========================================================
   TITULOS DE SECCIONES
   ========================================================= */

.settings-page-header + .card form > h5,
.settings-page-header + .card form > div > h5 {
    display: flex;
    align-items: center;
    gap: 8px;

    margin-top: 5px;
    margin-bottom: 16px !important;

    padding-bottom: 11px;

    border-bottom: 1px solid var(--border-soft);

    color: var(--text-main) !important;

    font-size: .86rem;
    font-weight: 800;
}

.settings-page-header + .card form h5 > i {
    color: #111827 !important;
    font-size: .9rem;
}


/* =========================================================
   LABELS
   ========================================================= */

.settings-page-header + .card .form-label {
    margin-bottom: 6px;

    color: var(--text-main);

    font-size: .71rem;
    font-weight: 750 !important;
}


/* =========================================================
   INPUTS
   ========================================================= */

.settings-page-header + .card .form-control,
.settings-page-header + .card .form-select {
    min-height: 41px;

    border: 1px solid var(--border-soft);
    border-radius: 9px;

    background-color: var(--card-bg);

    color: var(--text-main);

    font-size: .75rem;

    box-shadow: none;
}

.settings-page-header + .card .form-control:focus,
.settings-page-header + .card .form-select:focus {
    border-color: var(--primary);

    box-shadow:
        0 0 0 3px
        color-mix(in srgb, var(--primary) 10%, transparent);
}

.settings-page-header + .card input[type="file"] {
    padding-top: 8px;
}


/* =========================================================
   TEXTOS AUXILIARES
   ========================================================= */

.settings-page-header + .card small,
.settings-page-header + .card .text-muted.small {
    font-size: .64rem;
    line-height: 1.45;
}


/* =========================================================
   SEPARADORES
   ========================================================= */

.settings-page-header + .card hr {
    margin-top: 25px;
    margin-bottom: 22px;

    border-color: var(--border-soft);

    opacity: 1 !important;
}


/* =========================================================
   SELECTOR DE TEMAS
   ========================================================= */

.theme-section {
    padding: 18px !important;

    border: 1px solid var(--border-soft) !important;
    border-radius: 13px !important;

    background:
        color-mix(
            in srgb,
            var(--primary) 2%,
            var(--card-bg)
        ) !important;
}

.theme-card {
    min-height: 158px !important;

    padding: 14px !important;

    border: 1px solid var(--border-soft) !important;
    border-radius: 11px !important;

    background: var(--card-bg) !important;

    box-shadow: none !important;
}

.theme-card:hover {
    transform: translateY(-2px) !important;

    border-color:
        color-mix(
            in srgb,
            var(--primary) 30%,
            var(--border-soft)
        ) !important;

    box-shadow: 0 6px 15px rgba(15,23,42,.06) !important;
}

.theme-radio:checked + .theme-card {
    border-color: var(--primary) !important;

    box-shadow:
        0 0 0 2px
        color-mix(
            in srgb,
            var(--primary) 10%,
            transparent
        ) !important;
}

.theme-radio:checked + .theme-card .theme-check {
    background: var(--primary) !important;
}

.theme-current-label {
    color: var(--primary) !important;
}

.theme-name {
    color: var(--text-main) !important;

    font-size: .8rem !important;
}

.theme-description {
    color: var(--text-muted) !important;

    font-size: .65rem !important;
}


/* =========================================================
   VISTA PREVIA DEL TEMA
   ========================================================= */

.theme-live-preview {
    margin-top: 17px !important;

    border: 1px solid var(--border-soft) !important;
    border-radius: 12px !important;

    background: var(--light-bg) !important;
}

.theme-live-preview-header {
    padding: 10px 14px !important;

    border-bottom: 1px solid var(--border-soft) !important;

    background: var(--card-bg) !important;

    color: var(--text-main);
}


/* =========================================================
   CONFIGURACION SUNAT
   ========================================================= */

.settings-page-header + .card
select[name="sunat_environment"] {
    font-weight: 700 !important;
}

.settings-page-header + .card
input[name="sunat_sol_pass"],
.settings-page-header + .card
input[name="sunat_cert_password"] {
    letter-spacing: .5px;
}


/* Certificado */

.settings-page-header + .card
input[name="sunat_cert_file"] {
    background: var(--light-bg);
}


/* =========================================================
   METODOS DE PAGO
   ========================================================= */

.settings-page-header + .card
form .mb-4 > h5[style] {
    color: var(--text-main) !important;
}


/* Yape y Plin */
.settings-page-header + .card
form .row.g-4 > .col-md-6 > .card {
    border: 1px solid var(--border-soft) !important;
    border-radius: 13px !important;

    background: var(--card-bg);

    box-shadow: 0 4px 12px rgba(15,23,42,.04) !important;
}


/* QR */
.settings-page-header + .card
img[alt="QR Yape"],
.settings-page-header + .card
img[alt="QR Plin"] {
    width: 145px !important;
    height: 145px !important;

    border: 1px solid var(--border-soft) !important;
    border-radius: 11px !important;

    box-shadow: 0 3px 10px rgba(15,23,42,.05);
}


/* =========================================================
   BOTON GUARDAR
   ========================================================= */

.settings-page-header + .card
button[type="submit"].btn-primary {
    min-height: 42px;

    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    padding-left: 18px !important;
    padding-right: 18px !important;

    border: 1px solid var(--primary);
    border-radius: 9px;

    background: var(--primary);

    color: #fff;

    font-size: .74rem;
    font-weight: 750 !important;

    box-shadow:
        0 4px 10px
        color-mix(
            in srgb,
            var(--primary) 17%,
            transparent
        ) !important;
}

.settings-page-header + .card
button[type="submit"].btn-primary:hover {
    border-color: var(--primary-hover);

    background: var(--primary-hover);

    color: #fff;

    transform: translateY(-1px);
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 767.98px) {

    .settings-page-header + .card > .card-body {
        padding: 17px !important;
    }

    .theme-section {
        padding: 13px !important;
    }

    .settings-page-header + .card
    button[type="submit"].btn-primary {
        width: 100%;
    }
}
</style>
<style>
.settings-page-header + .card form h5.text-primary {
 color:var(--text-main)!important;
}
.settings-page-header + .card form h5 > i {
 color:var(--text-main)!important;
}
</style>
<div class="container-fluid">

    <div class="row">

        <div class="col-12">

            {{-- =====================================================
                 ENCABEZADO
            ====================================================== --}}

            <div class="settings-page-header">
                <i class="bi bi-gear-fill"></i>

                <div>
                    <h2 class="settings-page-title">
                        Configuración
                    </h2>

                    <p class="settings-page-subtitle">
                        Personaliza la identidad, región, apariencia y facturación de tu negocio
                    </p>
                </div>
            </div>


            <div class="card border-0 shadow-sm">

                <div class="card-body p-4">


                    <form
                        action="{{ route('settings.update') }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >

                        @csrf


                        {{-- =================================================
                             DATOS DE LA EMPRESA
                        ================================================== --}}

<style>
#settingsTicketPreview.settings-precuenta{width:100%;max-width:78mm;margin:auto;padding:18px 14px;background:#fff!important;color:#000!important;font:12px/1.35 'Courier New',Courier,monospace;border:1px solid #dce3ea;border-radius:10px;box-sizing:border-box}
#settingsTicketPreview .sp-header{text-align:center;border-bottom:1px dashed #000;padding-bottom:10px;margin-bottom:10px}
#settingsTicketPreview img{max-width:60px;max-height:70px;object-fit:contain;filter:grayscale(100%);margin-bottom:5px}
#settingsTicketPreview .sp-name{font-size:14px;font-weight:bold;text-transform:uppercase}
#settingsTicketPreview .sp-bold{font-weight:bold;margin-top:3px}
#settingsTicketPreview .sp-gap{margin-top:5px}
#settingsTicketPreview table{width:100%;border-collapse:collapse;margin-top:5px;color:#000!important;font:inherit}
#settingsTicketPreview th,#settingsTicketPreview td{padding:2px 0;background:#fff!important;color:#000!important;text-align:left;border:0}
#settingsTicketPreview thead tr{border-bottom:1px solid #000}
#settingsTicketPreview th:first-child{width:10%}
#settingsTicketPreview th:last-child,#settingsTicketPreview td:last-child{width:30%;text-align:right}
#settingsTicketPreview .sp-totals{margin-top:14px;border-top:1px solid #000;padding-top:5px}
#settingsTicketPreview .sp-row{display:flex;justify-content:space-between;gap:8px;margin-bottom:2px}
#settingsTicketPreview .sp-total{font-size:16px;font-weight:bold;margin-top:5px;border-top:1px dashed #000;padding-top:5px}
#settingsTicketPreview .sp-footer{margin-top:16px;border-top:1px dashed #000;padding-top:5px;font-size:10px;text-align:center}
</style>
<div class="row g-4 align-items-start"><div class="col-xl-8">                        <h5 class="fw-bold text-primary mb-3">

                            <i class="bi bi-shop me-2"></i>
                            Datos de la Empresa

                        </h5>


                        <style>
.business-logo-panel{display:flex;align-items:center;gap:24px;padding:20px;margin-bottom:28px;border:1px dashed var(--border-soft);border-radius:18px;background:color-mix(in srgb,var(--primary) 3%,var(--card-bg))}
.business-logo-preview{width:140px;height:140px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:2px dashed var(--border-soft);border-radius:24px;background:var(--card-bg);padding:12px}
.business-logo-preview img{width:100%;height:100%;object-fit:contain}
.business-logo-placeholder{text-align:center;color:var(--text-muted)}
.business-logo-placeholder i{display:block;font-size:36px}
.business-logo-copy strong{display:block;font-size:1.1rem;color:var(--text-main);margin-bottom:4px}
.business-logo-copy p{color:var(--text-muted);margin:0 0 12px;line-height:1.55}
.business-logo-upload{position:relative;display:inline-flex!important;flex-wrap:nowrap;white-space:nowrap;width:auto!important;min-width:148px;min-height:42px;justify-content:center;align-items:center;gap:8px;padding:9px 18px;border:1px solid var(--border-soft);border-radius:15px;background:var(--card-bg);color:var(--text-main);font-weight:600;cursor:pointer}
.business-logo-upload i{flex-shrink:0}.business-logo-upload input{position:absolute!important;width:1px!important;height:1px!important;min-height:0!important;padding:0!important;margin:-1px!important;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0!important}
.business-logo-upload:hover{border-color:var(--primary);color:var(--primary)}
.business-logo-upload:focus-within{outline:2px solid var(--primary);outline-offset:3px}
@media(max-width:575px){.business-logo-panel{flex-direction:column;align-items:flex-start;gap:16px}}
</style>
<div class="business-logo-panel">
 <div class="business-logo-preview">
  <img id="companyLogoThumbnail" src="{{ !empty($settings['company_logo']) ? asset('storage/'.$settings['company_logo']) : '' }}" alt="Logo del restaurante" @if(empty($settings['company_logo'])) hidden @endif>
  <div id="companyLogoPlaceholder" class="business-logo-placeholder" @if(!empty($settings['company_logo'])) hidden @endif><i class="bi bi-image"></i><span>Sin logo</span></div>
 </div>
 <div class="business-logo-copy">
  <strong>Logo de la empresa</strong>
  <p>Se muestra en el menú, el inicio de sesión y el ticket. PNG, JPG o WEBP, hasta 2 MB.<br>Recomendado: cuadrado, fondo transparente.</p>
  <label class="business-logo-upload" for="companyLogoInput"><i class="bi bi-upload"></i>Subir logo
   <input id="companyLogoInput" type="file" name="company_logo" accept="image/png,image/jpeg,image/webp" class="visually-hidden">
  </label>
 </div>
</div>
<div class="row g-3 mb-4">
<div class="col-md-6"><label class="form-label fw-bold" for="business_company_name">Nombre comercial *</label><input id="business_company_name" type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}" placeholder="Ej: Restaurante Sabor Peruano" required ></div>
<div class="col-md-6"><label class="form-label fw-bold" for="business_sunat_razon_social">Razón social</label><input id="business_sunat_razon_social" type="text" name="sunat_razon_social" class="form-control" value="{{ old('sunat_razon_social', $settings['sunat_razon_social'] ?? '') }}" placeholder=""  ></div>
<div class="col-md-4"><label class="form-label fw-bold" for="business_sunat_ruc">RUC / ID fiscal</label><input id="business_sunat_ruc" type="text" name="sunat_ruc" class="form-control" value="{{ old('sunat_ruc', $settings['sunat_ruc'] ?? '') }}" placeholder="20123456789"  maxlength="11" pattern="[0-9]{11}"></div>
<div class="col-md-8"><label class="form-label fw-bold" for="business_company_business">Giro o rubro</label><input id="business_company_business" type="text" name="company_business" class="form-control" value="{{ old('company_business', $settings['company_business'] ?? '') }}" placeholder="Restaurante, pollería, cevichería…"  ></div>
<div class="col-md-8"><label class="form-label fw-bold" for="business_company_address">Dirección</label><input id="business_company_address" type="text" name="company_address" class="form-control" value="{{ old('company_address', $settings['company_address'] ?? '') }}" placeholder="Av. Principal 123, Lima"  ></div>
<div class="col-md-4"><label class="form-label fw-bold" for="business_company_city">Ciudad / distrito</label><input id="business_company_city" type="text" name="company_city" class="form-control" value="{{ old('company_city', $settings['company_city'] ?? '') }}" placeholder=""  ></div>
<div class="col-md-4"><label class="form-label fw-bold" for="business_company_phone">Teléfono</label><input id="business_company_phone" type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}" placeholder="(01) 555-0123"  ></div>
<div class="col-md-4"><label class="form-label fw-bold" for="business_company_email">Correo electrónico</label><input id="business_company_email" type="email" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email'] ?? '') }}" placeholder=""  ></div>
<div class="col-md-4"><label class="form-label fw-bold" for="business_company_website">Sitio web</label><input id="business_company_website" type="text" name="company_website" class="form-control" value="{{ old('company_website', $settings['company_website'] ?? '') }}" placeholder="www.mirestaurante.com"  ></div>
</div>

</div><div class="col-xl-4"><div class="card mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3"><i class="bi bi-eye me-2"></i>Vista previa de la precuenta</h5>
                                <div id="settingsTicketPreview" class="settings-precuenta">
 <div class="sp-header">
 @if(!empty($settings['company_logo']))
 <img id="ticketPreviewLogo" style="width:60px;height:70px;max-width:60px;max-height:70px;object-fit:contain" src="{{ asset('storage/'.$settings['company_logo']) }}" alt="Logo">
 @else
 <img id="ticketPreviewLogo" alt="Logo" hidden>
 @endif
 <div class="sp-name" data-ticket="company_name">{{ $settings['company_name'] ?? 'MI RESTAURANTE' }}</div>
 <div id="ticketPreviewRucRow" @if(empty($settings['sunat_ruc'])) hidden @endif>RUC: <span data-ticket="sunat_ruc">{{ $settings['sunat_ruc'] ?? '' }}</span></div>
 <div data-ticket="company_address">{{ $settings['company_address'] ?? '' }}</div>
 <div>Tel: <span data-ticket="company_phone">{{ $settings['company_phone'] ?? '' }}</span></div>
 <div class="sp-gap">{{ now()->format('d/m/Y H:i') }}</div>
 <div class="sp-bold sp-gap">PRECUENTA #000100</div>
 <div class="sp-bold">Cli: CLIENTE DE EJEMPLO</div>
 <div class="sp-bold">MESA: BARRA</div>
 </div>
 <table><thead><tr><th>C.</th><th>DESCRIPCION</th><th>TOTAL</th></tr></thead><tbody><tr><td>1</td><td>LECHE DE TIGRE</td><td>18.00</td></tr></tbody></table>
 <div class="sp-totals"><div class="sp-row"><span>Subtotal:</span><span data-ticket-price="18">S/ 18.00</span></div>
 <div class="sp-row sp-total"><span>TOTAL A PAGAR:</span><span data-ticket-price="18">S/ 18.00</span></div></div>
 <div class="sp-footer"><span data-ticket="ticket_footer">{{ $settings['ticket_footer'] ?? '¡Gracias por su preferencia!' }}</span><br><br>.</div>
</div>
<p class="small text-muted mt-3 mb-0">Ejemplo con productos e importes de muestra. Los datos del negocio se actualizan al editar el formulario.</p>
                            </div>
                        </div></div></div>
                                                <hr class="text-muted opacity-25">


                        {{-- =================================================
                             REGIÓN Y SISTEMA
                        ================================================== --}}

                        <h5 class="fw-bold text-primary mb-3">

                            <i class="bi bi-globe-americas me-2"></i>
                            Región y Sistema

                        </h5>


                        <div class="row g-3 mb-4">


                            <div class="col-md-6 col-xl-4">

                                <label class="form-label fw-bold">

                                    <i class="bi bi-clock"></i>
                                    Zona Horaria

                                </label>


                                <select
                                    name="timezone"
                                    class="form-select"
                                >

                                    @foreach($timezones as $tz => $label)

                                        <option
                                            value="{{ $tz }}"
                                            {{ ($settings['timezone'] ?? 'America/Lima') == $tz ? 'selected' : '' }}
                                        >

                                            {{ $label }}

                                        </option>

                                    @endforeach

                                </select>


                                <small class="text-muted">

                                    Hora actual del sistema:

                                    <strong>
                                        {{ \Carbon\Carbon::now()->format('H:i:s') }}
                                    </strong>

                                </small>

                            </div>


                            <div class="col-md-6 col-xl-2">

                                <label class="form-label fw-bold">
                                    <i class="bi bi-currency-exchange me-1" aria-hidden="true"></i>Moneda
                                </label>


                                <select
                                    name="currency_symbol"
                                    class="form-select"
                                >

                                    <option
                                        value="S/"
                                        {{ ($settings['currency_symbol'] ?? '') == 'S/' ? 'selected' : '' }}
                                    >
                                        S/ (Soles)
                                    </option>

                                    <option
                                        value="$"
                                        {{ ($settings['currency_symbol'] ?? '') == '$' ? 'selected' : '' }}
                                    >
                                        $ (Dólares)
                                    </option>

                                    <option
                                        value="€"
                                        {{ ($settings['currency_symbol'] ?? '') == '€' ? 'selected' : '' }}
                                    >
                                        € (Euros)
                                    </option>

                                </select>

                            </div>


                            <div class="col-md-8 col-xl-3">

                                <label class="form-label fw-bold">
                                    <i class="bi bi-chat-left-text me-1" aria-hidden="true"></i>Mensaje Pie de Ticket
                                </label>

                                <input
                                    type="text"
                                    name="ticket_footer"
                                    class="form-control"
                                    value="{{ $settings['ticket_footer'] ?? '¡Gracias por su visita!' }}"
                                >

                            </div>


                            <div class="col-md-4 col-xl-3">

                                <label class="form-label fw-bold">
                                    <i class="bi bi-bullseye me-1"></i>
                                    Meta mensual de ventas
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        {{ $settings['currency_symbol'] ?? 'S/' }}
                                    </span>

                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        name="monthly_goal"
                                        class="form-control"
                                        value="{{ old('monthly_goal', $settings['monthly_goal'] ?? 5000) }}"
                                        placeholder="5000.00"
                                    >

                                </div>

                                <small class="text-muted">
                                    Esta meta se utiliza para calcular el progreso mensual del Dashboard.
                                </small>

                            </div>

                        </div>



{{-- =================================================
                             CELEBRACIÓN DE META MENSUAL
                        ================================================== --}}

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 bg-light">

                                    <div class="form-check form-switch mb-1">

                                        <input
                                            type="hidden"
                                            name="goal_notification_enabled"
                                            value="0"
                                        >

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="goal_notification_enabled"
                                            name="goal_notification_enabled"
                                            value="1"
                                            {{ ($settings['goal_notification_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                        >

                                        <label
                                            class="form-check-label fw-bold"
                                            for="goal_notification_enabled"
                                        >
                                            <i class="bi bi-bell-fill me-1"></i>
                                            Notificación de meta
                                        </label>

                                    </div>

                                    <small class="text-muted">
                                        Muestra una felicitación cuando se alcanza o supera la meta mensual.
                                    </small>

                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100 bg-light">

                                    <div class="form-check form-switch mb-1">

                                        <input
                                            type="hidden"
                                            name="goal_confetti_enabled"
                                            value="0"
                                        >

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            role="switch"
                                            id="goal_confetti_enabled"
                                            name="goal_confetti_enabled"
                                            value="1"
                                            {{ ($settings['goal_confetti_enabled'] ?? '1') == '1' ? 'checked' : '' }}
                                        >

                                        <label
                                            class="form-check-label fw-bold"
                                            for="goal_confetti_enabled"
                                        >
                                            <i class="bi bi-stars me-1"></i>
                                            Confetis de celebración
                                        </label>

                                    </div>

                                    <small class="text-muted">
                                        Muestra confetis mientras está visible la felicitación.
                                    </small>

                                </div>
                            </div>

                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =================================================
                             APARIENCIA DEL SISTEMA
                        ================================================== --}}

                        @php

                            $currentTheme =
                                $settings['dashboard_theme']
                                ?? 'ocean-orange';


                            $themes = [

                                'ocean-orange' => [

                                    'name' =>
                                        'Océano y Naranja',

                                    'description' =>
                                        'Fresco, profesional y relacionado con el mar.',

                                    'sidebar' =>
                                        '#063970',

                                    'sidebar2' =>
                                        '#0b4f8a',

                                    'primary' =>
                                        '#ff8c00',

                                    'background' =>
                                        '#eef8fc',

                                    'colors' => [
                                        '#063970',
                                        '#0b84c6',
                                        '#ff8c00',
                                        '#eef8fc'
                                    ]

                                ],


                                'lime-blue' => [

                                    'name' =>
                                        'Verde Lima y Azul',

                                    'description' =>
                                        'Natural, fresco y con fuerte contraste visual.',

                                    'sidebar' =>
                                        '#063970',

                                    'sidebar2' =>
                                        '#0b4f8a',

                                    'primary' =>
                                        '#84cc16',

                                    'background' =>
                                        '#f2fbf3',

                                    'colors' => [
                                        '#063970',
                                        '#22a06b',
                                        '#84cc16',
                                        '#f2fbf3'
                                    ]

                                ],


                                'purple-orange' => [

                                    'name' =>
                                        'Morado y Naranja',

                                    'description' =>
                                        'Creativo, vibrante y moderno.',

                                    'sidebar' =>
                                        '#4c1d95',

                                    'sidebar2' =>
                                        '#7c3aed',

                                    'primary' =>
                                        '#ff8c00',

                                    'background' =>
                                        '#f7f2ff',

                                    'colors' => [
                                        '#4c1d95',
                                        '#7c3aed',
                                        '#ff8c00',
                                        '#f7f2ff'
                                    ]

                                ],


                                'sand-navy' => [

                                    'name' =>
                                        'Arena y Azul Marino',

                                    'description' =>
                                        'Cálido, sobrio y elegante.',

                                    'sidebar' =>
                                        '#063970',

                                    'sidebar2' =>
                                        '#0b4f8a',

                                    'primary' =>
                                        '#c98a52',

                                    'background' =>
                                        '#f7f1e9',

                                    'colors' => [
                                        '#063970',
                                        '#c98a52',
                                        '#e7c6a5',
                                        '#f7f1e9'
                                    ]

                                ],


                                'teal-amber' => [

                                    'name' =>
                                        'Teal y Ámbar',

                                    'description' =>
                                        'Minimalista, moderno y equilibrado.',

                                    'sidebar' =>
                                        '#07575b',

                                    'sidebar2' =>
                                        '#0f8b8d',

                                    'primary' =>
                                        '#f59e0b',

                                    'background' =>
                                        '#eef9f8',

                                    'colors' => [
                                        '#07575b',
                                        '#0f8b8d',
                                        '#f59e0b',
                                        '#eef9f8'
                                    ]

                                ],


                                'wine-blue' => [

                                    'name' =>
                                        'Vino y Azul',

                                    'description' =>
                                        'Premium, diferente y sofisticado.',

                                    'sidebar' =>
                                        '#791837',

                                    'sidebar2' =>
                                        '#3346a8',

                                    'primary' =>
                                        '#d94f70',

                                    'background' =>
                                        '#faf0f4',

                                    'colors' => [
                                        '#791837',
                                        '#3346a8',
                                        '#d94f70',
                                        '#faf0f4'
                                    ]

                                ]

                            ];

                        @endphp


                        <div class="theme-section mb-4">


                            <div
                                class="
                                    d-flex
                                    align-items-center
                                    justify-content-between
                                    flex-wrap
                                    gap-2
                                    mb-4
                                "
                            >

                                <div>

                                    <h5 class="fw-bold mb-1">

                                        <i class="bi bi-palette-fill me-2"></i>
                                        Tema visual del sistema

                                    </h5>


                                    <p class="text-muted small mb-0">

                                        El administrador puede elegir la apariencia del panel administrativo.

                                    </p>

                                </div>


                                <span
                                    class="
                                        badge
                                        rounded-pill
                                        bg-light
                                        text-dark
                                        border
                                        px-3
                                        py-2
                                    "
                                >

                                    <i class="bi bi-brush me-1"></i>

                                    6 temas disponibles

                                </span>

                            </div>


                            <div class="row g-3">


                                @foreach($themes as $value => $theme)


                                    <div class="col-md-6 col-xl-4">


                                        <label
                                            class="theme-option"
                                            for="theme_{{ $value }}"
                                        >


                                            <input
                                                type="radio"
                                                class="theme-radio"
                                                id="theme_{{ $value }}"
                                                name="dashboard_theme"
                                                value="{{ $value }}"
                                                data-sidebar="{{ $theme['sidebar'] }}"
                                                data-sidebar2="{{ $theme['sidebar2'] }}"
                                                data-primary="{{ $theme['primary'] }}"
                                                data-background="{{ $theme['background'] }}"
                                                {{ $currentTheme === $value ? 'checked' : '' }}
                                            >


                                            <div class="theme-card">


                                                <div class="theme-card-header">


                                                    <div>

                                                        <div class="theme-name">

                                                            {{ $theme['name'] }}

                                                        </div>


                                                        <div class="theme-description">

                                                            {{ $theme['description'] }}

                                                        </div>

                                                    </div>


                                                    <div class="theme-check">

                                                        <i class="bi bi-check-lg"></i>

                                                    </div>


                                                </div>


                                                {{-- MINI PREVIEW --}}

                                                <div class="theme-preview">


                                                    <div
                                                        class="theme-preview-sidebar"
                                                        style="
                                                            background:
                                                                linear-gradient(
                                                                    180deg,
                                                                    {{ $theme['sidebar'] }},
                                                                    {{ $theme['sidebar2'] }}
                                                                );
                                                        "
                                                    ></div>


                                                    <div
                                                        class="theme-preview-main"
                                                        style="
                                                            background:
                                                                {{ $theme['background'] }};
                                                        "
                                                    >


                                                        <div class="theme-preview-top"></div>


                                                        <div class="theme-preview-cards">


                                                            <div
                                                                class="theme-preview-mini"
                                                                style="
                                                                    border-color:
                                                                        {{ $theme['primary'] }};
                                                                "
                                                            ></div>


                                                            <div
                                                                class="theme-preview-mini"
                                                                style="
                                                                    border-color:
                                                                        {{ $theme['sidebar2'] }};
                                                                "
                                                            ></div>


                                                            <div
                                                                class="theme-preview-mini"
                                                                style="
                                                                    border-color:
                                                                        {{ $theme['primary'] }};
                                                                "
                                                            ></div>


                                                        </div>


                                                    </div>


                                                </div>


                                                {{-- PALETA --}}

                                                <div class="theme-colors">


                                                    @foreach($theme['colors'] as $color)


                                                        <span
                                                            class="theme-color"
                                                            style="
                                                                background:
                                                                    {{ $color }};
                                                            "
                                                        ></span>


                                                    @endforeach


                                                    <span class="theme-current-label">

                                                        Tema actual

                                                    </span>


                                                </div>


                                            </div>


                                        </label>


                                    </div>


                                @endforeach


                            </div>


                            {{-- =================================================
                                 PREVISUALIZACIÓN EN VIVO
                            ================================================== --}}

                            <div class="theme-live-preview">


                                <div class="theme-live-preview-header">


                                    <div>

                                        <div class="fw-bold small">

                                            Previsualización

                                        </div>


                                        <small class="text-muted">

                                            Así se verá aproximadamente el panel.

                                        </small>

                                    </div>


                                    <span
                                        class="
                                            badge
                                            bg-light
                                            text-dark
                                            border
                                        "
                                        id="themePreviewName"
                                    >

                                        {{ $themes[$currentTheme]['name'] ?? 'Océano y Naranja' }}

                                    </span>


                                </div>


                                <div
                                    class="theme-live-preview-screen"
                                    id="themePreviewScreen"
                                    style="
                                        background:
                                            {{ $themes[$currentTheme]['background'] ?? '#eef8fc' }};
                                    "
                                >


                                    <div
                                        class="theme-live-sidebar"
                                        id="themePreviewSidebar"
                                        style="
                                            background:
                                                linear-gradient(
                                                    180deg,
                                                    {{ $themes[$currentTheme]['sidebar'] ?? '#063970' }},
                                                    {{ $themes[$currentTheme]['sidebar2'] ?? '#0b4f8a' }}
                                                );
                                        "
                                    >


                                        <div
                                            class="theme-live-logo"
                                            id="themePreviewLogo"
                                            style="
                                                background:
                                                    {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                            "
                                        ></div>


                                        <div class="theme-live-menu active"
                                             id="themePreviewMenuActive"
                                             style="
                                                background:
                                                    {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                             "
                                        ></div>


                                        <div class="theme-live-menu"></div>

                                        <div class="theme-live-menu"></div>

                                        <div class="theme-live-menu"></div>


                                    </div>


                                    <div class="theme-live-content">


                                        <div class="theme-live-navbar"></div>


                                        <div class="theme-live-stats">


                                            <div
                                                class="theme-live-stat"
                                                data-theme-stat
                                                style="
                                                    border-color:
                                                        {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                                "
                                            ></div>


                                            <div
                                                class="theme-live-stat"
                                                data-theme-stat
                                                style="
                                                    border-color:
                                                        {{ $themes[$currentTheme]['sidebar2'] ?? '#0b4f8a' }};
                                                "
                                            ></div>


                                            <div
                                                class="theme-live-stat"
                                                data-theme-stat
                                                style="
                                                    border-color:
                                                        {{ $themes[$currentTheme]['primary'] ?? '#ff8c00' }};
                                                "
                                            ></div>


                                        </div>


                                    </div>


                                </div>


                            </div>


                        </div>


                        <hr class="text-muted opacity-25">


                        {{-- =================================================
                             SUNAT
                        ================================================== --}}

                        <h5 class="fw-bold text-danger mb-3">

                            <i class="bi bi-receipt-cutoff me-2"></i>

                            Facturación Electrónica · SUNAT

                        </h5>


                        @php

                            $ambiente =
                                $settings['sunat_environment']
                                ?? 'beta';


                            $tieneCert =
                                !empty(
                                    $settings['sunat_cert_path']
                                );

                        @endphp


                        <div
                            class="
                                alert
                                {{ $ambiente === 'produccion' ? 'alert-danger' : 'alert-warning' }}
                                py-2
                                mb-3
                            "
                        >


                            <strong>
                                Ambiente actual:
                            </strong>


                            {{ $ambiente === 'produccion'
                                ? 'PRODUCCIÓN (emisión real)'
                                : 'BETA (pruebas)'
                            }}


                            @if(!$tieneCert)

                                ·

                                <span class="badge bg-secondary">

                                    Usando certificado demo

                                </span>

                            @endif


                        </div>


                        <div class="row g-3 mb-3">


                            <div class="col-md-4">

                                <label class="form-label fw-bold">

                                    RUC del emisor

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    data-business-mirror="sunat_ruc"
                                    class="form-control"
                                    value="{{ $settings['sunat_ruc'] ?? '' }}"
                                    maxlength="11"
                                    pattern="\d{11}"
                                    placeholder="20000000001"
                                >

                            </div>


                            <div class="col-md-5">

                                <label class="form-label fw-bold">

                                    Razón social

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <input
                                    type="text"
                                    data-business-mirror="sunat_razon_social"
                                    class="form-control"
                                    value="{{ $settings['sunat_razon_social'] ?? '' }}"
                                    placeholder="MI EMPRESA SAC"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Nombre comercial

                                </label>


                                <input
                                    type="text"
                                    name="sunat_nombre_comercial"
                                    class="form-control"
                                    value="{{ $settings['sunat_nombre_comercial'] ?? '' }}"
                                >

                            </div>


                            <div class="col-12">

                                <label class="form-label fw-bold">

                                    Dirección fiscal

                                </label>


                                <input
                                    type="text"
                                    name="sunat_direccion_fiscal"
                                    class="form-control"
                                    value="{{ $settings['sunat_direccion_fiscal'] ?? '' }}"
                                    placeholder="AV. PRINCIPAL 123"
                                >

                            </div>


                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Ubigeo
                                </label>

                                <input
                                    type="text"
                                    name="sunat_ubigeo"
                                    class="form-control"
                                    value="{{ $settings['sunat_ubigeo'] ?? '150101' }}"
                                    maxlength="6"
                                    placeholder="150101"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">
                                    Departamento
                                </label>

                                <input
                                    type="text"
                                    name="sunat_departamento"
                                    class="form-control"
                                    value="{{ $settings['sunat_departamento'] ?? 'LIMA' }}"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">
                                    Provincia
                                </label>

                                <input
                                    type="text"
                                    name="sunat_provincia"
                                    class="form-control"
                                    value="{{ $settings['sunat_provincia'] ?? 'LIMA' }}"
                                >

                            </div>


                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Distrito
                                </label>

                                <input
                                    type="text"
                                    name="sunat_distrito"
                                    class="form-control"
                                    value="{{ $settings['sunat_distrito'] ?? 'LIMA' }}"
                                >

                            </div>


                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Urbanización
                                </label>

                                <input
                                    type="text"
                                    name="sunat_urbanizacion"
                                    class="form-control"
                                    value="{{ $settings['sunat_urbanizacion'] ?? '-' }}"
                                >

                            </div>


                        </div>


                        <div class="row g-3 mb-3">


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Ambiente

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <select
                                    name="sunat_environment"
                                    class="form-select fw-bold"
                                >

                                    <option
                                        value="beta"
                                        {{ $ambiente === 'beta' ? 'selected' : '' }}
                                    >

                                        BETA (Pruebas)

                                    </option>


                                    <option
                                        value="produccion"
                                        {{ $ambiente === 'produccion' ? 'selected' : '' }}
                                    >

                                        PRODUCCIÓN (Real)

                                    </option>

                                </select>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    IGV (%)

                                </label>


                                <input
                                    type="number"
                                    step="0.01"
                                    name="sunat_igv_rate"
                                    class="form-control"
                                    value="{{ $settings['sunat_igv_rate'] ?? '18' }}"
                                >

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Usuario SOL

                                </label>


                                <input
                                    type="text"
                                    name="sunat_sol_user"
                                    class="form-control"
                                    value="{{ $settings['sunat_sol_user'] ?? 'MODDATOS' }}"
                                >


                                <small class="text-muted">

                                    En BETA:

                                    <code>
                                        MODDATOS
                                    </code>

                                </small>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label fw-bold">

                                    Clave SOL

                                </label>


                                <input
                                    type="password"
                                    name="sunat_sol_pass"
                                    class="form-control"
                                    value="{{ $settings['sunat_sol_pass'] ?? '' }}"
                                    placeholder="••••••••"
                                >

                            </div>


                        </div>


                        <div class="row g-3 mb-3">


                            <div class="col-md-8">


                                <label class="form-label fw-bold">

                                    <i class="bi bi-shield-lock"></i>

                                    Certificado digital (.pfx)

                                </label>


                                <input
                                    type="file"
                                    name="sunat_cert_file"
                                    class="form-control"
                                    accept=".pfx,.p12"
                                >


                                @if($tieneCert)


                                    <small class="text-success">

                                        <i class="bi bi-check-circle"></i>

                                        Cargado:

                                        <code>
                                            {{ $settings['sunat_cert_path'] }}
                                        </code>

                                    </small>


                                @else


                                    <small class="text-warning">

                                        Sin certificado real.

                                        Para BETA usa el demo

                                        (<code>php artisan sunat:cert:demo</code>).

                                        Para producción sube tu

                                        <code>.pfx</code>

                                        oficial.

                                    </small>


                                @endif


                            </div>


                            <div class="col-md-4">

                                <label class="form-label fw-bold">

                                    Contraseña del .pfx

                                </label>


                                <input
                                    type="password"
                                    name="sunat_cert_password"
                                    class="form-control"
                                    value="{{ $settings['sunat_cert_password'] ?? '' }}"
                                    placeholder="(opcional, depende del cert)"
                                >

                            </div>


                        </div>


                        {{-- =================================================
                             MÉTODOS DE PAGO - QR
                        ================================================== --}}

                        <hr class="text-muted opacity-25">

                        <div class="mb-4">
                            <h5 class="fw-bold mb-1" style="color:#198754;">
                                <i class="bi bi-qr-code me-2"></i>
                                Métodos de pago
                            </h5>

                            <p class="text-muted small mb-4">
                                Configura los códigos QR que se mostrarán al cobrar mediante Yape o Plin.
                            </p>

                            <div class="row g-4">

                                {{-- YAPE --}}
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100"
                                         style="border-radius:18px !important; border-top:4px solid #742284 !important;">

                                        <div class="card-body p-4">

                                            <div class="d-flex align-items-center mb-3">
                                                <div class="d-flex align-items-center justify-content-center me-3"
                                                     style="width:46px;height:46px;border-radius:13px;background:#faf5ff;color:#742284;font-size:1.4rem;">
                                                    <i class="bi bi-qr-code"></i>
                                                </div>

                                                <div>
                                                    <div class="fw-bold fs-5" style="color:#742284;">
                                                        Yape
                                                    </div>
                                                    <small class="text-muted">
                                                        Código QR para pagos con Yape
                                                    </small>
                                                </div>
                                            </div>

                                            @if(!empty($settings['yape_qr']))
                                                <div class="text-center mb-3">
                                                    <img
                                                        src="{{ asset('storage/' . $settings['yape_qr']) }}"
                                                        alt="QR Yape"
                                                        style="
                                                            width:170px;
                                                            height:170px;
                                                            object-fit:contain;
                                                            border-radius:15px;
                                                            border:1px solid #e5e7eb;
                                                            padding:8px;
                                                            background:white;
                                                        "
                                                    >
                                                </div>

                                                <div class="text-center mb-3">
                                                    <span class="badge rounded-pill"
                                                          style="background:#faf5ff;color:#742284;">
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                        QR configurado
                                                    </span>
                                                </div>
                                            @else
                                                <div class="text-center py-4 mb-3"
                                                     style="border:2px dashed #d8b9df;border-radius:15px;background:#fcf8fd;">
                                                    <i class="bi bi-qr-code d-block mb-2"
                                                       style="font-size:2.5rem;color:#742284;"></i>
                                                    <span class="text-muted small">
                                                        Aún no se ha cargado un QR
                                                    </span>
                                                </div>
                                            @endif

                                            <label class="form-label fw-bold">
                                                {{ !empty($settings['yape_qr']) ? 'Cambiar QR' : 'Subir QR' }}
                                            </label>

                                            <input
                                                type="file"
                                                name="yape_qr"
                                                class="form-control"
                                                accept="image/png,image/jpeg,image/webp"
                                            >

                                            <small class="text-muted">
                                                PNG, JPG o WEBP. Máximo 2 MB.
                                            </small>

                                        </div>
                                    </div>
                                </div>


                                {{-- PLIN --}}
                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm h-100"
                                         style="border-radius:18px !important; border-top:4px solid #00a884 !important;">

                                        <div class="card-body p-4">

                                            <div class="d-flex align-items-center mb-3">
                                                <div class="d-flex align-items-center justify-content-center me-3"
                                                     style="width:46px;height:46px;border-radius:13px;background:#f0fdfa;color:#00a884;font-size:1.4rem;">
                                                    <i class="bi bi-qr-code"></i>
                                                </div>

                                                <div>
                                                    <div class="fw-bold fs-5" style="color:#00a884;">
                                                        Plin
                                                    </div>
                                                    <small class="text-muted">
                                                        Código QR para pagos con Plin
                                                    </small>
                                                </div>
                                            </div>

                                            @if(!empty($settings['plin_qr']))
                                                <div class="text-center mb-3">
                                                    <img
                                                        src="{{ asset('storage/' . $settings['plin_qr']) }}"
                                                        alt="QR Plin"
                                                        style="
                                                            width:170px;
                                                            height:170px;
                                                            object-fit:contain;
                                                            border-radius:15px;
                                                            border:1px solid #e5e7eb;
                                                            padding:8px;
                                                            background:white;
                                                        "
                                                    >
                                                </div>

                                                <div class="text-center mb-3">
                                                    <span class="badge rounded-pill"
                                                          style="background:#f0fdfa;color:#008b6d;">
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                        QR configurado
                                                    </span>
                                                </div>
                                            @else
                                                <div class="text-center py-4 mb-3"
                                                     style="border:2px dashed #a7e3d4;border-radius:15px;background:#f5fcfa;">
                                                    <i class="bi bi-qr-code d-block mb-2"
                                                       style="font-size:2.5rem;color:#00a884;"></i>
                                                    <span class="text-muted small">
                                                        Aún no se ha cargado un QR
                                                    </span>
                                                </div>
                                            @endif

                                            <label class="form-label fw-bold">
                                                {{ !empty($settings['plin_qr']) ? 'Cambiar QR' : 'Subir QR' }}
                                            </label>

                                            <input
                                                type="file"
                                                name="plin_qr"
                                                class="form-control"
                                                accept="image/png,image/jpeg,image/webp"
                                            >

                                            <small class="text-muted">
                                                PNG, JPG o WEBP. Máximo 2 MB.
                                            </small>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- =================================================
                             GUARDAR
                        ================================================== --}}

                        <div
                            class="
                                mt-5
                                d-flex
                                justify-content-end
                            "
                        >

                            <button
                                type="submit"
                                class="
                                    btn
                                    btn-primary
                                    px-5
                                    fw-bold
                                    shadow
                                "
                            >

                                <i class="bi bi-save me-2"></i>

                                Guardar Configuración

                            </button>

                        </div>




                        {{-- =================================================
                             INTELIGENCIA ARTIFICIAL
                        ================================================== --}}

                        <hr class="text-muted opacity-25">

                        

                    </form>


                </div>

            </div>

        </div>

    </div>

</div>


<style>
/* =========================================================
   ESTADO PERMANENTE SUNAT
   ========================================================= */

.sunat-environment-status {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;

    padding: 10px 13px;

    border: 1px solid var(--border-soft);
    border-left: 3px solid var(--primary);
    border-radius: 9px;

    background:
        color-mix(
            in srgb,
            var(--primary) 4%,
            var(--card-bg)
        );

    color: var(--text-main);

    font-size: .7rem;
    line-height: 1.4;
}

.sunat-environment-status strong {
    font-weight: 800;
}

.sunat-environment-status.sunat-beta {
    border-left-color: #d97706;
}

.sunat-environment-status.sunat-production {
    border-left-color: #dc2626;
}

.sunat-environment-status .badge {
    padding: 4px 7px;

    border-radius: 5px;

    background: var(--light-bg) !important;
    border: 1px solid var(--border-soft);

    color: var(--text-muted) !important;

    font-size: .58rem;
    font-weight: 750;
}
</style>

<style>
/* DARK MODE - ICONOS CONFIGURACION */

/* Icono principal y títulos de secciones */
html[data-color-mode="dark"] .settings-page-header > i,
html[data-color-mode="dark"] .settings-page-header h1 i,
html[data-color-mode="dark"] .settings-page-header h2 i,
html[data-color-mode="dark"] .card-header h1 i,
html[data-color-mode="dark"] .card-header h2 i,
html[data-color-mode="dark"] .card-header h3 i,
html[data-color-mode="dark"] .card-header h4 i,
html[data-color-mode="dark"] .card-header h5 i,
html[data-color-mode="dark"] .card-title i {
    color: #ffffff !important;
}

/* Iconos negros heredados dentro de Configuración */
html[data-color-mode="dark"] .settings-page-header [style*="color:#000"],
html[data-color-mode="dark"] .settings-page-header [style*="color: #000"],
html[data-color-mode="dark"] .card [style*="color:#000"],
html[data-color-mode="dark"] .card [style*="color: #000"],
html[data-color-mode="dark"] .card [style*="color:#111827"],
html[data-color-mode="dark"] .card [style*="color: #111827"],
html[data-color-mode="dark"] .card [style*="color:#1f2937"],
html[data-color-mode="dark"] .card [style*="color: #1f2937"] {
    color: #ffffff !important;
}

/* Iconos que utilizan el color principal de texto */
html[data-color-mode="dark"] .settings-page-header i[style*="var(--text-main)"],
html[data-color-mode="dark"] .card i[style*="var(--text-main)"] {
    color: #ffffff !important;
}

</style>

<style>
/* DARK MODE - TODOS LOS ICONOS ESTRUCTURALES CONFIGURACION */

/* Encabezado principal */
html[data-color-mode="dark"] .settings-page-header i {
    color: #ffffff !important;
}

/* Iconos que acompañan títulos */
html[data-color-mode="dark"] .settings-page-header i,
html[data-color-mode="dark"] .settings-page-title i,
html[data-color-mode="dark"] .card-header i,
html[data-color-mode="dark"] .card-title i,
html[data-color-mode="dark"] legend i,
html[data-color-mode="dark"] h1 i,
html[data-color-mode="dark"] h2 i,
html[data-color-mode="dark"] h3 i,
html[data-color-mode="dark"] h4 i,
html[data-color-mode="dark"] h5 i,
html[data-color-mode="dark"] h6 i {
    color: #ffffff !important;
}

/* Iconos negros colocados manualmente */
html[data-color-mode="dark"] i[style*="color:#000"],
html[data-color-mode="dark"] i[style*="color: #000"],
html[data-color-mode="dark"] i[style*="color:#111"],
html[data-color-mode="dark"] i[style*="color: #111"],
html[data-color-mode="dark"] i[style*="color:#111827"],
html[data-color-mode="dark"] i[style*="color: #111827"],
html[data-color-mode="dark"] i[style*="color:#1f2937"],
html[data-color-mode="dark"] i[style*="color: #1f2937"],
html[data-color-mode="dark"] i[style*="color:#212529"],
html[data-color-mode="dark"] i[style*="color: #212529"],
html[data-color-mode="dark"] i[style*="color:var(--text-main)"],
html[data-color-mode="dark"] i[style*="color: var(--text-main)"] {
    color: #ffffff !important;
}

/* Bootstrap text-dark aplicado a iconos */
html[data-color-mode="dark"] i.text-dark,
html[data-color-mode="dark"] .text-dark > i {
    color: #ffffff !important;
}

/* Iconos dentro de encabezados personalizados */
html[data-color-mode="dark"] [class*="section-title"] i,
html[data-color-mode="dark"] [class*="section-header"] i,
html[data-color-mode="dark"] [class*="settings-"] h3 i,
html[data-color-mode="dark"] [class*="settings-"] h4 i,
html[data-color-mode="dark"] [class*="settings-"] h5 i {
    color: #ffffff !important;
}

</style>

<style>
/* DARK MODE - ICONOS REALES SETTINGS */

/* Iconos estructurales de las secciones de Configuración */
html[data-color-mode="dark"] .bi-gear-fill,
html[data-color-mode="dark"] .bi-shop,
html[data-color-mode="dark"] .bi-globe-americas,
html[data-color-mode="dark"] .bi-clock,
html[data-color-mode="dark"] .bi-bullseye,
html[data-color-mode="dark"] .bi-bell-fill,
html[data-color-mode="dark"] .bi-stars,
html[data-color-mode="dark"] .bi-palette-fill,
html[data-color-mode="dark"] .bi-brush,
html[data-color-mode="dark"] .bi-image,
html[data-color-mode="dark"] .bi-receipt-cutoff,
html[data-color-mode="dark"] .bi-shield-lock,
html[data-color-mode="dark"] .bi-qr-code {
    color: #ffffff !important;
}

/* También cubre pseudo-elementos de Bootstrap Icons */
html[data-color-mode="dark"] .bi-gear-fill::before,
html[data-color-mode="dark"] .bi-shop::before,
html[data-color-mode="dark"] .bi-globe-americas::before,
html[data-color-mode="dark"] .bi-clock::before,
html[data-color-mode="dark"] .bi-bullseye::before,
html[data-color-mode="dark"] .bi-bell-fill::before,
html[data-color-mode="dark"] .bi-stars::before,
html[data-color-mode="dark"] .bi-palette-fill::before,
html[data-color-mode="dark"] .bi-brush::before,
html[data-color-mode="dark"] .bi-image::before,
html[data-color-mode="dark"] .bi-receipt-cutoff::before,
html[data-color-mode="dark"] .bi-shield-lock::before,
html[data-color-mode="dark"] .bi-qr-code::before {
    color: #ffffff !important;
}

</style>
@endsection


@push('scripts')

<script>

    /*
    |--------------------------------------------------------------------------
    | PREVISUALIZACIÓN DE TEMAS
    |--------------------------------------------------------------------------
    */

    const themeRadios =
        document.querySelectorAll(
            'input[name="dashboard_theme"]'
        );


    const previewScreen =
        document.getElementById(
            'themePreviewScreen'
        );


    const previewSidebar =
        document.getElementById(
            'themePreviewSidebar'
        );


    const previewLogo =
        document.getElementById(
            'themePreviewLogo'
        );


    const previewActive =
        document.getElementById(
            'themePreviewMenuActive'
        );


    const previewName =
        document.getElementById(
            'themePreviewName'
        );


    const previewStats =
        document.querySelectorAll(
            '[data-theme-stat]'
        );


    themeRadios.forEach(
        radio => {


            radio.addEventListener(
                'change',
                function () {


                    const sidebar =
                        this.dataset.sidebar;


                    const sidebar2 =
                        this.dataset.sidebar2;


                    const primary =
                        this.dataset.primary;


                    const background =
                        this.dataset.background;


                    const card =
                        this.closest(
                            '.theme-card'
                        );


                    const themeName =
                        card
                            ?.querySelector(
                                '.theme-name'
                            )
                            ?.textContent
                            ?.trim();


                    /*
                     * Fondo
                     */

                    if (previewScreen) {

                        previewScreen.style.background =
                            background;

                    }


                    /*
                     * Sidebar
                     */

                    if (previewSidebar) {

                        previewSidebar.style.background =
                            `linear-gradient(
                                180deg,
                                ${sidebar},
                                ${sidebar2}
                            )`;

                    }


                    /*
                     * Logo
                     */

                    if (previewLogo) {

                        previewLogo.style.background =
                            primary;

                    }


                    /*
                     * Menú activo
                     */

                    if (previewActive) {

                        previewActive.style.background =
                            primary;

                    }


                    /*
                     * Nombre
                     */

                    if (previewName) {

                        previewName.textContent =
                            themeName ??
                            'Tema seleccionado';

                    }


                    /*
                     * Tarjetas
                     */

                    if (
                        previewStats.length >= 3
                    ) {

                        previewStats[0]
                            .style
                            .borderColor =
                                primary;


                        previewStats[1]
                            .style
                            .borderColor =
                                sidebar2;


                        previewStats[2]
                            .style
                            .borderColor =
                                primary;

                    }


                }
            );


        }
    );

</script>

@endpush


@push('scripts')
<script>
(() => {
 const preview = document.getElementById('settingsTicketPreview');
 const fields = ['sunat_ruc','company_name','company_address','company_phone','ticket_footer','currency_symbol'];
 const value = name => document.querySelector('[name="'+name+'"]')?.value.trim() || '';
 const refresh = () => {
  preview.querySelectorAll('[data-ticket]').forEach(el => {
   const key=el.dataset.ticket;
   el.textContent=value(key) || (key==='company_name' ? 'Nombre del restaurante' : '');
  });
  document.getElementById('ticketPreviewRucRow').hidden = !value('sunat_ruc');
  preview.querySelectorAll('[data-ticket-price]').forEach(el => {el.textContent=(value('currency_symbol') || 'S/')+' '+Number(el.dataset.ticketPrice).toFixed(2)});
 };
 fields.forEach(name => { const field=document.querySelector('[name="'+name+'"]');field?.addEventListener('input',refresh);field?.addEventListener('change',refresh) });
 const logoInput=document.querySelector('[name="company_logo"]');
 let logoUrl;
 logoInput?.addEventListener('change',()=>{const file=logoInput.files?.[0];if(!file)return;if(logoUrl)URL.revokeObjectURL(logoUrl);logoUrl=URL.createObjectURL(file);['ticketPreviewLogo','companyLogoThumbnail'].forEach(id=>{const image=document.getElementById(id);if(image){image.src=logoUrl;image.hidden=false}});const placeholder=document.getElementById('companyLogoPlaceholder');if(placeholder)placeholder.hidden=true});
 refresh();
})();
</script>
@endpush



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    ['sunat_ruc', 'sunat_razon_social'].forEach(function (name) {
        const source = document.querySelector('[name="' + name + '"]');
        const mirror = document.querySelector('[data-business-mirror="' + name + '"]');
        if (!source || !mirror) return;
        mirror.value = source.value;
        source.addEventListener('input', function () { mirror.value = source.value; });
        mirror.addEventListener('input', function () { source.value = mirror.value; source.dispatchEvent(new Event('input', { bubbles: true })); });
    });
});
</script>
@endpush
