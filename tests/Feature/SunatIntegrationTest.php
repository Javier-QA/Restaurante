<?php

namespace Tests\Feature;

use App\Models\DailySummary;
use App\Services\Sunat\DailySummaryBuilder;
use App\Services\Sunat\SunatConfig;
use App\Services\Sunat\SunatService;
use Greenter\Model\Summary\Summary;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\Support\RestaurantTestCase;

class SunatIntegrationTest extends RestaurantTestCase
{
    public function test_daily_summary_sends_eloquent_model_before_greenter_document(): void
    {
        $model = DailySummary::create(['reference_date' => now()->toDateString(), 'generation_date' => now()->toDateString(), 'identifier' => 'RC-TEST-1', 'sunat_status' => 'PENDING', 'user_id' => auth()->id()]);
        $document = new Summary;
        $builder = Mockery::mock(DailySummaryBuilder::class);
        $builder->shouldReceive('build')->once()->andReturn(['model' => $model, 'summary' => $document]);
        $service = Mockery::mock(SunatService::class);
        $service->shouldReceive('sendSummary')->once()->with($model, $document)->andReturn($model);
        $this->instance(DailySummaryBuilder::class, $builder);
        $this->instance(SunatService::class, $service);
        $this->post('/daily-summaries', ['reference_date' => now()->toDateString()])->assertRedirect(route('daily_summaries.index'))->assertSessionHas('success');
    }

    public function test_uploaded_p12_certificate_is_loaded_from_private_disk(): void
    {
        Storage::fake('local');
        $key = openssl_pkey_new(['private_key_bits' => 2048]);
        $csr = openssl_csr_new(['commonName' => 'Restaurant test'], $key);
        $cert = openssl_csr_sign($csr, null, $key, 1);
        openssl_pkcs12_export($cert, $p12, $key, 'test-password');
        Storage::disk('local')->put('sunat/certs/test.p12', $p12);
        $config = new SunatConfig(['sunat_cert_path' => 'sunat/certs/test.p12', 'sunat_cert_password' => 'test-password']);
        $this->assertSame(Storage::disk('local')->path('sunat/certs/test.p12'), $config->certPath());
        $method = new \ReflectionMethod(SunatService::class, 'loadCertificate');
        $pem = $method->invoke(new SunatService($config));
        $this->assertStringContainsString('BEGIN CERTIFICATE', $pem);
        $this->assertStringContainsString('PRIVATE KEY',$pem);
    }
}
