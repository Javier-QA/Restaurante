<?php

namespace App\Services\SysIa;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;

/**
 * Núcleo del Asistente IA y del Chat IA de SaborPOS.
 *
 * Seguridad (invariante):
 *  - La IA nunca ve tablas reales: solo vistas de lectura v_ia_* (sin usuarios ni contraseñas).
 *  - El SQL que propone se valida (solo SELECT/WITH) y se ejecuta en una conexión aparte,
 *    dentro de START TRANSACTION READ ONLY, con límite de tiempo y de filas. Siempre ROLLBACK.
 *  - La clave de API se guarda cifrada (AES-256-GCM, config/secret.key) y nunca vuelve al navegador.
 */
require_once __DIR__.'/bridge.php';

const IA_MAX_FILAS = 500;

const IA_PRESETS = [
    'gemini' => ['nombre' => 'Google Gemini (gratis)', 'url' => 'https://generativelanguage.googleapis.com/v1beta/openai', 'modelo' => 'gemini-3.5-flash-lite', 'clave' => true,
        'ayuda' => 'Gratis con tu cuenta de Google. Crea la clave en aistudio.google.com/apikey. Si da error 404, prueba el modelo gemini-3.5-flash.'],
    'groq' => ['nombre' => 'Groq', 'url' => 'https://api.groq.com/openai/v1', 'modelo' => 'llama-3.3-70b-versatile', 'clave' => true,
        'ayuda' => 'Muy rápido y con plan gratuito. Clave en console.groq.com/keys.'],
    'openrouter' => ['nombre' => 'OpenRouter', 'url' => 'https://openrouter.ai/api/v1', 'modelo' => 'meta-llama/llama-3.3-70b-instruct:free', 'clave' => true,
        'ayuda' => 'Muchos modelos (varios gratuitos con sufijo :free). Clave en openrouter.ai/keys.'],
    'ollama' => ['nombre' => 'Ollama (local, sin internet)', 'url' => 'http://localhost:11434/v1', 'modelo' => 'qwen2.5:7b', 'clave' => false,
        'ayuda' => 'Corre en tu propia PC: los datos no salen a internet. Requiere tener Ollama instalado y el modelo descargado.'],
    'otro' => ['nombre' => 'Otro (compatible con OpenAI)', 'url' => '', 'modelo' => '', 'clave' => true,
        'ayuda' => 'Cualquier servicio con API compatible con /chat/completions.'],
];

/* ------------------------------------------------------------------ */
/*  Permiso: solo el administrador (rol con acceso total) */
/* ------------------------------------------------------------------ */
function ia_es_admin(): bool
{
    return es_admin();
}

/* ------------------------------------------------------------------ */
/*  Configuración (tabla configuracion, claves ia_*) */
/* ------------------------------------------------------------------ */
function ia_cfg_get(string $k, string $def = ''): string
{
    $s = db()->prepare('SELECT valor FROM ia_configuracion WHERE clave=?');
    $s->execute([$k]);
    $v = $s->fetchColumn();

    return $v === false || $v === null ? $def : (string) $v;
}
function ia_cfg_set(string $k, string $v): void
{
    db()->prepare('INSERT INTO ia_configuracion (clave,valor) VALUES (?,?) ON DUPLICATE KEY UPDATE valor=VALUES(valor)')->execute([$k, $v]);
}

function ia_cfg(): array
{
    $p = ia_cfg_get('ia_proveedor', 'gemini');
    if (! isset(IA_PRESETS[$p])) {
        $p = 'gemini';
    }
    $pre = IA_PRESETS[$p];
    $clave = ia_cfg_get('ia_clave');

    return [
        'proveedor' => $p,
        'url' => ia_cfg_get('ia_url', $pre['url']) ?: $pre['url'],
        'modelo' => ia_cfg_get('ia_modelo', $pre['modelo']) ?: $pre['modelo'],
        'clave' => $clave !== '' ? ia_descifrar($clave) : '',
        'resumen' => ia_cfg_get('ia_resumen', '1') === '1',
    ];
}
function ia_listo(?array $c = null): bool
{
    $c = $c ?? ia_cfg();

    return $c['url'] !== '' && $c['modelo'] !== '' && (! IA_PRESETS[$c['proveedor']]['clave'] || $c['clave'] !== '');
}

