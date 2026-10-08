<?php
namespace Tests\Feature;
use Tests\TestCase;
class AiRankingSummaryTest extends TestCase
{
    public function test_sales_ranking_uses_actual_names_and_quantities_without_provider(): void
    {
        require_once app_path('Services/SysIa/core.php');
        $text = \App\Services\SysIa\ia_resumir('Productos más vendidos', ['producto', 'cantidad'], [['producto' => 'Ceviche', 'cantidad' => 9], ['producto' => 'Agua', 'cantidad' => 1]]);
        $this->assertSame('Los resultados muestran Ceviche con 9 unidades y Agua con 1 unidad.', $text);
    }
    public function test_unsupported_results_are_left_for_ai_interpretation(): void
    {
        require_once app_path('Services/SysIa/core.php');
        $this->assertNull(\App\Services\SysIa\ia_resumen_ranking(['producto', 'stock'], [['producto' => 'Agua', 'stock' => 4]], 'Productos más vendidos'));
        $this->assertNull(\App\Services\SysIa\ia_resumen_ranking(['producto', 'cantidad'], [['producto' => 'Agua', 'cantidad' => 'desconocido']], 'Productos más vendidos'));
    }
}
