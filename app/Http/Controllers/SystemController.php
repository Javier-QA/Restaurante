<?php

namespace App\Http\Controllers;

use App\Models\InventoryLog;
use App\Models\Order;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    public function index()
    {
        // Contamos qué vamos a borrar para informar al usuario
        $counts = [
            'orders' => Order::count(),
            'reservations' => Reservation::count(),
            'logs' => InventoryLog::count(),
        ];

        return view('system.index', compact('counts'));
    }

    public function resetData(Request $request)
    {
        $request->validate(['password' => 'required']);

        // Verificación de seguridad simple: La contraseña debe ser la del usuario actual
        if (! password_verify($request->password, auth()->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta. No se realizaron cambios.');
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Remove dependents first so reset leaves no orphaned delivery/billing records.
            foreach (['daily_summary_details', 'daily_summaries', 'credit_notes', 'deliveries', 'ia_consultas', 'ai_queries'] as $table) {
                if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }
            // 1. Borrar Ventas y Detalles
            DB::table('order_details')->truncate();
            DB::table('orders')->truncate();

            // 2. Borrar Reservas y Finanzas
            DB::table('reservations')->truncate();
            DB::table('expenses')->truncate();
            DB::table('cash_registers')->truncate();

            // 3. Borrar Kardex
            DB::table('inventory_logs')->truncate();
            DB::table('products')->update(['stock' => 0]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return back()->with('success', '¡Sistema reiniciado! Ventas, Reservas y Stock han vuelto a cero.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error crítico: '.$e->getMessage());
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    public function backup()
    {
        try {
            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', '127.0.0.1');

            $fileName = 'backup_'.date('Y-m-d_H-i-s').'.sql';
            $filePath = storage_path('app/'.$fileName);

            /*
             * Ruta de mysqldump.
             * En Laragon buscamos automáticamente el ejecutable para no
             * depender de que MySQL esté agregado al PATH de Windows.
             */
            $mysqldump = 'mysqldump';

            if (PHP_OS_FAMILY === 'Windows') {
                $laragonPaths = glob('C:/laragon/bin/mysql/*/bin/mysqldump.exe');

                if (! empty($laragonPaths)) {
                    $mysqldump = $laragonPaths[0];
                }
            }

            $passwordParam = empty($dbPass)
                ? ''
                : '--password='.escapeshellarg($dbPass);

            $command =
                escapeshellarg($mysqldump)
                .' --user='.escapeshellarg($dbUser)
                .' '.$passwordParam
                .' --host='.escapeshellarg($dbHost)
                .' '.escapeshellarg($dbName)
                .' > '.escapeshellarg($filePath);

            $output = [];
            $returnVar = null;

            exec($command, $output, $returnVar);

            if ($returnVar !== 0 || ! file_exists($filePath) || filesize($filePath) === 0) {

                if (file_exists($filePath)) {
                    @unlink($filePath);
                }

                return back()->with(
                    'error',
                    'No se pudo generar la copia de seguridad de la base de datos.'
                );
            }

            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar la copia de seguridad: '.$e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
            'password' => 'required',
        ]);

        if (! password_verify($request->password, auth()->user()->password)) {
            return back()->with('error', 'Contraseña incorrecta. No se restauró el sistema.');
        }

        try {
            $file = $request->file('backup_file');

            // Verificación simple de que es un archivo SQL
            if ($file->getClientOriginalExtension() !== 'sql') {
                return back()->with('error', 'El archivo debe ser un .sql válido.');
            }

            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST', '127.0.0.1');

            $filePath = $file->getRealPath();

            $passwordParam = empty($dbPass) ? '' : '--password='.escapeshellarg($dbPass);
            // Usa < para inyectar el archivo SQL en la BD
            $mysql = 'mysql';
            if (PHP_OS_FAMILY === 'Windows') {
                $paths = glob('C:/laragon/bin/mysql/*/bin/mysql.exe');
                if ($paths) {
                    $mysql = $paths[0];
                }
            }
            $command = escapeshellarg($mysql).' --user='.escapeshellarg($dbUser)
                .' '.$passwordParam.' --host='.escapeshellarg($dbHost)
                .' '.escapeshellarg($dbName).' < '.escapeshellarg($filePath);

            $output = [];
            $returnVar = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                return back()->with('error', 'Ocurrió un error al ejecutar la restauración en la base de datos.');
            }

            return back()->with('success', '¡El sistema ha sido restaurado exitosamente desde la copia de seguridad!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar el sistema: '.$e->getMessage());
        }
    }
}
