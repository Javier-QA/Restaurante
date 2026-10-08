<?php

namespace Tests\Feature;

use Tests\TestCase;

class AiShortResultTest extends TestCase
{
    public function test_short_rankings_include_five_actual_results(): void
    {
        require_once app_path('Services/SysIa/core.php');
        $rows = array_map(fn ($i) => ['plato' => 'Plato '.$i, 'cantidad' => 10 - $i], range(1, 6));
        $text = \App\Services\SysIa\ia_resumen_chat_local(['plato', 'cantidad'], $rows);
        foreach (range(1, 5) as $i) {
            $this->assertStringContainsString('plato: Plato '.$i.'; cantidad: '.(10 - $i), $text);
        }
        $this->assertStringNotContainsString('Plato 6', $text);
        $this->assertStringContainsString('Ver datos', $text);
    }

    public function test_empty_and_aggregate_results_are_not_invented(): void
    {
        require_once app_path('Services/SysIa/core.php');
        $this->assertSame('No se encontraron resultados para esta consulta.', \App\Services\SysIa\ia_resumen_chat_local(['ventas'], []));
        $this->assertSame('total ventas: 234; pedidos: 2.', \App\Services\SysIa\ia_resumen_chat_local(['total_ventas', 'pedidos'], [['total_ventas' => 234, 'pedidos' => 2]]));
    }
}
