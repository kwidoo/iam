<?php

namespace App\Services;

use App\Contracts\Services\ProfileService;
use App\Contracts\Repositories\ProfileRepository;
use App\Models\Profile;
use Kwidoo\Lifecycle\Contracts\Lifecycle\Lifecycle;
use App\Services\BaseService;
use Kwidoo\Mere\Services\Traits\OnlyCreate;
use Kwidoo\Lifecycle\Traits\RunsLifecycle;
use Kwidoo\Mere\Contracts\Services\MenuService;
use Spatie\LaravelData\Contracts\BaseData;

class DefaultProfileService extends BaseService implements ProfileService
{
    use OnlyCreate;
    use RunsLifecycle;

    public function __construct(
        MenuService $menuService,
        ProfileRepository $repository,
        Lifecycle $lifecycle,
    ) {
        parent::__construct($menuService, $repository, $lifecycle);
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
     * @param ProfileCreateData $data
     *
     * @return mixed
     */
    protected function handleCreate(BaseData $data): mixed
    {
        return $this->repository->create([
            'fname' => $data->fname,
            'lname' => $data->lname,
            'user_id' => $data->userId,
            'dob' => $data->dob,
            'gender' => $data->gender
        ]);
    }

    protected function eventKey(): string
    {
        return 'profile';
    }
}
