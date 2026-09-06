<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        // Lista de zonas horarias comunes en Latinoamérica para el select
        $timezones = [
            'America/Lima' => '(UTC-05:00) Lima, Bogotá, Quito',
            'America/Caracas' => '(UTC-04:00) Caracas',
            'America/La_Paz' => '(UTC-04:00) La Paz',
            'America/Santiago' => '(UTC-03:00) Santiago',
            'America/Argentina/Buenos_Aires' => '(UTC-03:00) Buenos Aires',
            'America/Montevideo' => '(UTC-03:00) Montevideo',
            'America/Mexico_City' => '(UTC-06:00) Ciudad de México',
            'America/Tijuana' => '(UTC-08:00) Tijuana',
            'America/New_York' => '(UTC-05:00) Hora del Este (EE.UU.)',
            'Europe/Madrid' => '(UTC+01:00) Madrid',
            'UTC' => '(UTC+00:00) Tiempo Universal Coordinado'
        ];

        return view('settings.index', compact('settings', 'timezones'));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', 'company_logo', 'sunat_cert_file', 'yape_qr', 'plin_qr']);

        // 1. Guardar textos (Nombre, Timezone, Moneda, config SUNAT, etc.)
        foreach ($data as $key => $value) {
            if (is_null($value)) continue;
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 2. Guardar Logo
        if ($request->hasFile('company_logo')) {
            $request->validate(['company_logo' => 'image|max:2048']);
            $oldLogo = Setting::where('key', 'company_logo')->value('value');
            if ($oldLogo) Storage::disk('public')->delete($oldLogo);
            $path = $request->file('company_logo')->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'company_logo'], ['value' => $path]);
        }

        // 3. Guardar certificado SUNAT (.pfx)
        if ($request->hasFile('sunat_cert_file')) {
            $request->validate([
                'sunat_cert_file' => 'file|max:512',
            ]);

            $ext = strtolower($request->file('sunat_cert_file')->getClientOriginalExtension());
            if (!in_array($ext, ['pfx', 'p12'])) {
                return redirect()->back()->with('error', 'El certificado debe ser un archivo .pfx o .p12');
            }

            // Borrar cert anterior si existía y NO es el demo
            $oldCert = Setting::where('key', 'sunat_cert_path')->value('value');
            if ($oldCert
                && !str_contains($oldCert, 'demo')
                && Storage::disk('local')->exists($oldCert)) {
                Storage::disk('local')->delete($oldCert);
            }

            // Guardar el nuevo en storage/app/sunat/certs/
            $filename = 'cert_' . date('Ymd_His') . '.' . $ext;
            $request->file('sunat_cert_file')->storeAs('sunat/certs', $filename, 'local');

            Setting::updateOrCreate(
                ['key' => 'sunat_cert_path'],
                ['value' => 'sunat/certs/' . $filename]
            );
        }

        // 4. Guardar QR de Yape
        if ($request->hasFile('yape_qr')) {
            $request->validate([
                'yape_qr' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
            ]);

            $oldYape = Setting::where('key', 'yape_qr')->value('value');

            if ($oldYape) {
                Storage::disk('public')->delete($oldYape);
            }

            $path = $request->file('yape_qr')->store('settings', 'public');

            Setting::updateOrCreate(
                ['key' => 'yape_qr'],
                ['value' => $path]
            );
        }

        // 5. Guardar QR de Plin
        if ($request->hasFile('plin_qr')) {
            $request->validate([
                'plin_qr' => 'image|mimes:jpeg,jpg,png,webp|max:2048',
            ]);

            $oldPlin = Setting::where('key', 'plin_qr')->value('value');

            if ($oldPlin) {
                Storage::disk('public')->delete($oldPlin);
            }

            $path = $request->file('plin_qr')->store('settings', 'public');

            Setting::updateOrCreate(
                ['key' => 'plin_qr'],
                ['value' => $path]
            );
        }
        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}