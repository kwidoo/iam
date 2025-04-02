<?php

namespace App\Contracts\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface SystemSettingRepository.
 *
 * @package namespace App\Contracts\Repositories;
 */
interface SystemSettingRepository extends RepositoryInterface
{
    public function getBooleanOrDefault(string $key, string $configKey): bool;
    public function getRawSetting(string $key): ?string;
}
