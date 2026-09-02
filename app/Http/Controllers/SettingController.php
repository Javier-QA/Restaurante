<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        return view(
            'settings.index',
            compact('settings', 'timezones')
        );
    }

    public function update(Request $request)
    {
        // Validación de las imágenes
        $request->validate([
            'company_logo' => 'nullable|image|max:2048',
            'yape_qr' => 'nullable|image|max:2048',
            'plin_qr' => 'nullable|image|max:2048',
        ]);

        // Datos de configuración que no son archivos
        $data = $request->except([
            '_token',
            'company_logo',
            'yape_qr',
            'plin_qr',
        ]);

        // =========================================================
        // GUARDAR CONFIGURACIONES DE TEXTO
        // =========================================================

        foreach ($data as $key => $value) {

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }


        // =========================================================
        // GUARDAR LOGO
        // =========================================================

        if ($request->hasFile('company_logo')) {

            $oldLogo = Setting::where(
                'key',
                'company_logo'
            )->value('value');

            if ($oldLogo) {

                Storage::disk('public')->delete(
                    $oldLogo
                );
            }

            $path = $request
                ->file('company_logo')
                ->store('settings', 'public');

            Setting::updateOrCreate(
                ['key' => 'company_logo'],
                ['value' => $path]
            );
        }


        // =========================================================
        // GUARDAR QR DE YAPE
        // =========================================================

        if ($request->hasFile('yape_qr')) {

            $oldYapeQr = Setting::where(
                'key',
                'yape_qr'
            )->value('value');

            if ($oldYapeQr) {

                Storage::disk('public')->delete(
                    $oldYapeQr
                );
            }

            $path = $request
                ->file('yape_qr')
                ->store('settings/qr', 'public');

            Setting::updateOrCreate(
                ['key' => 'yape_qr'],
                ['value' => $path]
            );
        }


        // =========================================================
        // GUARDAR QR DE PLIN
        // =========================================================

        if ($request->hasFile('plin_qr')) {

            $oldPlinQr = Setting::where(
                'key',
                'plin_qr'
            )->value('value');

            if ($oldPlinQr) {

                Storage::disk('public')->delete(
                    $oldPlinQr
                );
            }

            $path = $request
                ->file('plin_qr')
                ->store('settings/qr', 'public');

            Setting::updateOrCreate(
                ['key' => 'plin_qr'],
                ['value' => $path]
            );
        }


        return redirect()
            ->back()
            ->with(
                'success',
                'Configuración actualizada correctamente.'
            );
    }
}