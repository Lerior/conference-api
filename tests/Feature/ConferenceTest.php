<?php

namespace Tests\Feature;

use App\Models\Conference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConferenceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_conference_list_returns_200(): void
    {
        $response = $this->get('/api/conference');

        $response->assertStatus(200);
    }

    public function test_user_can_create_a_conference(){

        $user = User::factory()->create();

        $payload= [
            'title' => 'Conferencia de prueba',
            'description' => 'Descripcion de prueba para testing',
            'date' => '2026-03-02'
        ];

        $response = $this->actingAs($user)->postJson('/api/conference',$payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('conferences', [
            'title'=>'Conferencia de prueba'
        ]);

    }

    public function test_conference_list_returns_data(){

        Conference::create([
            'title' => 'Conferencia existente',
            'description' => 'Descripcion existente',
            'date' => '2026-03-02',
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->get('/api/conference');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Conferencia existente',
        ]);
    }

    public function test_create_conference_requires_data(){

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/conference', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'title',
            'description',
            'date',
        ]);
    }
}
