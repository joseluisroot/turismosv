<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class LegalDocumentsTest extends TestCase {
 use RefreshDatabase;
 public function test_all_legal_documents_are_public_and_versioned(): void { foreach(['legal.terms','legal.privacy','legal.cookies','legal.community','legal.notice'] as $route)$this->get(route($route))->assertOk();$this->get(route('legal.terms'))->assertSee(config('legal.version'))->assertSee('Revisión jurídica pendiente');$this->get(route('legal.privacy'))->assertSee('Decreto Legislativo 144'); }
 public function test_home_links_real_legal_documents_and_assets_include_cookie_notice(): void { $this->seed();$this->get(route('home'))->assertOk()->assertSee(route('legal.cookies'),false)->assertSee(route('legal.community'),false)->assertSee(route('legal.notice'),false);$script=file_get_contents(resource_path('js/app.js'));$this->assertStringContainsString('turismosv_cookie_notice',$script);$this->assertStringContainsString('No hay publicidad ni analítica opcional activa',$script); }
}