/* ------------------------------------------------------------------ */
/*  Cifrado de la clave de API */
/* ------------------------------------------------------------------ */
function ia_cifrar(string $txt): string
{
    return \Illuminate\Support\Facades\Crypt::encryptString($txt);
}
function ia_descifrar(string $enc): string
{
    try {
        return \Illuminate\Support\Facades\Crypt::decryptString($enc);
    } catch (Throwable $e) {
        throw new RuntimeException('No se pudo descifrar la clave de IA. Vuelve a guardarla en Configurar IA.');
    }
}

/* ------------------------------------------------------------------ */
/*  Vistas de lectura que ve la IA */
/* ------------------------------------------------------------------ */
function ia_vistas(): array
{
    return require __DIR__.'/views.php';
}
function ia_instalar(bool $force = false): void
{
    if (! \Illuminate\Support\Facades\Schema::hasTable('ia_consultas')) {
        throw new RuntimeException('Ejecuta php artisan migrate para instalar el Chatbot y el Asistente IA.');
    }
}

/** Texto con vistas, descripción y columnas (se inyecta en los prompts y se muestra al usuario). */
function ia_esquema_texto(): string
{
    $db = db();
    $o = '';
    foreach (ia_vistas() as $n => $v) {
        $cols = [];
        foreach ($db->query("SHOW COLUMNS FROM $n")->fetchAll() as $c) {
            $cols[] = $c['Field'];
        }
        $o .= "$n(".implode(', ', $cols).")\n  → ".$v['desc']."\n";
    }

    return rtrim($o);
}

/* ------------------------------------------------------------------ */
/*  Llamada al modelo (API compatible con OpenAI) */
/* ------------------------------------------------------------------ */
function ia_llamar(array $mensajes, int $maxTokens = 900): string
{
    $c = ia_cfg();
    if (! ia_listo($c)) {
        throw new RuntimeException('La IA no está configurada. Pulsa «Configurar IA» e ingresa tu clave.');
    }
    if (! function_exists('curl_init')) {
        throw new RuntimeException('Falta la extensión cURL de PHP (actívala en php.ini: extension=curl).');
    }
    $key = 'sys-ia-provider:'.auth()->id();
    if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 20)) {
        throw new RuntimeException('Demasiadas consultas a la IA. Espera un minuto.');
    }
    \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
    $h = ['Content-Type: application/json'];
    if ($c['clave'] !== '') {
        $h[] = 'Authorization: Bearer '.$c['clave'];
    }
    $ch = curl_init(rtrim($c['url'], '/').'/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_POST => true, CURLOPT_HTTPHEADER => $h, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 60, CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_POSTFIELDS => json_encode(['model' => $c['modelo'], 'messages' => $mensajes, 'temperature' => 0, 'max_tokens' => $maxTokens], JSON_UNESCAPED_UNICODE),
    ]);
    $cafile = ini_get('curl.cainfo') ?: ini_get('openssl.cafile');
    if (! $cafile) {
        $loc = storage_path('app/cacert.pem');
        if (is_file($loc)) {
            curl_setopt($ch, CURLOPT_CAINFO, $loc);
        }
    }
    $res = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($res === false) {
        if (stripos($err, 'SSL') !== false) {
            throw new RuntimeException('Error de certificado SSL al conectar con la IA. En Laragon revisa curl.cainfo en php.ini. Detalle: '.$err);
        }
        throw new RuntimeException('No se pudo conectar con el proveedor de IA ('.$err.'). Revisa tu conexión a internet y la URL.');
    }
    $d = json_decode($res, true);
    if ($code >= 400) {
        $det = is_array($d) ? ($d['error']['message'] ?? ($d[0]['error']['message'] ?? '')) : '';
        $msg = match (true) {
            $code === 401 || $code === 403 => 'La clave de la IA no es válida o no tiene permiso. Revísala en «Configurar IA».',
            $code === 429 => 'Se alcanzó el límite gratuito del proveedor de IA. Espera un minuto e inténtalo otra vez.',
            $code === 404 => 'No se encontró el modelo o la URL de la IA. Revisa el nombre del modelo (p. ej. gemini-3.5-flash-lite o gemini-3.5-flash) y la URL.',
            $code >= 500 => 'El proveedor de IA tiene problemas en este momento. Inténtalo en unos minutos.',
            default => 'El proveedor de IA rechazó la petición ('.$code.').',
        };
        throw new RuntimeException($msg.($det !== '' ? ' ['.mb_substr($det, 0, 160).']' : ''));
    }
    $t = $d['choices'][0]['message']['content'] ?? null;
    if (! is_string($t) || trim($t) === '') {
        throw new RuntimeException('La IA devolvió una respuesta vacía. Inténtalo de nuevo.');
    }

    return $t;
}

