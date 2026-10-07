<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Throwable;
class SysIaController extends Controller
{
    public function assistant(Request $request) { return $this->dispatch($request, 'ia'); }
    public function chat(Request $request) { return $this->dispatch($request, 'chat'); }
    private function dispatch(Request $request, string $kind) {
        $_SESSION = $request->session()->get('sys_ia', []);
        try {
            require app_path('Services/SysIa/' . $kind . '_endpoint.php');
            return response()->json(['ok'=>false, 'error'=>'Acción inválida.'], 400);
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        } catch (Throwable $e) {
            Log::warning('SYS IA: ' . get_class($e));
            $message = $e instanceof \RuntimeException ? $e->getMessage() : 'No se pudo procesar la consulta. Revisa las migraciones y la conexión de base de datos.';
            return response()->json(['ok'=>false, 'error'=>$message], 422);
        } finally {
            $request->session()->put('sys_ia', $_SESSION);
            unset($_SESSION);
        }
    }
}
