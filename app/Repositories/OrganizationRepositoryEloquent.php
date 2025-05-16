<?php

namespace App\Repositories;

use Prettus\Repository\Criteria\RequestCriteria;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Models\ProfileInterface;
use Kwidoo\Mere\Contracts\Models\UserInterface;
use Kwidoo\Mere\Repositories\RepositoryEloquent;

/**
 * Class OrganizationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class OrganizationRepositoryEloquent extends RepositoryEloquent implements OrganizationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrganizationInterface::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @return \App\Models\Organization|null
     */
    public function getMainOrganization(): ?OrganizationInterface
    {
        return $this->where('slug', 'main')->first();
    }

    /**
     * Attach a user to an organization with a specific role
     *
     * @param \App\Models\Organization $organization
     * @param \App\Models\User $user
     * @param string $role
     * @return void
     */
    public function attachUser(OrganizationInterface $organization, UserInterface $user, string $role): void
    {
        $organization->users()->syncWithoutDetaching([$user->id => ['role' => $role]]);
    }

    /**
     * Attach a profile to an organization with a specific role
     *
     * @param \App\Models\Organization $organization
     * @param \App\Models\Profile $profile
     * @param string $role
     * @return void
     */
    public function attachProfile(OrganizationInterface $organization, ProfileInterface $profile): void
    {
        $organization->profiles()->syncWithoutDetaching([$profile->id]);
    }
}
