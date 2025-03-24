<?php

namespace Tests\Unit;

use App\Models\Invitation;
use App\Models\Organization;
use App\Models\User;
use App\Services\OrganizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OrganizationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrganizationService $organizationService;

    protected function setUp(): void
    {
        parent::setUp();
        // Resolve the service from the container (or instantiate directly)
        $this->organizationService = new OrganizationService();
    }

    public function it_creates_an_organization_with_valid_data_and_attaches_the_owner()
    {
        // Create an owner user.
        $owner = User::factory()->create();

        $data = [
            'name' => 'Test Organization',
            'description' => 'A test organization',
            'logo' => 'logo.png',
        ];

        $organization = $this->organizationService->createOrganization($data, $owner);

        // Verify that a new Organization model was returned.
        $this->assertInstanceOf(Organization::class, $organization);
        // Check that the organization ID is a valid UUID.
        $this->assertTrue(Str::isUuid($organization->id));
        // Verify auto-generated slug.
        $this->assertEquals(Str::slug($data['name']), $organization->slug);
        // Check that the owner_id is correctly set.
        $this->assertEquals($owner->id, $organization->owner_id);

        // Confirm that the owner is attached as a user with the "owner" role.
        $this->assertTrue($organization->users()->where('user_id', $owner->id)->exists());
        $pivot = $organization->users()->where('user_id', $owner->id)->first()->pivot;
        $this->assertEquals('owner', $pivot->role);

        // Verify that the owner has been assigned the Spatie role "owner".
        $this->assertTrue($owner->hasRole('owner'));
    }

    public function it_throws_a_validation_exception_when_an_organization_with_the_same_slug_already_exists()
    {
        $this->expectException(ValidationException::class);

        $owner = User::factory()->create();
        $data = [
            'name' => 'Duplicate Org',
            'description' => 'First org',
        ];

        // Create the first organization.
        $this->organizationService->createOrganization($data, $owner);

        // Attempt to create a second organization with the same name (slug).
        $anotherOwner = User::factory()->create();
        $this->organizationService->createOrganization($data, $anotherOwner);
    }

    public function it_adds_a_user_to_an_organization_with_the_specified_role_and_assigns_spatie_role()
    {
        // Create an organization and a user to add.
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        // Ensure the user is not already attached.
        $this->assertFalse($organization->users()->where('user_id', $user->id)->exists());

        // Add the user to the organization as a member.
        $this->organizationService->addUserToOrganization($organization, $user, 'member');

        // Refresh the organization to load the latest pivot data.
        $organization->refresh();

        // Confirm that the user is attached with role "member".
        $this->assertTrue($organization->users()->where('user_id', $user->id)->exists());
        $pivot = $organization->users()->where('user_id', $user->id)->first()->pivot;
        $this->assertEquals('member', $pivot->role);

        // Verify that the user is assigned the Spatie role "member".
        $this->assertTrue($user->hasRole('member'));
    }

    public function it_throws_a_validation_exception_when_the_user_already_belongs_to_the_organization()
    {
        $this->expectException(ValidationException::class);

        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        // First, add the user to the organization.
        $this->organizationService->addUserToOrganization($organization, $user, 'member');

        // Attempting to add the same user again should throw an exception.
        $this->organizationService->addUserToOrganization($organization, $user, 'member');
    }

    public function it_creates_an_invitation_with_valid_data_and_correct_foreign_keys()
    {
        $organization = Organization::factory()->create();
        $inviter = User::factory()->create();

        $data = [
            'contact_type'  => 'email',
            'contact_value' => 'invitee@example.com',
            'expires_at'    => now()->addDays(7)->toDateTimeString(),
        ];

        $invitation = $this->organizationService->createInvitation($data, $organization, $inviter);

        // Verify that an Invitation model was returned.
        $this->assertInstanceOf(Invitation::class, $invitation);
        // Ensure the invitation has a valid UUID.
        $this->assertTrue(Str::isUuid($invitation->id));
        // Check that a token was generated.
        $this->assertNotEmpty($invitation->token);
        // Verify the foreign keys.
        $this->assertEquals($organization->id, $invitation->organization_id);
        $this->assertEquals($inviter->id, $invitation->invited_by);
        // Validate that other fields were set correctly.
        $this->assertEquals($data['contact_type'], $invitation->contact_type);
        $this->assertEquals($data['contact_value'], $invitation->contact_value);
        $this->assertEquals($data['expires_at'], $invitation->expires_at->toDateTimeString());
    }
}
