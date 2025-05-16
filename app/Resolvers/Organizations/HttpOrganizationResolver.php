<?php

namespace App\Resolvers\Organizations;

use Kwidoo\Mere\Contracts\Models\OrganizationInterface;
use Kwidoo\Mere\Contracts\Repositories\OrganizationRepository;
use Kwidoo\Mere\Contracts\Repositories\SystemSettingRepository;
use App\Contracts\Resolvers\OrganizationResolver;
use App\Enums\OrganizationFlow;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;

class HttpOrganizationResolver implements OrganizationResolver
{
    public function __construct(
        protected OrganizationRepository $repository,
        protected SystemSettingRepository $settingRepository
    ) {
        if (app()->runningInConsole()) {
            throw new RuntimeException('This resolver is only for HTTP requests.');
        }
    }

    /**
     *
     * Resolve the organization based on the provided name or request context.
     *
     * @param string|null $name
     * @return \App\Models\Organization|null
     * @throws InvalidArgumentException
     */
    public function resolve(?string $name = null): ?OrganizationInterface
    {
        if ($this->settingBool('registration.force_main_org', 'iam.defaults.force_main_org') === OrganizationFlow::MAIN_ONLY) {
            return $this->repository->getMainOrganization();
        }

        $slug = $name
            ?? request()->route('organization')
            ?? $this->fromSubdomain(request());

        if ($this->settingBool('registration.force_main_org', 'iam.defaults.force_main_org') !== OrganizationFlow::MAIN_ONLY && $slug === null) {
            return null;
        }

        if (!$slug) {
            throw new InvalidArgumentException('No organization provided.');
        }

        return $this->repository
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->first();
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    protected function fromSubdomain(Request $request): ?string
    {
        $host = $request->getHost(); // e.g. org.example.com
        $parts = explode('.', $host);
        if (count($parts) < 3) return null;
        return $parts[0]; // org
    }

    /**
     * @param string $key
     * @param string $configKey
     *
     * @return bool
     */
    protected function settingBool(string $key, string $configKey): bool
    {
        $setting = $this->settingRepository->findByField('key', $key)->first();

        return $setting !== null
            ? filter_var($setting->value, FILTER_VALIDATE_BOOLEAN)
            : config($configKey, false);
    }
}
