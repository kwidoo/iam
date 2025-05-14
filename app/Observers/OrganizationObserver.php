<?php

namespace App\Observers;

use App\Events\OrganizationMembershipChanged;
use App\Models\Organization;
use App\Services\OrganizationAccessProvider;

class OrganizationObserver
{
    public function __construct(protected OrganizationAccessProvider $accessProvider) {}

    /**
     * Handle the Organization "created" event.
     */
    public function created(Organization $organization): void
    {
        // When a new organization is created, set up default permissions
        $this->accessProvider->setupOrganizationDefaultPermissions($organization);

        // If owner was already attached, sync their roles and permissions
        if ($organization->owner) {
            event(new OrganizationMembershipChanged($organization, $organization->owner));
        }
    }

    /**
     * Handle the Organization "updated" event.
     */
    public function updated(Organization $organization): void
    {
        // If the organization slug changed, we need to update role names
        if ($organization->isDirty('slug')) {
            // This will trigger a role sync for all users in the organization
            event(new OrganizationMembershipChanged($organization));
        }
    }

    /**
     * Handle the Organization "deleted" event.
     */
    public function deleted(Organization $organization): void
    {
        // Not implementing cascading deletes here
        // Permission and role cleanup would typically happen in a more controlled manner
    }
}
