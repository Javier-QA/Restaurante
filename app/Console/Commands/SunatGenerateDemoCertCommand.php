<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Genera un certificado X.509 auto-firmado de prueba para SUNAT BETA.
 *
 * SUNAT BETA acepta cualquier certificado válido (no valida la cadena de
 * confianza). Para PRODUCCIÓN se debe usar un certificado emitido por una
 * Entidad de Certificación autorizada.
 *
 * Uso:
 *   php artisan sunat:cert:demo
 *   php artisan sunat:cert:demo --force   # Regenera aunque ya exista
 */
class SunatGenerateDemoCertCommand extends Command
{
    protected $signature = 'sunat:cert:demo {--force : Sobrescribe el certificado existente}';

    protected $description = 'Genera un certificado auto-firmado para pruebas en SUNAT BETA';

    public function handle(): int
    {
        if (!extension_loaded('openssl')) {
            $this->error('La extensión openssl de PHP no está habilitada.');
            return self::FAILURE;
        }

        $relPath  = 'sunat/certs/demo.pem';
        $absPath  = storage_path('app/' . $relPath);

        if (Storage::disk('local')->exists($relPath) && !$this->option('force')) {
            $this->warn("Ya existe: {$absPath}");
            $this->line('Usa --force para regenerarlo.');
            return self::SUCCESS;
        }

        $this->info('Generando certificado auto-firmado (RSA 2048, 10 años)...');

        // 1. Generar par de claves RSA 2048
        $key = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        if (!$key) {
            $this->error('No se pudo crear la clave: ' . openssl_error_string());
            return self::FAILURE;
        }

        // 2. Crear CSR con datos de la empresa demo
        $dn = [
            'countryName'            => 'PE',
            'stateOrProvinceName'    => 'Lima',
            'localityName'           => 'Lima',
            'organizationName'       => 'EMPRESA DEMO SAC',
            'organizationalUnitName' => 'Facturacion Electronica',
            'commonName'             => '20000000001',
            'emailAddress'           => 'demo@demo.pe',
        ];

        $csr = openssl_csr_new($dn, $key, ['digest_alg' => 'sha256']);
        if (!$csr) {
            $this->error('No se pudo crear el CSR: ' . openssl_error_string());
            return self::FAILURE;
        }

        // 3. Auto-firmar (10 años) con extensiones X.509 v3 correctas
        //    para firma digital (no es CA, sirve para firmar documentos)
        $x509 = openssl_csr_sign($csr, null, $key, 3650, [
            'digest_alg'       => 'sha256',
            'x509_extensions'  => 'usr_cert',
        ]);
        if (!$x509) {
            $this->error('No se pudo firmar el certificado: ' . openssl_error_string());
            return self::FAILURE;
        }

        // 4. Exportar a PEM (certificado + clave privada)
        openssl_x509_export($x509, $certPem);
        openssl_pkey_export($key, $keyPem);

        $pem = $certPem . PHP_EOL . $keyPem;

        Storage::disk('local')->put($relPath, $pem);

        $this->newLine();
        $this->info('✓ Certificado demo generado:');
        $this->line("   {$absPath}");
        $this->line('   Subject:  CN=20000000001, O=EMPRESA DEMO SAC, C=PE');
        $this->line('   Validez:  10 años');
        $this->newLine();
        $this->warn('⚠  Este certificado SOLO es válido para SUNAT BETA. ');
        $this->warn('   Para producción debes usar uno emitido por una EC autorizada.');

        return self::SUCCESS;
    }
}
