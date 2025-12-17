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

    public function test_guest_cannot_create_conference(){

        $payload = [
            'title' => 'Conferencia sin auth',
            'description' => 'Descripcion sin auth',
            'date' => '2026-03-02',
        ];

        $response = $this->postJson('/api/conference', $payload);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('conferences', [
            'title' => 'Conferencia sin auth',
        ]);
    }

    public function test_can_get_conference_by_id(){

        $user = User::factory()->create();

        $conference = Conference::create([
            'title' => 'Conferencia por ID',
            'description' => 'Descripcion por ID',
            'date' => '2026-03-02',
            'user_id' => $user->id,
        ]);

        $response = $this->get("/api/conference/{$conference->id}");

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'title' => 'Conferencia por ID',
            'description' => 'Descripcion por ID',
        ]);
    }

    public function test_get_conference_by_id_returns_404_when_not_found(){

        $response = $this->get('/api/conference/999999');

        $response->assertStatus(404);

        $response->assertJson([
            'message'=>'Conference not found'
        ]);
    }

    public function test_user_can_get_my_conferences(){

        $user = User::factory()->create();

        Conference::create([
            'title' => 'Mia 1',
            'description' => 'Desc 1',
            'date' => '2026-03-02',
            'user_id' => $user->id,
        ]);

        Conference::create([
            'title' => 'Mia 2',
            'description' => 'Desc 2',
            'date' => '2026-03-03',
            'user_id' => $user->id,
        ]);

        Conference::create([
            'title' => 'No es mia',
            'description' => 'Otra',
            'date' => '2026-03-04',
            'user_id' => User::factory()->create()->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/my-conferences");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['title' => 'Mia 1']);
        $response->assertJsonFragment(['title' => 'Mia 2']);
    }

    public function test_guest_cannot_get_my_conferences(){
        
        $response = $this->getJson("/api/my-conferences");

        $response->assertStatus(401);
    }

    public function test_owner_can_update_conference(){

        $user = User::factory()->create();

        $conference = Conference::create([
            'title' => 'Titulo original',
            'description' => 'Descripcion original',
            'date' => '2026-03-02',
            'user_id' => $user->id,
        ]);

        $payload = [
            'title' => 'Titulo actualizado',
            'description' => 'Descripcion actualizado',
            'date' => '2026-04-10',
        ];

        $response = $this->actingAs($user)->patchJson("/api/conference/{$conference->id}",$payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('conferences',[
            'id' => $conference->id,
            'title' => 'Titulo actualizado',
        ]);
    }

    public function test_non_owner_cannot_update_conference(){

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $conference = Conference::create([
            'title' => 'Titulo original',
            'description' => 'Descripcion original',
            'date' => '2026-03-02',
            'user_id' => $owner->id,
        ]);


        $payload = [
            'title' => 'Intento de hack',
            'description' => 'No deberia actualizar',
            'date' => '2026-05-01',
        ];

        $response = $this->actingAs($otherUser)->patchJson("/api/conference/{$conference->id}",$payload);

        $response->assertStatus(403);

        $this->assertDatabaseHas('conferences',[
            'id' => $conference->id,
            'title' => 'Titulo original',
        ]);
    }

    public function test_owner_can_delete_conference(){

        $user = User::factory()->create();

        $conference = Conference::create([
            'title' => 'Conferencia a eliminar',
            'description' => 'Descripcion',
            'date' => '2026-03-02',
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/conference/{$conference->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('conferences', [
            'id' => $conference->id,
        ]);
    }

    public function test_guest_cannot_delete_conference(){

        $conference = Conference::create([
            'title' => 'Conferencia',        
            'description' => 'Descripcion',        
            'date' => '2026-03-02',        
            'user_id' => User::factory()->create()->id,        
        ]);

        $response = $this->deleteJson("/api/conference/{$conference->id}");

        $response->assertStatus(401);

        $this->assertDatabaseHas('conferences',[
            'id' => $conference->id,
        ]);
    }

    public function test_non_owner_cannot_delete_conference(){

        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $conference = Conference::create([
            'title' => 'Conferencia',
            'description' => 'Descripcion',
            'date' => '2026-03-02',
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser)->deleteJson("/api/conference/{$conference->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('conferences',[
            'id' => $conference->id,
        ]);
    }

    public function test_delete_conference_returns_404_when_not_found(){

        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/conference/9999999");

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Conference not found',
        ]);
    }
}
