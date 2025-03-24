<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that an invitation is created when valid data is provided
     * and that authorization is enforced.
     */
    public function test_store_creates_invitation_with_valid_data()
    {
        /** @var User */
        $owner = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $owner->id]);
        // Attach the owner so that invite policy passes.
        $organization->users()->attach($owner->id, ['role' => 'owner']);

        $payload = [
            'contact_type'  => 'email',
            'contact_value' => 'invitee@example.com',
            'expires_at'    => now()->addDays(7)->toDateTimeString(),
        ];

        $response = $this->actingAs($owner, 'api')
            ->postJson("/api/organizations/{$organization->id}/invitations", $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['contact_value' => 'invitee@example.com']);

        $this->assertDatabaseHas('invitations', [
            'organization_id' => $organization->id,
            'contact_value'   => 'invitee@example.com',
        ]);
    }

    /**
     * Test that unauthorized users cannot create invitations.
     */
    public function test_store_fails_for_unauthorized_user()
    {
        /** @var User */
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        // Attach user as a regular member (not owner/admin).
        $organization->users()->attach($user->id, ['role' => 'member']);

        $payload = [
            'contact_type'  => 'email',
            'contact_value' => 'invitee@example.com',
        ];

        $this->actingAs($user, 'api')
            ->postJson("/api/organizations/{$organization->id}/invitations", $payload)
            ->assertForbidden();
    }

    /**
     * Test accepting a valid invitation.
     * Verify that the user is added to the organization and the invitation's accepted_at timestamp is updated.
     */
    public function test_accept_invitation_successfully()
    {
        /** @var User */
        $inviter = User::factory()->create();
        /** @var User */
        $invitee = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $inviter->id]);
        // Attach inviter as owner.
        $organization->users()->attach($inviter->id, ['role' => 'owner']);

        // Create an invitation without an expiration.
        $invitation = Invitation::factory()->create([
            'organization_id' => $organization->id,
            'invited_by'      => $inviter->id,
            'contact_type'    => 'email',
            'contact_value'   => 'invitee@example.com',
            'expires_at'      => null,
            'accepted_at'     => null,
        ]);

        $response = $this->actingAs($invitee, 'api')
            ->postJson("/api/invitations/accept/{$invitation->token}");

        $response->assertOk()
            ->assertJsonFragment(['message' => 'Invitation accepted']);

        $this->assertNotNull($invitation->fresh()->accepted_at);
        // Verify the invitee was added as a member.
        $this->assertTrue($organization->users()->where('user_id', $invitee->id)->exists());
    }

    /**
     * Test that accepting an expired invitation returns an error.
     */
    public function test_accept_invitation_fails_if_expired()
    {
        /** @var User */
        $inviter = User::factory()->create();
        /** @var User */
        $invitee = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $inviter->id]);
        $organization->users()->attach($inviter->id, ['role' => 'owner']);

        // Create an invitation that expired yesterday.
        $invitation = Invitation::factory()->create([
            'organization_id' => $organization->id,
            'invited_by'      => $inviter->id,
            'contact_type'    => 'email',
            'contact_value'   => 'invitee@example.com',
            'expires_at'      => now()->subDay(),
            'accepted_at'     => null,
        ]);

        $this->actingAs($invitee, 'api')
            ->postJson("/api/invitations/accept/{$invitation->token}")
            ->assertStatus(403)
            ->assertJsonFragment(['error' => 'Invitation expired']);

        $this->assertNull($invitation->fresh()->accepted_at);
        $this->assertFalse($organization->users()->where('user_id', $invitee->id)->exists());
    }

    /**
     * Test that attempting to accept an invitation that has already been accepted returns an error.
     */
    public function test_accept_invitation_fails_if_already_accepted()
    {
        /** @var User */
        $inviter = User::factory()->create();
        /** @var User */
        $invitee = User::factory()->create();
        $organization = Organization::factory()->create(['owner_id' => $inviter->id]);
        $organization->users()->attach($inviter->id, ['role' => 'owner']);

        // Create an invitation that has already been accepted.
        $invitation = Invitation::factory()->create([
            'organization_id' => $organization->id,
            'invited_by'      => $inviter->id,
            'contact_type'    => 'email',
            'contact_value'   => 'invitee@example.com',
            'expires_at'      => now()->addDay(),
            'accepted_at'     => now(),
        ]);

        $this->actingAs($invitee, 'api')
            ->postJson("/api/invitations/accept/{$invitation->token}")
            ->assertStatus(403)
            ->assertJsonFragment(['error' => 'Invitation already accepted']);

        $this->assertFalse($organization->users()->where('user_id', $invitee->id)->exists());
    }
}