function ia_extraer_json(string $t): ?array
{
    $t = trim($t);
    $t = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $t);
    $d = json_decode($t, true);
    if (is_array($d)) {
        return $d;
    }
    $a = strpos($t, '{');
    $b = strrpos($t, '}');
    if ($a !== false && $b > $a) {
        $d = json_decode(substr($t, $a, $b - $a + 1), true);
        if (is_array($d)) {
            return $d;
        }
    }

    return null;
}

/* ------------------------------------------------------------------ */
/*  Validación y ejecución segura del SQL */
/* ------------------------------------------------------------------ */
function ia_tablas_reales(): array
{
    static $t = null;
    if ($t === null) {
        $t = [];
        foreach (db()->query('SHOW FULL TABLES')->fetchAll(PDO::FETCH_NUM) as $r) {
            if (strncmp($r[0], 'v_ia_', 5) !== 0) {
                $t[] = strtolower($r[0]);
            }
        }
    }

    return $t;
}

/** Devuelve el SQL limpio o lanza una excepción con el motivo del rechazo. */
function ia_validar_sql(string $sql): string
{
    $s = trim($sql);
    $s = rtrim($s, " \t\r\n;");
    if ($s === '' || mb_strlen($s) > 4000) {
        throw new RuntimeException('La consulta está vacía o es demasiado larga.');
    }
    // Literales de texto fuera del análisis estructural
    $t = preg_replace("/'(?:[^'\\\\]|\\\\.|'')*'/s", '0', $s);
    $t = preg_replace('/"(?:[^"\\\\]|\\\\.|"")*"/s', '0', $t);
    $t = str_replace('`', '', $t);
    if (strpbrk($t, "'\"") !== false) {
        throw new RuntimeException('Consulta rechazada: comillas sin cerrar.');
    }
    if (preg_match('/;|--|\/\*|\*\/|#|@/', $t)) {
        throw new RuntimeException('Consulta rechazada: solo se permite una sentencia SELECT, sin comentarios ni variables.');
    }
    if (! preg_match('/^\s*(select|with)\b/i', $t)) {
        throw new RuntimeException('Consulta rechazada: debe empezar por SELECT.');
    }
    if (preg_match('/\bHOUR\s*\(\s*(?:[a-z_][a-z0-9_]*\s*\.\s*)?fecha\s*\)/i', $t)) {
        throw new RuntimeException('Consulta rechazada: fecha no contiene hora; usa la columna hora para agrupar ventas por horario.');
    }
    $prohibidas = 'insert|update|delete|replace|drop|alter|create|truncate|rename|grant|revoke|call|execute|prepare|deallocate|handler|into|outfile|dumpfile|load_file|load|sleep|benchmark|get_lock|release_lock|master_pos_wait|'
        .'information_schema|performance_schema|mysql|sys|user|current_user|session_user|system_user|database|schema|version|connection_id|row_count|found_rows|last_insert_id|recursive|set|use|show|lock|unlock|procedure|analyse|for|describe|explain|optimize|repair|flush|kill|shutdown';
    if (preg_match('/\b('.$prohibidas.')\b/i', $t, $m)) {
        throw new RuntimeException('Consulta rechazada: no se permite «'.strtolower($m[1]).'».');
    }
    if (preg_match('/\b'.preg_quote((string) config('database.connections.'.config('database.default').'.database'), '/').'\b/i', $t)) {
        throw new RuntimeException('Consulta rechazada: no uses el nombre de la base de datos.');
    }
    foreach (ia_tablas_reales() as $n) {
        $sin = preg_replace('/\bas\s+`?'.preg_quote($n, '/').'`?\b/i', ' ', $t);   // un alias con ese nombre es inofensivo
        if (preg_match('/\b'.preg_quote($n, '/').'\b/i', $sin)) {
            throw new RuntimeException('Consulta rechazada: solo puedes usar las vistas v_ia_*, no la tabla «'.$n.'».');
        }
    }

    return $s;
}

