<?php

namespace App\Data\Organizations;

use App\Enums\OrganizationFlow;
use App\Enums\OrganizationMode;
use Spatie\LaravelData\Data;

/**
 * Data Transfer Object for organization creation configuration.
 * Contains the necessary settings specific for organization creation process.
 *
 * @category App\Data\Organization
 * @package  App\Data\Organization
 */
class OrganizationConfigData extends Data
{
    /**
     * Initialize organization configuration with required settings.
     *
     * @param OrganizationFlow $flow Organization creation flow type
     * @param OrganizationMode $mode Organization registration mode
     */
    public function __construct(
        public OrganizationFlow $flow,
        public OrganizationMode $mode,
    ) {}

    /**
     * Get all configuration keys and their values.
     *
     * @return array<string, mixed>
     */
    public function allKeys(): array
    {
        return [
            'flow' => $this->flow,
            'mode' => $this->mode,
        ];
    }
}
