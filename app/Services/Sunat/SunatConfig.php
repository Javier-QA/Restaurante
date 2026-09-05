<?php

namespace App\Services\Sunat;

use App\Models\Setting;

/**
 * Lee y centraliza la configuración SUNAT almacenada en la tabla settings.
 */
class SunatConfig
{
    /** Endpoints SOAP de SUNAT */
    public const ENDPOINT_BETA       = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
    public const ENDPOINT_PRODUCCION = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';

    /** Endpoints para Resumen Diario / Comunicación de Baja (envíoResumen) */
    public const ENDPOINT_RC_BETA       = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';
    public const ENDPOINT_RC_PRODUCCION = 'https://www.sunat.gob.pe/ol-ti-itcpfegem/billService';

    /** Certificado público de demo (provisto por SUNAT/Greenter para BETA) */
    public const DEMO_CERT_PATH = null; // Si null, usamos el del paquete greenter (ver Service)

    private array $settings;

    public function __construct(?array $settings = null)
    {
        $this->settings = $settings ?? Setting::pluck('value', 'key')->toArray();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    public function ruc(): string
    {
        return (string) $this->get('sunat_ruc', '20000000001');
    }

    public function razonSocial(): string
    {
        return (string) $this->get('sunat_razon_social', 'EMPRESA DEMO SAC');
    }

    public function nombreComercial(): string
    {
        return (string) $this->get('sunat_nombre_comercial', $this->razonSocial());
    }

    public function direccion(): string
    {
        return (string) $this->get('sunat_direccion_fiscal', '-');
    }

    public function ubigeo(): string
    {
        return (string) $this->get('sunat_ubigeo', '150101');
    }

    public function departamento(): string
    {
        return (string) $this->get('sunat_departamento', 'LIMA');
    }

    public function provincia(): string
    {
        return (string) $this->get('sunat_provincia', 'LIMA');
    }

    public function distrito(): string
    {
        return (string) $this->get('sunat_distrito', 'LIMA');
    }

    public function urbanizacion(): string
    {
        return (string) $this->get('sunat_urbanizacion', '-');
    }

    public function codigoPais(): string
    {
        return (string) $this->get('sunat_codigo_pais', 'PE');
    }

    public function igvRate(): float
    {
        return (float) $this->get('sunat_igv_rate', 18);
    }

    public function igvFactor(): float
    {
        return $this->igvRate() / 100;
    }

    public function isProduction(): bool
    {
        return $this->get('sunat_environment', 'beta') === 'produccion';
    }

    public function endpoint(): string
    {
        return $this->isProduction() ? self::ENDPOINT_PRODUCCION : self::ENDPOINT_BETA;
    }

    public function endpointResumen(): string
    {
        return $this->isProduction() ? self::ENDPOINT_RC_PRODUCCION : self::ENDPOINT_RC_BETA;
    }

    /** Usuario SOL: para BETA es 20XXXXXXXXXMODDATOS, en PROD el usuario secundario real. */
    public function solUser(): string
    {
        $user = (string) $this->get('sunat_sol_user', 'MODDATOS');

        if (!$this->isProduction() && !str_starts_with($user, $this->ruc())) {
            return $this->ruc() . $user;
        }

        return $user;
    }

    public function solPass(): string
    {
        return (string) $this->get('sunat_sol_pass', 'MODDATOS');
    }

    public function certPath(): ?string
    {
        $path = $this->get('sunat_cert_path');
        return $path ? storage_path('app/' . $path) : null;
    }

    public function certPassword(): string
    {
        return (string) $this->get('sunat_cert_password', '');
    }
}
