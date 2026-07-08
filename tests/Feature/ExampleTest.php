<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_homepage_shows_institutional_logos(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Kemdikdasmen', false)
            ->assertSee('Disdik Kota Tangerang', false)
            ->assertSee('PGRI', false)
            ->assertSee('Merdeka Belajar', false)
            ->assertSee('grayscale contrast-125 brightness-75 opacity-70', false)
            ->assertDontSee('Grapho', false)
            ->assertDontSee('Signum', false)
            ->assertDontSee('Vectra', false)
            ->assertDontSee('Optimal', false);
    }
}
