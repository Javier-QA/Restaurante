<?php
namespace App\Services\SysIa;
use Throwable;
use RuntimeException;
use Illuminate\Http\Exceptions\HttpResponseException;
/**
 * Asistente de reportes con IA (solo administrador).
 * GET  ?action=estado|csv|esquema
 * POST (JSON + X-CSRF-Token): guardar_config|probar|preguntar|ejecutar|resumir|favorito|borrar
 */
require_once __DIR__ . '/core.php';

/** Respuesta con tipos nativos y tolerante a UTF-8 inválido */

$action = request()->query('action') ?? 'estado';
if (!usuario_actual()) ia_fail('Sesión expirada', 401);
if (!ia_es_admin()) ia_fail('Solo el administrador puede usar el asistente de IA.', 403);
$in = [];
if (request()->method() === 'POST') {
    if (!csrf_ok(request()->header('X-CSRF-Token') ?? null)) ia_fail('Token de seguridad inválido. Recargue la página.', 419);
    $in = request()->json()->all();
} elseif (!in_array($action, ['estado', 'csv', 'esquema'], true)) ia_fail('Método no permitido.', 405);

$uid = (int)auth()->id();
$db = db();
try { ia_instalar(); } catch (Throwable $e) { if ($action !== 'csv') ia_fail('No se pudieron preparar las vistas del asistente: ' . $e->getMessage(), 500); }