/** Conexión aparte (no comparte sesión con el resto del sistema), sin buffer. */
function ia_pdo(): PDO
{
    $name = config('database.default');
    $cfg = config('database.connections.'.$name);
    if (($cfg['driver'] ?? '') !== 'mysql') {
        throw new RuntimeException('El asistente requiere MySQL o MariaDB.');
    }
    config(['database.connections.sys_ia_read' => $cfg]);
    \Illuminate\Support\Facades\DB::purge('sys_ia_read');
    $p = \Illuminate\Support\Facades\DB::connection('sys_ia_read')->getPdo();
    $p->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
    // Reject aggregate queries that silently combine a random hour with all orders.
    $p->exec("SET SESSION sql_mode = CONCAT_WS(',', NULLIF(@@SESSION.sql_mode, ''), 'ONLY_FULL_GROUP_BY')");
    try {
        $p->exec("SET time_zone = '-05:00'");
    } catch (Throwable $e) {
    }

    return $p;
}

/** Valida y ejecuta. Devuelve ['columnas','filas','truncado','ms']. */
function ia_ejecutar(string $sql): array
{
    $sql = ia_validar_sql($sql);
    ia_instalar();
    $p = ia_pdo();
    $enTx = false;
    try {
        try {
            $p->exec('SET SESSION MAX_EXECUTION_TIME=8000');
        } catch (Throwable $e) {
        }   // MySQL 5.7.8+
        try {
            $p->exec('SET SESSION max_statement_time=8');
        } catch (Throwable $e) {
        }      // MariaDB
        $p->exec('START TRANSACTION READ ONLY');
        $enTx = true;
        $t0 = microtime(true);
        $st = $p->query($sql);
        $cols = [];
        $num = [];
        for ($i = 0; $i < $st->columnCount(); $i++) {
            $m = $st->getColumnMeta($i);
            $cols[] = $m['name'];
            $num[] = in_array($m['native_type'] ?? '', ['NEWDECIMAL', 'DECIMAL', 'LONG', 'LONGLONG', 'SHORT', 'TINY', 'INT24', 'FLOAT', 'DOUBLE'], true);
        }
        $filas = [];
        $trunc = false;
        while (($r = $st->fetch(PDO::FETCH_NUM)) !== false) {
            if (count($filas) >= IA_MAX_FILAS) {
                $trunc = true;
                break;
            }
            foreach ($r as $i => $v) {
                if ($v !== null && $num[$i] && is_numeric($v)) {
                    $r[$i] = $v + 0;
                }
            }
            $filas[] = $r;
        }
        $st->closeCursor();
        $ms = (int) round((microtime(true) - $t0) * 1000);

        return ['columnas' => $cols, 'filas' => $filas, 'truncado' => $trunc, 'ms' => $ms];
    } catch (PDOException $e) {
        $m = $e->getMessage();
        if (stripos($m, 'execution time') !== false || stripos($m, 'max_statement_time') !== false || stripos($m, 'interrupted') !== false) {
            throw new RuntimeException('La consulta tardó demasiado (límite 8 s). Pide algo más específico.');
        }
        throw new RuntimeException('Error SQL: '.preg_replace('/^SQLSTATE\[[^\]]+\]:\s*/', '', $m));
    } finally {
        if ($enTx) {
            try {
                $p->exec('ROLLBACK');
            } catch (Throwable $e) {
            }
        }
    }
}

