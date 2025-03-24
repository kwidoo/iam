<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that the index endpoint returns all organizations
     * that the authenticated user belongs to.
     */
    public function test_index_returns_user_organizations()
    {
        /** @var User */
        $user = User::factory()->create();
        $org1 = Organization::factory()->create(['owner_id' => $user->uuid]);
        $org2 = Organization::factory()->create();

        // Attach the user to two organizations.
        $user->organizations()->attach($org1->id, ['role' => 'member']);
        $user->organizations()->attach($org2->id, ['role' => 'member']);

        $this->actingAs($user, 'api')
            ->getJson('/api/organizations')
            ->assertOk()
            ->assertJsonCount(2);
    }

    /**
     * Test that a new organization is created with valid input
     * and a 201 status code is returned.
     */
    public function test_store_creates_organization_with_valid_data()
    {
        /** @var User */
        $user = User::factory()->create();
        $payload = [
            'name'        => 'Test Organization',
            'description' => 'A description for testing',
            'logo'        => 'test-logo.png',
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/organizations', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Organization']);

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Organization',
        ]);
    }

    /**
     * Test that a member can view an organization.
     */
    public function test_show_returns_organization_for_member()
    {
        /** @var User */
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        // Attach user as a member so policy passes.
        $organization->users()->attach($user->id, ['role' => 'member']);

        $this->actingAs($user, 'api')
            ->getJson("/api/organizations/{$organization->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $organization->id]);
    }

    /**
     * Test that a non-member cannot view the organization.
     */
    public function test_show_fails_for_non_member()
    {
        /** @var User */
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->actingAs($user, 'api')
            ->getJson("/api/organizations/{$organization->id}")
            ->assertForbidden();
    }

    /**
     * Test that an authorized user (owner/admin) can update an organization.
     */
    public function test_update_allows_authorized_user()
    {
        /** @var User */
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        // Attach the user as an owner.
        $organization->users()->attach($user->id, ['role' => 'owner']);

        $payload = [
            'name'        => 'Updated Organization Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($user, 'api')
            ->putJson("/api/organizations/{$organization->id}", $payload);

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Updated Organization Name']);

        $this->assertDatabaseHas('organizations', [
            'id'   => $organization->id,
            'name' => 'Updated Organization Name',
        ]);
    }

    /**
     * Test that an unauthorized user (member) cannot update the organization.
     */
    public function test_update_fails_for_unauthorized_user()
    {
        /** @var User */
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        // Attach the user as a member (not owner/admin).
        $organization->users()->attach($user->id, ['role' => 'member']);

        $payload = ['name' => 'Updated Name'];

        $this->actingAs($user, 'api')
            ->putJson("/api/organizations/{$organization->id}", $payload)
            ->assertForbidden();
    }

    /**
     * Test that deletion is prevented if the authenticated user is the sole owner.
     */
    public function test_destroy_prevents_deletion_if_sole_owner()
    {
        /** @var User */
        $user = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $user->id]);
        // Attach the user as the sole owner.
        $organization->users()->attach($user->id, ['role' => 'owner']);

        $this->actingAs($user, 'api')
            ->deleteJson("/api/organizations/{$organization->id}")
            ->assertStatus(403)
            ->assertJsonFragment([
                'error' => 'You cannot delete the organization as you are the sole owner'
            ]);

        $this->assertDatabaseHas('organizations', ['id' => $organization->id]);
    }

    /**
     * Test that deletion works when there are multiple owners.
     */
    public function test_destroy_allows_deletion_when_multiple_owners()
    {
        /** @var User */
        $user = User::factory()->create();
        $otherOwner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $user->id]);
        // Attach both users as owners.
        $organization->users()->attach($user->id, ['role' => 'owner']);
        $organization->users()->attach($otherOwner->id, ['role' => 'owner']);

        $this->actingAs($user, 'api')
            ->deleteJson("/api/organizations/{$organization->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('organizations', ['id' => $organization->id]);
    }
}