switch ($action) {
case 'estado':
    $c = ia_cfg();
    $f = $db->prepare('SELECT id,pregunta,titulo FROM ia_consultas WHERE usuario_id=? AND favorito=1 ORDER BY ultimo_uso DESC'); $f->execute([$uid]);
    $h = $db->prepare('SELECT id,pregunta,titulo,favorito FROM ia_consultas WHERE usuario_id=? AND favorito=0 ORDER BY ultimo_uso DESC, id DESC LIMIT 15'); $h->execute([$uid]);
    ia_out(['cfg' => ['proveedor' => $c['proveedor'], 'url' => $c['url'], 'modelo' => $c['modelo'], 'clave_puesta' => $c['clave'] !== '', 'resumen' => $c['resumen']],
        'listo' => ia_listo($c), 'presets' => IA_PRESETS, 'favoritos' => $f->fetchAll(), 'historial' => $h->fetchAll()]);

case 'guardar_config':
    $p = (string)($in['proveedor'] ?? '');
    if (!isset(IA_PRESETS[$p])) ia_fail('Proveedor no válido.');
    $url = trim((string)($in['url'] ?? '')); $modelo = trim((string)($in['modelo'] ?? ''));
    if (!preg_match('#^https?://[^\s]+$#i', $url)) ia_fail('La URL debe empezar por http:// o https://');
    if ($modelo === '' || mb_strlen($modelo) > 100) ia_fail('Indica el nombre del modelo.');
    $k = trim((string)($in['clave'] ?? ''));
    if ($k !== '' && $k !== '__borrar__' && (mb_strlen($k) < 8 || mb_strlen($k) > 250 || preg_match('/\s/', $k))) ia_fail('La clave no parece válida.');
    ia_cfg_set('ia_proveedor', $p); ia_cfg_set('ia_url', $url); ia_cfg_set('ia_modelo', $modelo);
    ia_cfg_set('ia_resumen', !empty($in['resumen']) ? '1' : '0');
    if ($k === '__borrar__') ia_cfg_set('ia_clave', ''); elseif ($k !== '') ia_cfg_set('ia_clave', ia_cifrar($k));
    ia_out([]);

case 'probar':
    $t = ia_llamar([['role' => 'user', 'content' => 'Responde solo con la palabra OK.']], 20);
    ia_out(['respuesta' => mb_substr(trim($t), 0, 60)]);

case 'preguntar':
    $pregunta = trim((string)($in['pregunta'] ?? ''));
    if (mb_strlen($pregunta) < 4) ia_fail('Escribe tu pregunta (mínimo 4 caracteres).');
    if (mb_strlen($pregunta) > 500) ia_fail('La pregunta es demasiado larga (máx. 500 caracteres).');
    $now = time(); $_SESSION['ia_hist'] = array_values(array_filter($_SESSION['ia_hist'] ?? [], fn($t) => $t > $now - 600));
    if (count($_SESSION['ia_hist']) >= 30) ia_fail('Demasiadas consultas seguidas. Espera unos minutos.', 429);
    $_SESSION['ia_hist'][] = $now;
    $previo = is_array($in['previo'] ?? null) ? $in['previo'] : null;
    $r = ia_preguntar($pregunta, $previo);
    if (isset($r['mensaje'])) ia_out(['mensaje' => $r['mensaje']]);
    $db->prepare('INSERT INTO ia_consultas (usuario_id,pregunta,titulo,sql_texto,grafico) VALUES (?,?,?,?,?)')
       ->execute([$uid, $pregunta, $r['titulo'], $r['sql'], json_encode($r['grafico'])]);
    $id = (int)$db->lastInsertId();
    $db->prepare('DELETE FROM ia_consultas WHERE usuario_id=? AND favorito=0 AND id NOT IN (SELECT id FROM (SELECT id FROM ia_consultas WHERE usuario_id=? AND favorito=0 ORDER BY id DESC LIMIT 60) x)')->execute([$uid, $uid]);
    ia_out(['id' => $id, 'favorito' => 0] + $r);

case 'ejecutar':   // volver a correr una consulta guardada, sin gastar IA
    $c = ia_consulta((int)($in['id'] ?? 0), $uid);
    $r = ia_ejecutar($c['sql_texto']);
    $db->prepare('UPDATE ia_consultas SET veces=veces+1, ultimo_uso=NOW() WHERE id=?')->execute([$c['id']]);
    $g = json_decode((string)$c['grafico'], true);
    ia_out(['id' => (int)$c['id'], 'pregunta' => $c['pregunta'], 'titulo' => $c['titulo'] ?: $c['pregunta'], 'sql' => $c['sql_texto'],
        'grafico' => ia_grafico($g, $r['columnas'], $r['filas']), 'favorito' => (int)$c['favorito']] + $r);

case 'resumir':
    if (!ia_cfg()['resumen']) ia_fail('El resumen está desactivado.');
    $c = ia_consulta((int)($in['id'] ?? 0), $uid);
    $r = ia_ejecutar($c['sql_texto']);
    if (!$r['filas']) ia_out(['resumen' => 'La consulta no devolvió resultados para ese período.']);
    ia_out(['resumen' => ia_resumir($c['pregunta'], $r['columnas'], $r['filas'])]);

case 'favorito':
    $c = ia_consulta((int)($in['id'] ?? 0), $uid); $v = !empty($in['valor']) ? 1 : 0;
    $db->prepare('UPDATE ia_consultas SET favorito=? WHERE id=?')->execute([$v, $c['id']]); ia_out(['favorito' => $v]);

case 'borrar':
    $c = ia_consulta((int)($in['id'] ?? 0), $uid);
    $db->prepare('DELETE FROM ia_consultas WHERE id=?')->execute([$c['id']]); ia_out([]);

case 'csv':
    $c = ia_consulta((int)(request()->query('id') ?? 0), $uid);
    $r = ia_ejecutar($c['sql_texto']);
    throw new HttpResponseException(response()->streamDownload(function () use ($r) {
        $o = fopen('php://output', 'w'); fwrite($o, "\xEF\xBB\xBF");
        $safe = fn($v) => is_string($v) && $v !== '' && strpos("=+-@\t\r", $v[0]) !== false ? "'" . $v : $v;
        fputcsv($o, $r['columnas'], ';');
        foreach ($r['filas'] as $f) fputcsv($o, array_map($safe, $f), ';');
        fclose($o);
    }, 'consulta_' . $c['id'] . '_' . date('Ymd') . '.csv', ['Content-Type'=>'text/csv; charset=utf-8']));

case 'esquema':
    throw new HttpResponseException(response(ia_esquema_texto(), 200, ['Content-Type'=>'text/plain; charset=utf-8']));

default: ia_fail('Acción no válida.', 400);
}
