<?php

namespace Tests\Feature;

use App\Models\CorporateContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorporateContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_corporate_contact()
    {
        $response = $this->postJson('/api/corporate-contact', [
            'name' => 'Test Company',
            'email' => 'john@example.com',
            'message' => 'We want to partner.',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('corporate_contacts', ['name' => 'Test Company']);
    }
}
