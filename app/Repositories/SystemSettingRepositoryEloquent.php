<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\Repositories\SystemSettingRepository;
use App\Models\SystemSetting;
use App\Validators\SystemSettingValidator;

/**
 * Class SystemSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SystemSettingRepositoryEloquent extends BaseRepository implements SystemSettingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SystemSetting::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getRawSetting(string $key): ?string
    {
        return optional($this->findByField("key", $key)->first())->value;
    }

    public function getBooleanOrDefault(string $key, string $configKey): bool
    {
        $raw = $this->getRawSetting($key);

        return $raw !== null
            ? filter_var($raw, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false); // optional third fallback
    }
}
