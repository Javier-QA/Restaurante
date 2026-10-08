<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Tests\TestCase;

class SectionTitleTest extends TestCase
{
    public function test_top_bar_matches_sidebar_sections_across_modules(): void
    {
        $sections = [
            'Operaciones' => ['pos.index', 'delivery.show', 'reservations.index', 'sales.index', 'kitchen.index', 'barra.index'],
            'Caja / Arqueo' => ['cash_registers.create', 'billing.show', 'credit_notes.index', 'daily_summaries.index'],
            'Gestión' => ['clients.index', 'categories.index', 'products.edit', 'tables.index', 'users.index', 'settings.index', 'system.index'],
            'Inteligencia' => ['ai.assistant', 'ai.chat', 'assistant.index', 'chatbot.index'],
            'General' => ['dashboard', 'reports.index'],
        ];
        foreach ($sections as $section => $names) {
            foreach ($names as $name) {
                $route = (new Route('GET', '/test', fn () => null))->name($name);
                $request = Request::create('/test');
                $request->setRouteResolver(fn () => $route);
                app()->instance('request', $request);
                $html = view('layouts.partials.section-title')->render();
                $this->assertStringContainsString('<h5 class="mb-0">'.$section.'</h5>', $html, $name);
            }
        }
    }
}
