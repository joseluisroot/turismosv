<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    public function test_local_configuration_is_rejected_without_exposing_secrets(): void
    {
        $this->artisan('turismosv:production-check')
            ->expectsOutputToContain('APP_ENV=production')
            ->expectsOutputToContain('Produccion no esta lista')
            ->assertFailed();
    }
}