/** Gráfico saneado: el tipo y las columnas x/y deben existir en el resultado. */
function ia_grafico($g, array $cols, array $filas): array
{
    $none = ['tipo' => 'none', 'x' => null, 'y' => null];
    if (count($filas) < 2 || count($cols) < 2) {
        return $none;
    }
    $g = is_array($g) ? $g : [];
    $tipo = in_array($g['tipo'] ?? '', ['bar', 'line', 'pie', 'none'], true) ? $g['tipo'] : 'bar';
    if ($tipo === 'none') {
        return $none;
    }
    $esNum = fn ($i) => is_int($filas[0][$i]) || is_float($filas[0][$i]);
    $x = array_search($g['x'] ?? '', $cols, true);
    $y = array_search($g['y'] ?? '', $cols, true);
    if ($x === false) {
        $x = 0;
        foreach ($cols as $i => $_) {
            if (! $esNum($i)) {
                $x = $i;
                break;
            }
        }
    }
    if ($y === false || ! $esNum($y)) {
        $y = false;
        foreach ($cols as $i => $_) {
            if ($i !== $x && $esNum($i)) {
                $y = $i;
                break;
            }
        }
    }
    if ($y === false) {
        return $none;
    }
    if ($tipo === 'pie' && count($filas) > 12) {
        $tipo = 'bar';
    }

    return ['tipo' => $tipo, 'x' => $cols[$x], 'y' => $cols[$y]];
}

/* ------------------------------------------------------------------ */
/*  Prompts */
/* ------------------------------------------------------------------ */
function ia_fecha_texto(): string
{
    $dias = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];

    return date('Y-m-d').' ('.$dias[(int) date('w')].')';
}

function ia_reglas(): string
{
    return 'REGLAS:
- Hoy es '.ia_fecha_texto().'. La semana empieza el lunes. «Este mes» = desde el día 1 del mes actual; «ayer», «semana pasada», etc. se calculan con CURDATE() y DATE_SUB. Zona horaria de Lima.
- Moneda: soles ('.MONEDA.'). Los precios incluyen IGV '.cfg('igv', 18).' %.
- INGRESOS / VENTAS: usa v_ia_sys_ventas con estado = \'pagado\' y agrupa por la columna fecha. Los pedidos anulados, abiertos o en cocina no son ingresos. «Ticket promedio» = AVG(total). «Venta sin IGV» = total - impuesto.
- PLATOS MÁS VENDIDOS: SUM(cantidad) en v_ia_sys_detalle_ventas con estado_pedido = \'pagado\' AND estado_item <> \'anulado\'. «Más rentables» = SUM(utilidad).
- Tipos de pedido: salon, llevar, delivery. Métodos de pago: efectivo, tarjeta, yape, plin, transferencia. «Mozo» = vendedor/atendió. «Horas pico»: usa directamente la columna hora de v_ia_sys_ventas, agrupa GROUP BY hora y ordena COUNT(*) DESC si se piden más pedidos, o SUM(total) DESC si se pide mayor ingreso. Nunca uses HOUR(fecha): fecha es DATE y pierde la hora; HOUR(fecha) devuelve cero. Muestra el intervalo HH:00–HH:59 y el período consultado. Si no indican período, usa todo el historial y dilo.
- No hay compras ni proveedores registrados en este esquema. No inventes esas tablas. Las vistas de inventario incluyen unidad, stock_minimo y stock_bajo. La fecha de venta usa paid_at; para cobros antiguos se conserva una fecha estimada. COUNT de productos cuenta registros de productos distintos; SUM(stock) cuenta existencias. Nunca describas un COUNT de productos como unidades disponibles.
- UNIDAD DE MEDIDA: cuando pregunten por productos en unidades, kilos, gramos, litros, paquetes o cajas, usa la columna unidad de v_ia_sys_insumos. Códigos: und, kg, g, lt, ml, paq, caja. Para unidades filtra unidad = \'und\'; nunca busques unid, kg o gramos en el nombre del producto. Para listar productos de venta y su categoría/precio, une v_ia_sys_insumos i con v_ia_sys_productos p ON p.id=i.id y filtra i.activo=1 AND p.activo=1. Devuelve nombre, unidad y stock; COUNT cuenta productos distintos, no unidades de stock. Una porción llamada 10 unid. no indica la unidad de inventario ni el stock disponible.
- Los valores de estado/tipo van en MINÚSCULAS, tal como aparecen en las descripciones.
- Usa SOLO las vistas y columnas listadas (nunca tablas reales). Sintaxis MySQL/MariaDB. Una sola sentencia SELECT (puede usar WITH), sin punto y coma ni comentarios.
- Alias de columnas en español, en minúscula con guion bajo (p. ej. total_ventas, cantidad_vendida). No uses como alias los nombres de tablas reales (productos, clientes, pedidos, compras, gastos, mesas, reservas…).
- Rankings: ORDER BY ... DESC con LIMIT 10 salvo que pidan otra cantidad. Máximo '.IA_MAX_FILAS.' filas. Redondea dinero con ROUND(x,2).
- Si pides una serie por día/mes, ordénala cronológicamente (ASC). No inventes datos ni columnas.';
}

