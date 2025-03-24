<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use App\Policies\OrganizationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new OrganizationPolicy();
    }

    public function view_policy_allows_members_to_view_the_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        // Attach the user as a member.
        $organization->users()->attach($user->id, ['role' => 'member']);

        $this->assertTrue($this->policy->view($user, $organization));
    }

    public function view_policy_denies_non_members_from_viewing_the_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->assertFalse($this->policy->view($user, $organization));
    }

    public function update_policy_allows_owners_and_admins_to_update_the_organization()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create();

        // Attach users with respective roles.
        $organization->users()->attach($owner->id, ['role' => 'owner']);
        $organization->users()->attach($admin->id, ['role' => 'admin']);
        $organization->users()->attach($member->id, ['role' => 'member']);

        $this->assertTrue($this->policy->update($owner, $organization));
        $this->assertTrue($this->policy->update($admin, $organization));
        $this->assertFalse($this->policy->update($member, $organization));
    }

    public function update_policy_denies_non_members_from_updating_the_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->assertFalse($this->policy->update($user, $organization));
    }

    public function delete_policy_allows_only_owners_to_delete_the_organization()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create();

        // Attach users with various roles.
        $organization->users()->attach($owner->id, ['role' => 'owner']);
        $organization->users()->attach($admin->id, ['role' => 'admin']);
        $organization->users()->attach($member->id, ['role' => 'member']);

        $this->assertTrue($this->policy->delete($owner, $organization));
        $this->assertFalse($this->policy->delete($admin, $organization));
        $this->assertFalse($this->policy->delete($member, $organization));
    }

    public function delete_policy_denies_non_members_from_deleting_the_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $this->assertFalse($this->policy->delete($user, $organization));
    }

    public function invite_policy_allows_only_owners_and_admins_to_invite_new_users()
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $nonMember = User::factory()->create();
        $organization = Organization::factory()->create();

        // Attach users with specific roles.
        $organization->users()->attach($owner->id, ['role' => 'owner']);
        $organization->users()->attach($admin->id, ['role' => 'admin']);
        $organization->users()->attach($member->id, ['role' => 'member']);

        $this->assertTrue($this->policy->invite($owner, $organization));
        $this->assertTrue($this->policy->invite($admin, $organization));
        $this->assertFalse($this->policy->invite($member, $organization));
        $this->assertFalse($this->policy->invite($nonMember, $organization));
    }
}
