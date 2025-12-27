<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Conference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    public function test_user_can_get_attendances(){

        $user = User::factory()->create();
        $conference1 = Conference::factory()->create();
        $conference2 = Conference::factory()->create();
        $conference3 = Conference::factory()->create();

        Attendance::create([
            'user_id' => $user->id,
            'conference_id' => $conference1->id,
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'conference_id' => $conference2->id,
        ]);
        Attendance::create([
            'user_id' => User::factory()->create()->id,
            'conference_id' => $conference3->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/attendance/me');
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');    
        $response->assertJsonFragment(['conference_id' => $conference1->id]);    
        $response->assertJsonFragment(['conference_id'=> $conference2->id]);    
    }

    public function test_conference_owner_can_get_attendances(){

        $conference = Conference::factory()->create();
        $otherconference = Conference::factory()->create();
        $owner = $conference->user;
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        Attendance::create([
            'user_id' => $user1->id,
            'conference_id' => $conference->id,
        ]);
        Attendance::create([
            'user_id' => $user2->id,
            'conference_id' => $conference->id,
        ]);
        Attendance::create([
            'user_id' => $user3->id,
            'conference_id' => $otherconference->id,
        ]);

        $response = $this->actingAs($owner)->getJson("/api/attendance/{$conference->id}/conference");
        $response->assertStatus(200);
        $response->assertJsonCount(2,'data');
        $response->assertJsonFragment(['email' => $user1->email]);
        $response->assertJsonFragment(['email' => $user2->email]);
    }

    public function test_user_can_create_attendance(): void
    {
        $user = User::factory()->create();
        $conference = Conference::factory()->create();

        $payload = [
            'conference_id' => $conference->id,
        ];

        $this->actingAs($user)
        ->postJson('/api/attendance', $payload)
        ->assertStatus(200);

    }

    public function test_guest_cannot_create_attendance(){

        $conference = Conference::factory()->create();

        $payload = [
            'conference_id' => $conference->id,
        ];

         $this->postJson('/api/attendance', $payload)
        ->assertStatus(401);
    }


    public function test_user_cannot_create_duplicate_attendance(): void
{
        $user = User::factory()->create();
        $conference = Conference::factory()->create();

        $payload = [
        'conference_id' => $conference->id,
    ];

        $this->actingAs($user)->postJson('/api/attendance', $payload)
            ->assertStatus(200);

        $this->actingAs($user)->postJson('/api/attendance', $payload)
            ->assertStatus(409);
}

    public function test_conference_owner_can_delete_attendance(){
        
        $conference = Conference::factory()->create();
        $owner = $conference->user;
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'conference_id' => $conference->id,
        ]);

        $this->actingAs($owner)->deleteJson("/api/attendance/{$attendance->id}")
        ->assertStatus(200);

}

    public function test_user_can_delete_attendance(){

        $conference = Conference::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'conference_id' => $conference->id,
        ]);

        $this->actingAs($user)->deleteJson("/api/attendance/{$attendance->id}")
        ->assertStatus(200);
    }

    public function test_guest_cannot_delete_attendance(){

        $conference = Conference::factory()->create();
        $user = User::factory()->create();

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'conference_id' => $conference->id,
        ]);

        $this->deleteJson("/api/attendance/{$attendance->id}")
        ->assertStatus(401);
    }

}
