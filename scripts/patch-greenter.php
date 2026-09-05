<?php
/**
 * Re-aplica los parches necesarios sobre la librería Greenter para
 * que sea compatible con PHP 8+.
 *
 * Se ejecuta automáticamente tras `composer install` y `composer update`
 * mediante los scripts post-install-cmd / post-update-cmd del composer.json.
 *
 * Bugs corregidos:
 *  1. WSSESecurityHeader: $this->SoapHeader(...) → parent::__construct(...) (PHP 8)
 *  2. Plantillas Twig: remover atributo languageLocaleID en cbc:Note
 *     (SUNAT BETA lo rechaza con error 0306 por validar contra UBL2.1 estándar
 *     sin la customización SUNAT; el atributo es opcional y la leyenda se
 *     mantiene en el CDATA, que es lo que SUNAT realmente lee).
 */

$patches = [
    [
        'file'   => __DIR__ . '/../vendor/greenter/ws/src/Ws/Header/WSSESecurityHeader.php',
        'search' => '$this->SoapHeader(self::WSS_NAMESPACE',
        'replace'=> 'parent::__construct(self::WSS_NAMESPACE',
        'name'   => 'WSSESecurityHeader (PHP-4 constructor call)',
    ],
    [
        'file'   => __DIR__ . '/../vendor/greenter/xml/src/Xml/Templates/invoice2.1.xml.twig',
        'search' => '<cbc:Note languageLocaleID="{{ leg.code }}"><![CDATA[{{ leg.value }}]]></cbc:Note>',
        'replace'=> '<cbc:Note><![CDATA[{{ leg.value }}]]></cbc:Note>',
        'name'   => 'invoice2.1.xml.twig (remove languageLocaleID)',
    ],
    [
        'file'   => __DIR__ . '/../vendor/greenter/xml/src/Xml/Templates/notacr2.1.xml.twig',
        'search' => '<cbc:Note languageLocaleID="{{ leg.code }}"><![CDATA[{{ leg.value }}]]></cbc:Note>',
        'replace'=> '<cbc:Note><![CDATA[{{ leg.value }}]]></cbc:Note>',
        'name'   => 'notacr2.1.xml.twig (remove languageLocaleID)',
    ],
    [
        'file'   => __DIR__ . '/../vendor/greenter/xml/src/Xml/Templates/notadb2.1.xml.twig',
        'search' => '<cbc:Note languageLocaleID="{{ leg.code }}"><![CDATA[{{ leg.value }}]]></cbc:Note>',
        'replace'=> '<cbc:Note><![CDATA[{{ leg.value }}]]></cbc:Note>',
        'name'   => 'notadb2.1.xml.twig (remove languageLocaleID)',
    ],
];

$applied  = 0;
$skipped  = 0;
$missing  = 0;

foreach ($patches as $p) {
    if (!file_exists($p['file'])) {
        echo "  [SKIP] {$p['name']}: archivo no existe\n";
        $missing++;
        continue;
    }

    $contents = file_get_contents($p['file']);

    if (str_contains($contents, $p['replace'])) {
        // Ya parchado
        $skipped++;
        continue;
    }

    if (!str_contains($contents, $p['search'])) {
        echo "  [WARN] {$p['name']}: patrón no encontrado (¿versión diferente de Greenter?)\n";
        $missing++;
        continue;
    }

    $patched = str_replace($p['search'], $p['replace'], $contents);
    file_put_contents($p['file'], $patched);
    echo "  [OK]   {$p['name']}\n";
    $applied++;
}

if ($applied > 0) {
    echo "Greenter: {$applied} parche(s) aplicado(s), {$skipped} ya estaba(n) aplicado(s).\n";
} elseif ($missing === 0) {
    echo "Greenter: todos los parches ya estaban aplicados.\n";
}
