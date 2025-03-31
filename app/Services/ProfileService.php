<?php

namespace App\Services;

use App\Contracts\Services\ProfileService as ProfileServiceContract;
use App\Contracts\Repositories\ProfileRepository;
use App\Data\RegistrationData;
use App\Models\Profile;
use App\Models\User;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;

class ProfileService extends BaseService implements ProfileServiceContract
{
    public function __construct(
        MenuService $menuService,
        ProfileRepository $repository,
        Lifecycle $lifecycle,
        protected User $user
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
    }

    protected function eventKey(): string
    {
        return 'profile';
    }

    /**
     * @param string $userId
     *
     * @return Profile
     */
    public function findByUserId(string $userId): Profile
    {
        return $this->repository->findByField('user_id', $userId)->first();
    }

    /**
     * @param RegistrationData $data
     *
     * @return mixed
     */
    public function registerProfile(RegistrationData $data): mixed
    {
        return $this->create([
            'fname' => $data->fname,
            'lname' => $data->lname,
            'user_id' => $data->userId,
        ]);
    }
}
