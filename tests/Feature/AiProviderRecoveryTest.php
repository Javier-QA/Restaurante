<?php

namespace Tests\Feature;

use App\Services\SysIa\ProviderClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiProviderRecoveryTest extends TestCase
{
    public function test_temporary_failure_is_retried_and_real_response_is_returned(): void
    {
        Http::fake(['example.test/*' => Http::sequence()->pushStatus(503)->push(['choices' => [['message' => ['content' => 'Respuesta real']]]])]);
        $response = app(ProviderClient::class)->send('https://example.test/chat/completions', ['clave' => 'test-key'], ['model' => 'test-model']);
        $this->assertSame('Respuesta real', $response->json('choices.0.message.content'));
        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-key') && $request['model'] === 'test-model');
    }

    public function test_persistent_failure_stops_after_one_retry(): void
    {
        Http::fake(['example.test/*' => Http::sequence()->pushStatus(502)->pushStatus(503)]);
        $response = app(ProviderClient::class)->send('https://example.test/chat/completions', [], []);
        $this->assertSame(503, $response->status());
        Http::assertSentCount(2);
    }

    public function test_invalid_key_quota_and_model_errors_are_not_retried(): void
    {
        foreach ([401, 403, 404, 429] as $status) {
            Http::swap(new \Illuminate\Http\Client\Factory);
            Http::fake(['example.test/*' => Http::response([], $status)]);
            $response = app(ProviderClient::class)->send('https://example.test/chat/completions', [], []);
            $this->assertSame($status, $response->status());
            Http::assertSentCount(1);
        }
    }
}
