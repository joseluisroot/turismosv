<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_presents_the_product_and_catalog(): void
    {
        $this->seed();

        $this->get('/')
            ->assertOk()
            ->assertSee('Tu próximo lugar favorito')
            ->assertSee('PASAPORTE TURÍSTICO')
            ->assertSee('Verificado por TurismoSV')
            ->assertSee('Contenido de demostración');
    }
}
