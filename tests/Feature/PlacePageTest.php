<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlacePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_place_has_a_public_detail_page(): void
    {
        $this->seed();

        $this->get('/lugares/volcan-de-santa-ana')
            ->assertOk()
            ->assertSee('Volcán de Santa Ana')
            ->assertSee('Verificado por TurismoSV')
            ->assertSee('Planifica tu visita')
            ->assertSee('Ingresa para registrar tu visita');
    }

    public function test_an_unknown_place_returns_not_found(): void
    {
        $this->get('/lugares/no-existe')->assertNotFound();
    }
}
