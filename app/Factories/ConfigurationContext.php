<?php

namespace App\Factories;

class ConfigurationContext
{
    public function forOrg(?string $orgId): self
    {
        // You could look up OrgAuthRules or SystemSetting here
        return $this;
    }

    public function strategy(): string
    {
        // Stubbed fallback for now
        return 'main_only';
    }
}
