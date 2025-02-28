<?php

namespace Tests\Unit\Services;

use App\Aggregates\UserAggregate;
use App\Models\User;
use App\Services\AddEmailService;
use App\Contracts\CreateEmail;
use App\Events\Email\EmailCreated;
use App\Events\Email\EmailRemoved;
use App\Exceptions\EmailCreationFailed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\EventSourcing\AggregateRoots\FakeAggregateRoot;
use Tests\TestCase;

class AddEmailServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FakeAggregateRoot $createEmail;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createEmail = $this->app->make(CreateEmail::class)->fake();
        $this->service = $this->app->make(AddEmailService::class, [$this->createEmail]);
    }

    public function testAddsNewEmailToUser()
    {
        $user = User::factory()->create();
        $email = 'newuser@example.com';
        $referenceId = 'ref123';

        $this->createEmail->given([])
            ->expects('createEmail')
            ->expects('persist');

        $this->service->__invoke($user, $email, $referenceId);

        $this->createEmail->assertRecorded([new EmailCreated(['email' => $email])]);
    }

    public function testReplacesUnverifiedEmail()
    {
        $user = User::factory()->create(['email' => 'old@example.com']);
        $newEmail = 'new@example.com';
        $referenceId = 'ref124';

        $user->emails()->create([
            'email' => 'old@example.com',
            'verified_at' => null,
        ]);

        $this->createEmail->given([new EmailCreated(['email' => 'old@example.com'])])
            ->expects('removeEmail')
            ->expects('createEmail')
            ->expects('persist');

        $this->service->__invoke($user, $newEmail, $referenceId);

        $this->createEmail->assertRecorded([
            new EmailRemoved($user->email),
            new EmailCreated(['email' => $newEmail])
        ]);
    }

    public function testEmailCreationFailure()
    {
        $user = User::factory()->create();
        $email = 'failuser@example.com';
        $referenceId = 'ref125';

        $this->createEmail->given([])
            ->willThrow(new \Exception('Database error'));

        $this->expectException(EmailCreationFailed::class);
        $this->expectExceptionMessage('Email creation failed: Database error');

        $this->service->__invoke($user, $email, $referenceId);
    }
}
