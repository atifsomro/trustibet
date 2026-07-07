<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GamesApiTest extends TestCase
{
    public function test_games_api_endpoint_returns_json(): void
    {
        Http::fake([
            'https://www.freetogame.com/api/games' => Http::response([
                ['id' => 1, 'title' => 'Test Game', 'genre' => 'Action'],
            ], 200),
        ]);

        $response = $this->getJson('/api/games');

        $response->assertOk()
            ->assertJson([
                ['id' => 1, 'title' => 'Test Game', 'genre' => 'Action'],
            ]);
    }
}
