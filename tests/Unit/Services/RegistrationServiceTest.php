<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\UserRepository;
use App\Contracts\Services\RegistrationService as RegistrationServiceContract;
use App\Data\RegistrationData;
use App\Enums\RegistrationFlow;
use App\Factories\ContactServiceFactory;
use App\Factories\OrganizationServiceFactory;
use App\Factories\ProfileServiceFactory;
use App\Models\Organization;
use App\Models\Profile;
use App\Models\User;
use App\Resolvers\ConfigurationContextResolver;
use App\Resolvers\RegistrationStrategyResolver;
use App\Services\RegistrationService;
use App\Services\Traits\OnlyCreate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kwidoo\Mere\Contracts\Lifecycle;
use Kwidoo\Mere\Contracts\MenuService;
use Mockery;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private RegistrationService $service;
    private Mockery\MockInterface $menuService;
    private Mockery\MockInterface $userRepository;
    private Mockery\MockInterface $lifecycle;
    private Mockery\MockInterface $contactServiceFactory;
    private Mockery\MockInterface $profileServiceFactory;
    private Mockery\MockInterface $organizationServiceFactory;
    private Mockery\MockInterface $registrationStrategyResolver;
    private Mockery\MockInterface $configurationContextResolver;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var MenuService */
        $this->menuService = Mockery::mock(MenuService::class);
        /** @var UserRepository */
        $this->userRepository = Mockery::mock(UserRepository::class);
        /** @var Lifecycle */
        $this->lifecycle = Mockery::mock(Lifecycle::class);
        /** @var ContactServiceFactory */
        $this->contactServiceFactory = Mockery::mock(ContactServiceFactory::class);
        /** @var ProfileServiceFactory */
        $this->profileServiceFactory = Mockery::mock(ProfileServiceFactory::class);
        /** @var OrganizationServiceFactory */
        $this->organizationServiceFactory = Mockery::mock(OrganizationServiceFactory::class);
        /** @var RegistrationStrategyResolver */
        $this->registrationStrategyResolver = Mockery::mock(RegistrationStrategyResolver::class);
        /** @var ConfigurationContextResolver */
        $this->configurationContextResolver = Mockery::mock(ConfigurationContextResolver::class);

        $this->service = new RegistrationService(
            $this->menuService,
            $this->userRepository,
            $this->lifecycle,
            $this->contactServiceFactory,
            $this->profileServiceFactory,
            $this->organizationServiceFactory,
            $this->registrationStrategyResolver,
            $this->configurationContextResolver
        );
    }

    /**
     * @test
     */
    public function test_implements_registration_service_contract()
    {
        $this->assertInstanceOf(RegistrationServiceContract::class, $this->service);
    }

    /**
     * @test
     */
    public function test_registers_new_user_with_email_and_password()
    {
        // Prepare test data
        $data = new RegistrationData(
            method: 'email',
            otp: false,
            value: 'test@example.com',
            password: 'password123',
            fname: 'John',
            lname: 'Doe',
            flow: RegistrationFlow::MAIN_ONLY->value
        );

        $user = User::factory()->make();
        $organization = Organization::factory()->make();

        // Mock lifecycle calls
        $this->lifecycle->shouldReceive('run')
            ->once()
            ->andReturn($user);

        $this->lifecycle->shouldReceive('withoutTrx')
            ->andReturn($this->lifecycle);

        $this->lifecycle->shouldReceive('withoutAuth')
            ->andReturn($this->lifecycle);

        // Mock context resolver
        $this->configurationContextResolver->shouldReceive('forOrg')
            ->with(null)
            ->andReturn($this->configurationContextResolver);

        $this->configurationContextResolver->shouldReceive('registrationConfig')
            ->andReturn(['config' => 'value']);

        $this->configurationContextResolver->shouldReceive('getOrg')
            ->andReturn($organization);

        // Mock strategy resolver
        $this->registrationStrategyResolver->shouldReceive('setConfig')
            ->with(['config' => 'value'])
            ->andReturnNull();

        $this->registrationStrategyResolver->shouldReceive('resolve')
            ->with('secret', Mockery::any())
            ->andReturn(Mockery::mock(OnlyCreate::class));

        // Mock service factories
        $contactService = Mockery::mock(OnlyCreate::class);
        $this->contactServiceFactory->shouldReceive('make')
            ->with($user, $this->lifecycle)
            ->andReturn($contactService);

        $profileService = Mockery::mock(OnlyCreate::class);
        $this->profileServiceFactory->shouldReceive('make')
            ->with($user, $this->lifecycle)
            ->andReturn($profileService);

        $organizationService = Mockery::mock(OnlyCreate::class);
        $this->organizationServiceFactory->shouldReceive('make')
            ->with($this->lifecycle)
            ->andReturn($organizationService);

        // Execute
        $result = $this->service->registerNewUser($data);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user, $result);
    }

    /**
     * @test
     */
    public function test_registers_new_user_with_phone_and_otp()
    {
        // Prepare test data
        $data = new RegistrationData(
            method: 'phone',
            otp: true,
            value: '+1234567890',
            fname: 'John',
            lname: 'Doe',
            flow: RegistrationFlow::MAIN_ONLY->value
        );

        $user = User::factory()->make();
        $organization = Organization::factory()->make();

        // Mock lifecycle calls
        $this->lifecycle->shouldReceive('run')
            ->once()
            ->andReturn($user);

        $this->lifecycle->shouldReceive('withoutTrx')
            ->andReturn($this->lifecycle);

        $this->lifecycle->shouldReceive('withoutAuth')
            ->andReturn($this->lifecycle);

        // Mock context resolver
        $this->configurationContextResolver->shouldReceive('forOrg')
            ->with(null)
            ->andReturn($this->configurationContextResolver);

        $this->configurationContextResolver->shouldReceive('registrationConfig')
            ->andReturn(['config' => 'value']);

        $this->configurationContextResolver->shouldReceive('getOrg')
            ->andReturn($organization);

        // Mock strategy resolver
        $this->registrationStrategyResolver->shouldReceive('setConfig')
            ->with(['config' => 'value'])
            ->andReturnNull();

        $this->registrationStrategyResolver->shouldReceive('resolve')
            ->with('secret', Mockery::any())
            ->andReturn(Mockery::mock(OnlyCreate::class));

        // Mock service factories
        $contactService = Mockery::mock(OnlyCreate::class);
        $this->contactServiceFactory->shouldReceive('make')
            ->with($user, $this->lifecycle)
            ->andReturn($contactService);

        $profileService = Mockery::mock(OnlyCreate::class);
        $this->profileServiceFactory->shouldReceive('make')
            ->with($user, $this->lifecycle)
            ->andReturn($profileService);

        $organizationService = Mockery::mock(OnlyCreate::class);
        $this->organizationServiceFactory->shouldReceive('make')
            ->with($this->lifecycle)
            ->andReturn($organizationService);

        // Execute
        $result = $this->service->registerNewUser($data);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user, $result);
    }

    /**
     * @test
     */
    public function test_handles_invite_only_registration()
    {
        // Prepare test data
        $organization = Organization::factory()->create(['registration_mode' => 'invite_only']);
        $data = new RegistrationData(
            method: 'email',
            otp: false,
            value: 'test@example.com',
            password: 'password123',
            fname: 'John',
            lname: 'Doe',
            organization: $organization,
            inviteCode: 'valid-invite-code',
            flow: RegistrationFlow::MAIN_ONLY->value
        );

        $user = User::factory()->make();

        // Mock lifecycle calls
        $this->lifecycle->shouldReceive('run')
            ->once()
            ->with('registerNewUser', 'registration-invite', $data, Mockery::any())
            ->andReturn($user);

        $this->lifecycle->shouldReceive('withoutTrx')
            ->andReturn($this->lifecycle);

        $this->lifecycle->shouldReceive('withoutAuth')
            ->andReturn($this->lifecycle);

        // Mock context resolver
        $this->configurationContextResolver->shouldReceive('forOrg')
            ->with($organization)
            ->andReturn($this->configurationContextResolver);

        $this->configurationContextResolver->shouldReceive('registrationConfig')
            ->andReturn(['config' => 'value']);

        $this->configurationContextResolver->shouldReceive('getOrg')
            ->andReturn($organization);

        // Mock strategy resolver
        $this->registrationStrategyResolver->shouldReceive('setConfig')
            ->with(['config' => 'value'])
            ->andReturnNull();

        $this->registrationStrategyResolver->shouldReceive('resolve')
            ->with('secret', Mockery::any())
            ->andReturn(Mockery::mock(OnlyCreate::class));

        // Mock service factories
        $contactService = Mockery::mock(OnlyCreate::class);
        $this->contactServiceFactory->shouldReceive('make')
            ->with($user, $this->lifecycle)
            ->andReturn($contactService);

        $profileService = Mockery::mock(OnlyCreate::class);
        $this->profileServiceFactory->shouldReceive('make')
            ->with($user, $this->lifecycle)
            ->andReturn($profileService);

        $organizationService = Mockery::mock(OnlyCreate::class);
        $this->organizationServiceFactory->shouldReceive('make')
            ->with($this->lifecycle)
            ->andReturn($organizationService);

        // Execute
        $result = $this->service->registerNewUser($data);

        // Assert
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user, $result);
    }

    /**
     * @test
     */
    public function test_validates_required_fields()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Missing required fields
        $data = new RegistrationData(
            method: '',
            otp: false
        );

        $this->service->registerNewUser($data);
    }

    /**
     * @test
     */
    public function test_validates_method_type()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Invalid method
        $data = new RegistrationData(
            method: 'invalid',
            otp: false,
            value: 'test@example.com'
        );

        $this->service->registerNewUser($data);
    }

    /**
     * @test
     */
    public function test_validates_password_when_otp_is_false()
    {
        $this->expectException(\InvalidArgumentException::class);

        // Missing password when OTP is false
        $data = new RegistrationData(
            method: 'email',
            otp: false,
            value: 'test@example.com'
        );

        $this->service->registerNewUser($data);
    }
}