function ia_prompt_sistema(): string
{
    return 'Eres el analista de datos de un restaurante (sistema Restaurante). Conviertes preguntas en español en UNA consulta SQL de solo lectura sobre estas vistas:

'.ia_esquema_texto().'

'.ia_reglas().'

Responde SOLO con un JSON válido (sin markdown ni texto adicional) con esta forma:
{"titulo":"título corto del reporte","sql":"SELECT ...","grafico":{"tipo":"bar|line|pie|none","x":"columna_eje_x","y":"columna_numerica"},"mensaje":""}
- grafico.tipo: line para series de tiempo, pie para participación (máx. 8 partes), bar para rankings y comparaciones, none si no aplica (un solo dato o tabla de detalle).
- Si la pregunta no se puede responder con estos datos, deja "sql" vacío y explica en "mensaje" (en español, breve) qué sí puedes consultar.
- Si recibes una consulta anterior, la nueva pregunta es de seguimiento: modifica esa consulta (filtros, agrupación, período) en lugar de empezar de cero.';
}

/** Genera y ejecuta una consulta a partir de una pregunta. Hasta 2 intentos, devolviendo el error SQL a la IA. */
function ia_preguntar(string $pregunta, ?array $previo = null): array
{
    $user = $pregunta;
    if ($previo && ! empty($previo['sql'])) {
        $user = "Consulta anterior\nPregunta: ".mb_substr((string) ($previo['pregunta'] ?? ''), 0, 300)."\nSQL: ".mb_substr((string) $previo['sql'], 0, 2500)."\n\nNueva pregunta (seguimiento): ".$pregunta;
    }
    $msgs = [['role' => 'system', 'content' => ia_prompt_sistema()], ['role' => 'user', 'content' => $user]];
    $ultimo = '';
    for ($i = 0; $i < 2; $i++) {
        $raw = ia_llamar($msgs, 1100);
        $j = ia_extraer_json($raw);
        $msgs[] = ['role' => 'assistant', 'content' => $raw];
        if (! $j) {
            $ultimo = 'respuesta sin formato JSON';
            $msgs[] = ['role' => 'user', 'content' => 'Tu respuesta no fue un JSON válido. Responde SOLO con el JSON indicado.'];

            continue;
        }
        $sql = trim((string) ($j['sql'] ?? ''));
        if ($sql === '') {
            return ['mensaje' => trim((string) ($j['mensaje'] ?? '')) ?: 'No pude responder esa pregunta con los datos disponibles. Prueba con ventas, productos, insumos, compras, gastos, clientes o reservas.'];
        }
        try {
            $r = ia_ejecutar($sql);
        } catch (RuntimeException $e) {
            $ultimo = $e->getMessage();
            $msgs[] = ['role' => 'user', 'content' => 'Esa consulta falló: '.$ultimo.'. Corrígela usando solo las vistas y columnas indicadas y responde de nuevo SOLO con el JSON.'];

            continue;
        }
        $titulo = mb_substr(trim((string) ($j['titulo'] ?? '')) ?: $pregunta, 0, 120);

        return ['titulo' => $titulo, 'sql' => ia_validar_sql($sql), 'grafico' => ia_grafico($j['grafico'] ?? null, $r['columnas'], $r['filas']), 'pregunta' => $pregunta] + $r;
    }
    throw new RuntimeException('No pude armar una consulta válida para esa pregunta ('.$ultimo.'). Intenta reformularla con más detalle.');
}

