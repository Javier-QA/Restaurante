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
            $this->assertStringContainsString('Plato: Plato '.$i.'; Cantidad: '.(10 - $i), $text);
        }
        $this->assertStringNotContainsString('Plato 6', $text);
        $this->assertStringContainsString('Ver datos', $text);
    }

    public function test_empty_and_aggregate_results_are_not_invented(): void
    {
        require_once app_path('Services/SysIa/core.php');
        $this->assertSame('No se encontraron resultados para esta consulta.', \App\Services\SysIa\ia_resumen_chat_local(['ventas'], []));
        $this->assertSame('Total de ventas: S/234.00; Pedidos: 2.', \App\Services\SysIa\ia_resumen_chat_local(['total_ventas', 'pedidos'], [['total_ventas' => 234, 'pedidos' => 2]]));
    }
    public function test_relative_periods_and_dates_are_shown_in_readable_format(): void
    {
        require_once app_path('Services/SysIa/core.php');
        $this->travelTo(\Carbon\Carbon::parse('2026-10-08 15:00:00', 'America/Lima'));
        $today = \App\Services\SysIa\ia_resumen_chat_local(['total_ventas', 'numero_pedidos'], [['total_ventas' => 234, 'numero_pedidos' => 2]], '¿Cuánto vendimos hoy?');
        $this->assertStringContainsString('Hoy (08/10/2026)', $today);
        $this->assertStringContainsString('Total de ventas: S/234.00; Número de pedidos: 2.', $today);
        $this->assertStringContainsString('Ayer (07/10/2026)', \App\Services\SysIa\ia_resumen_chat_local(['pedidos'], [['pedidos' => 7]], '¿Y ayer?'));
        $this->assertSame('Fecha: 02/09/2026.', \App\Services\SysIa\ia_resumen_local(['fecha'], [['fecha' => '2026-09-02']]));
        $this->travelBack();
    }
}
