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
        $response->assertSee('Sali Vuz!');
        $response->assertSee('Herzlich Willkommen in Drachenstein');
        $response->assertSee('Beiträge in den letzten 24 Stunden');
    }
}
