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
