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

    public function test_registration_page_renders_in_register_mode(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        $response->assertSee('Buat Akun Baru');
    }
}