/** Resumen en lenguaje natural (2.ª llamada, hasta 25 filas). */
function ia_resumir(string $pregunta, array $cols, array $filas): string
{
    $datos = json_encode(['columnas' => $cols, 'filas' => array_slice($filas, 0, 25), 'total_filas' => count($filas)], JSON_UNESCAPED_UNICODE);

    return trim(ia_llamar([
        ['role' => 'system', 'content' => 'Eres analista de un restaurante. Resume en 1 o 2 frases en español, con cifras exactas de los datos (moneda '.MONEDA.'), la respuesta a la pregunta. No inventes nada y no uses markdown.'],
        ['role' => 'user', 'content' => "Pregunta: $pregunta\nDatos: $datos"],
    ], 300));
}

/* ------------------------------------------------------------------ */
/*  Chat IA */
/* ------------------------------------------------------------------ */
const IA_GUIA = 'GUÍA DEL RESTAURANTE:
- Dashboard: resumen de ventas y operación.
- Punto de venta: elegir mesa, agregar productos, enviar a cocina/barra y cobrar con caja abierta.
- Pedidos y ventas: seguimiento de pedidos y ventas completadas, comprobantes y filtros.
- Productos: carta, precios, costos, recetas e inventario; ajustes de stock.
- Clientes: clientes registrados y consumo.
- Gastos: registrar egresos operativos.
- Caja: apertura, cierre y arqueo.
- Reservas: reservas de mesas.
- Delivery: pedidos delivery o recojo, repartidor y estados.
- Asistente IA: preguntas, reportes con tablas y gráficos, CSV, favoritos y recientes.
- Chatbot: conversación y consulta de datos reales; no modifica registros.
- Compras y proveedores de SYS no existen como módulos/tablas en este proyecto. No inventes información ni instrucciones para esos módulos.
- No hay stock mínimo ni unidad configurados: no inventes umbrales para stock bajo. Puedes mostrar existencias o productos sin stock.';

function ia_prompt_chat(): string
{
    return 'Tu identidad es el asistente del restaurante '.json_encode(ia_empresa(), JSON_UNESCAPED_UNICODE).'. Cuando te presentes usa ese nombre del negocio, nunca SaborPOS ni el nombre del software.
Eres el asistente de un restaurante (sistema Restaurante). Hablas con el administrador, en español, de forma clara y breve. Puedes: (1) consultar los datos del negocio con SQL de solo lectura, o (2) explicar cómo usar el sistema. NO puedes crear, modificar ni borrar datos: si te lo piden, indica en qué módulo se hace.

Vistas disponibles:
'.ia_esquema_texto().'

'.ia_reglas().'

'.IA_GUIA.'

Responde SOLO con un JSON válido, sin markdown:
- Para consultar datos: {"accion":"consulta","sql":"SELECT ..."}  (usa LIMIT razonable, p. ej. 10).
- Para responder sin consultar (ayuda de uso, saludos, aclaraciones o preguntas fuera del negocio): {"accion":"respuesta","texto":"..."}
Si la pregunta es ambigua, asume lo más razonable (por defecto «hoy» o «este mes» según el contexto) y dilo en la respuesta.';
}

