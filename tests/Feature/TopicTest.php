<?php

namespace Tests\Feature;

use App\Models\Conference;
use App\Models\Topic;
use App\Models\User;
use Database\Factories\ConferenceFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TopicTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_only_conference_owner_can_create_topic(): void
    {
        $conference = Conference::Factory()->create();
        $owner = $conference->user;
        $otherUser = User::Factory()->create();

        $payload = [
            'title' => 'Nuevo topic',
            'description' => 'Nueva descripcion',
            'conference_id' => $conference->id,
            'user_id' => User::factory()->create()->id,
        ];

        $this->actingAs($otherUser)
        ->postJson('/api/topic', $payload)
        ->assertStatus(403);
        
        $this->actingAs($owner)
        ->postJson('/api/topic', $payload)
        ->assertStatus(201);
    }

    public function test_guest_cannot_create_topic(){

        $conference = Conference::factory()->create();

        $payload = [
            'title' => 'Topic sin auth',
            'description' => 'Descripcion sin auth',
            'conference_id' => $conference->id,
            'user_id' => User::factory()->create()->id,
        ];

        $response = $this->postJson('/api/topic',$payload);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('topics', [
            'title' => 'Topic sin auth',
        ]);
    }


    public function test_conference_owner_can_update_topic(){
        
        $conference = Conference::factory()->create();
        $owner = $conference->user;

        $topic = Topic::create([
            'title' => 'Titulo original',
            'description' => 'Descripcion original',
            'conference_id' => $conference->id,
            'user_id' => User::factory()->create()->id, 
        ]);

        $payload = [
            'title' => 'Titulo actualizado',
            'description' => 'Descripcion actualizada',
            'conference_id' => $conference->id,
            'speaker_name' => 'Roy Serth',
        ];

        $response = $this->actingAs($owner)->patchJson("/api/topic/{$topic->id}", $payload);
    
        $response->assertStatus(200);

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'title' => 'Titulo actualizado',
        ]);
    }
}
