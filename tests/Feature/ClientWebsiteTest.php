<?php

namespace Tests\Feature;

use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_vue_shell(): void
    {
        $this->withoutVite();

        $this->get('/')
            ->assertOk()
            ->assertSee('id="app"', false);
    }

    public function test_clients_endpoint_returns_active_websites(): void
    {
        $client = Client::query()->create(['email' => 'client@example.com']);
        $client->websites()->create(['url' => 'example.com']);
        $client->websites()->create(['url' => 'https://inactive.example.com', 'is_active' => false]);

        $this->getJson(route('clients.index'))
            ->assertOk()
            ->assertJsonPath('clients.0.email', 'client@example.com')
            ->assertJsonPath('clients.0.websites.0.url', 'https://example.com')
            ->assertJsonCount(1, 'clients.0.websites');
    }
}
