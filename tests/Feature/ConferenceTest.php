<?php

namespace Tests\Feature;

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
}
