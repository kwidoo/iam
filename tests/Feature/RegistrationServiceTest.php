<?php

namespace Tests\Feature;


use App\Services\RegistrationService;
use App\Data\RegistrationData;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kwidoo\Contacts\Tests\Fixtures\FakeEmailVerifier;
use Kwidoo\Contacts\Tests\Fixtures\FakePhoneVerifier;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Replace the verifier config with fakes
        config()->set('contacts.verifiers', [
            'email' => FakeVerifier::class,
            'phone' => FakeVerifier::class,
        ]);

        // If needed, resolve services again with the fake config now in place
        $this->service = app(\App\Services\RegistrationService::class);
    }
    public function test_registers_user_with_email_and_password(): void
    {
        $data = new RegistrationData(
            method: 'email',
            otp: false,
            value: 'user@example.com',
            password: 'secret123', // not used, overwritten by strategy
            fname: 'Jane',
            lname: 'Doe',
        );

        $user = $this->service->registerNewUser($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('user@example.com', $user->contacts()->first()->value);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseCount('organizations', 1); // default org created
    }
    public function test_registers_user_with_phone_and_otp(): void
    {
        $data = new RegistrationData(
            method: 'phone',
            otp: true,
            value: '+1234567890',
            fname: 'John',
            lname: 'Smith',
        );

        $user = $this->service->registerNewUser($data);

        $this->assertEquals('+1234567890', $user->contacts()->first()->value);
        $this->assertNotNull($user->profile);
        $this->assertNotEmpty($user->organizations);
    }
    public function test_registers_user_with_existing_organization(): void
    {
        $org = Organization::factory()->create();

        $data = new RegistrationData(
            fname: 'John',
            lname: 'Smith',
            method: 'email',
            otp: false,
            value: 'withorg@example.com',
            organization: $org
        );

        $user = $this->service->registerNewUser($data);

        $this->assertTrue($user->organizations->contains($org));
    }
    public function test_registration_fails_with_missing_value(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class); // or a custom exception

        $data = new RegistrationData(
            method: 'email',
            otp: false,
            value: '', // required
        );

        $this->service->registerNewUser($data);
    }
}