/** Un turno de chat. $hist = [['q'=>..,'a'=>..], ...]. Devuelve ['texto','tabla'?,'sql'?]. */
function ia_chat(string $msg, array $hist = []): array
{
    if (preg_match('/^\s*(hola|buenas|buenos días|buenos dias|buenas tardes|buenas noches)[!¡¿?.,\s]*$/iu', $msg)) {
        return ['texto' => '¡Hola! Soy el asistente de '.ia_empresa().'. ¿En qué puedo ayudarte hoy con la gestión de tu restaurante?'];
    }
    $msgs = [['role' => 'system', 'content' => ia_prompt_chat()]];
    foreach (array_slice($hist, -6) as $h) {
        $msgs[] = ['role' => 'user', 'content' => (string) $h['q']];
        $msgs[] = ['role' => 'assistant', 'content' => (string) $h['a']];
    }
    $msgs[] = ['role' => 'user', 'content' => $msg];
    $res = null;
    $sql = '';
    $err = '';
    for ($i = 0; $i < 2; $i++) {
        $raw = ia_llamar($msgs, 900);
        $j = ia_extraer_json($raw);
        if (! $j) {
            return ['texto' => trim(preg_replace('/^```\w*|```$/m', '', $raw))];
        }   // el modelo contestó en texto plano
        if (($j['accion'] ?? '') !== 'consulta') {
            return ['texto' => trim((string) ($j['texto'] ?? $j['mensaje'] ?? '')) ?: 'No tengo una respuesta para eso.'];
        }
        $sql = (string) ($j['sql'] ?? '');
        $msgs[] = ['role' => 'assistant', 'content' => $raw];
        try {
            $res = ia_ejecutar($sql);
            break;
        } catch (RuntimeException $e) {
            $err = $e->getMessage();
            $msgs[] = ['role' => 'user', 'content' => 'Esa consulta falló: '.$err.'. Corrígela y responde de nuevo SOLO con el JSON.'];
        }
    }
    if (! $res) {
        return ['texto' => 'No pude consultar eso con los datos disponibles ('.$err.'). ¿Puedes reformular la pregunta?'];
    }
    $datos = json_encode(['columnas' => $res['columnas'], 'filas' => array_slice($res['filas'], 0, 30), 'total_filas' => count($res['filas'])], JSON_UNESCAPED_UNICODE);
    $texto = trim(ia_llamar([
        ['role' => 'system', 'content' => 'Eres el asistente de un restaurante. Redacta en español una respuesta breve y clara (máx. 4 frases) a la pregunta usando SOLO los datos dados, con cifras exactas. Los conteos de productos no son unidades de stock: distingue COUNT de productos de SUM(stock). Usa moneda solo para importes (moneda '.MONEDA.'). Si hay una lista, menciona los primeros elementos; el resto se ve en «Ver datos». Si no hay filas, dilo. Puedes usar **negrita** para cifras clave. Sin tablas ni listas largas.'],
        ['role' => 'user', 'content' => "Pregunta: $msg\nDatos: $datos"],
    ], 400));

    return ['texto' => $texto, 'sql' => ia_validar_sql($sql),
        'tabla' => ['columnas' => $res['columnas'], 'filas' => array_slice($res['filas'], 0, 10), 'total' => count($res['filas'])]];
}

function ia_out(array $d, int $code = 200): void
{
    throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json($d + ['ok' => true], $code, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE));
}
function ia_fail(string $m, int $code = 422): void
{
    ia_out(['ok' => false, 'error' => $m], $code);
}

function ia_consulta(int $id, int $uid): array
{
    $s = db()->prepare('SELECT * FROM ia_consultas WHERE id=? AND usuario_id=?');
    $s->execute([$id, $uid]);
    $c = $s->fetch();
    if (! $c) {
        ia_fail('Consulta no encontrada.', 404);
    }

    return $c;
}

function ch_out(array $d, int $code = 200): void
{
    throw new \Illuminate\Http\Exceptions\HttpResponseException(response()->json($d + ['ok' => true], $code, [], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE));
}
function ch_fail(string $m, int $code = 422): void
{
    ch_out(['ok' => false, 'error' => $m], $code);
}
