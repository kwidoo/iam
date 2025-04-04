<?php

namespace App\Factories;

use App\Contracts\Repositories\InvitationRepository;
use App\Contracts\Services\InvitationStrategy;
use App\Data\InvitationConfigData;
use App\Enums\InviteMethod;
use App\Services\Invitation\Strategies\InviteByNotificationChannelStrategy;
use App\Services\Invitation\Strategies\InviteExistingUserToOrgStrategy;
use InvalidArgumentException;
use RuntimeException;

class InvitationStrategyFactory
{
    protected array $basedMethods = [
        InviteMethod::EMAIL,
        InviteMethod::PHONE,
    ];

    public function __construct(
        protected InvitationRepository $repository,
    ) {}

    public function make(InvitationConfigData $config): InvitationStrategy
    {
        if (in_array($config->method, $this->basedMethods, true)) {
            return app()->make(InviteByNotificationChannelStrategy::class);
        }

        return match ($config->method) {
            InviteMethod::EXISTING_USER => app()->make(InviteExistingUserToOrgStrategy::class),
            default => throw new InvalidArgumentException("Unsupported invitation method: {$config->method->value}")
        };
    }

    public function makeFromToken(string $token): InvitationStrategy
    {
        $invitation = $this->repository->findByToken($token);

        if (! $invitation) {
            throw new RuntimeException("Invitation not found for token: {$token}");
        }

        return $this->make(new InvitationConfigData(
            InviteMethod::from($invitation->method)
        ));
    }
}
