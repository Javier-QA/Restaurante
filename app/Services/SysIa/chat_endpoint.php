<?php
namespace App\Services\SysIa;
use Throwable;
use RuntimeException;
use Illuminate\Http\Exceptions\HttpResponseException;
/**
 * Chat IA (solo administrador): conversa sobre los datos y explica cómo usar el sistema.
 * GET  ?action=estado
 * POST (JSON + X-CSRF-Token): enviar|limpiar
 */
require_once __DIR__ . '/core.php';


$action = request()->query('action') ?? 'estado';
if (!usuario_actual()) ch_fail('Sesión expirada', 401);
if (!ia_es_admin()) ch_fail('Solo el administrador puede usar el chat de IA.', 403);
$in = [];
if (request()->method() === 'POST') {
    if (!csrf_ok(request()->header('X-CSRF-Token') ?? null)) ch_fail('Token de seguridad inválido. Recargue la página.', 419);
    $in = request()->json()->all();
} elseif ($action !== 'estado') ch_fail('Método no permitido.', 405);

switch ($action) {
case 'estado':
    $c = ia_cfg();
    $neg = ia_empresa();
    ch_out(['listo' => ia_listo($c), 'proveedor' => IA_PRESETS[$c['proveedor']]['nombre'], 'empresa' => $neg ?: APP_NAME, 'historial' => array_values($_SESSION['chat_hist'] ?? [])]);

case 'enviar':
    $m = trim((string)($in['mensaje'] ?? ''));
    if ($m === '') ch_fail('Escribe un mensaje.');
    if (mb_strlen($m) > 400) ch_fail('El mensaje es demasiado largo (máx. 400 caracteres).');
    if (!ia_listo()) ch_fail('Primero activa la IA con tu clave.');
    $now = time(); $_SESSION['chat_rate'] = array_values(array_filter($_SESSION['chat_rate'] ?? [], fn($t) => $t > $now - 600));
    if (count($_SESSION['chat_rate']) >= 40) ch_fail('Demasiados mensajes seguidos. Espera unos minutos.', 429);
    $_SESSION['chat_rate'][] = $now;
    ia_instalar();
    $hist = $_SESSION['chat_hist'] ?? [];
    $r = ia_chat($m, $hist);
    $hist[] = ['q' => $m, 'a' => mb_substr((string)$r['texto'], 0, 1500)];
    $_SESSION['chat_hist'] = array_slice($hist, -12);
    ch_out($r);

case 'limpiar':
    unset($_SESSION['chat_hist']); ch_out([]);

default: ch_fail('Acción no válida.', 400);
}
