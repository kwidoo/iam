<?php

namespace Tests\Unit\Factories;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\SystemSettingRepository;
use App\Data\RegistrationConfigData;
use App\Enums\RegistrationFlow;
use App\Enums\RegistrationMode;
use App\Enums\RegistrationIdentity;
use App\Enums\RegistrationProfile;
use App\Enums\RegistrationSecret;
use App\Resolvers\ConfigurationContextResolver;
use App\Models\Organization;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use Tests\TestCase;

class ConfigurationContextTest extends TestCase
{
    protected MockObject $organizationRepository;
    protected MockObject $settingRepository;
    protected ConfigurationContextResolver $context;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var OrganizationRepository */
        $this->organizationRepository = $this->createMock(OrganizationRepository::class);
        /** @var SystemSettingRepository */
        $this->settingRepository = $this->createMock(SystemSettingRepository::class);
        $this->context = new ConfigurationContextResolver($this->organizationRepository, $this->settingRepository);
    }

    public function test_for_org_with_string_slug_finds_org()
    {
        $org = Organization::factory()->make(['slug' => 'test-org']);
        $collection = new Collection([$org]);

        $this->organizationRepository
            ->expects($this->once())
            ->method('findByField')
            ->with('slug', 'test-org')
            ->willReturn($collection);

        $this->context->forOrg('test-org');

        $this->assertTrue(true); // Passes if no exceptions thrown
    }

    public function test_for_org_with_model_sets_directly()
    {
        $org = Organization::factory()->make();

        $result = $this->context->forOrg($org);
        $this->assertInstanceOf(ConfigurationContextResolver::class, $result);
    }

    public function test_for_user_sets_user()
    {
        $user = User::factory()->make();

        $result = $this->context->forUser($user);
        $this->assertInstanceOf(ConfigurationContextResolver::class, $result);
    }

    public function test_registration_config_returns_expected_defaults()
    {
        // Empty orgs list = INITIAL_BOOTSTRAP
        $this->organizationRepository
            ->method('all')
            ->willReturn(collect());

        $config = $this->context->registrationConfig();

        $this->assertInstanceOf(RegistrationConfigData::class, $config);
        $this->assertEquals(RegistrationFlow::INITIAL_BOOTSTRAP, $config->flow);
        $this->assertEquals(RegistrationIdentity::EMAIL, $config->identity);
        $this->assertEquals(RegistrationProfile::DEFAULT_PROFILE, $config->profile);
        $this->assertEquals(RegistrationSecret::PASSWORD, $config->secret);
    }

    public function test_determine_flow_when_user_joins_org()
    {
        $org = Organization::factory()->make();
        $user = User::factory()->make();
        $org->setRelation('owner', User::factory()->make(['id' => 999])); // different from $user

        $this->organizationRepository->method('all')->willReturn(collect(['something']));
        $this->context->forOrg($org)->forUser($user);

        $flow = $this->context->determineFlow();
        $this->assertEquals(RegistrationFlow::USER_JOINS_USER_ORG, $flow);
    }

    public function test_determine_flow_returns_main_only_from_config()
    {
        $org = Organization::factory()->make();
        $user = User::factory()->make();
        $org->setRelation('owner', $user); // same user

        config()->set('iam.defaults.registration_strategy', RegistrationFlow::MAIN_ONLY);

        $this->organizationRepository->method('all')->willReturn(collect(['something']));
        $this->context->forOrg($org)->forUser($user);

        $flow = $this->context->determineFlow();
        $this->assertEquals(RegistrationFlow::MAIN_ONLY, $flow);
    }

    public function test_determine_mode_returns_org_mode()
    {
        $org = Organization::factory()->make(['registration_mode' => RegistrationMode::OPEN]);
        $this->context->forOrg($org);

        $this->assertEquals(RegistrationMode::OPEN, $this->context->determineMode());
    }

    public function test_setting_bool_returns_true_if_setting_exists_and_true()
    {
        $settingModel = new SystemSetting();
        $settingModel->value = 'true';

        $this->settingRepository->expects($this->once())
            ->method('findByField')
            ->with('key', 'feature_x')
            ->willReturn(collect([$settingModel]));

        $reflected = new ReflectionClass(ConfigurationContextResolver::class);
        $method = $reflected->getMethod('settingBool');
        $method->setAccessible(true);

        $result = $method->invoke($this->context, 'feature_x', 'config.fallback');
        $this->assertTrue($result);
    }

    public function test_setting_bool_returns_config_if_no_setting()
    {
        config()->set('config.fallback', true);

        $this->settingRepository->expects($this->once())
            ->method('findByField')
            ->with('key', 'missing_setting')
            ->willReturn(collect());

        $reflected = new ReflectionClass(ConfigurationContextResolver::class);
        $method = $reflected->getMethod('settingBool');
        $method->setAccessible(true);

        $result = $method->invoke($this->context, 'missing_setting', 'config.fallback');
        $this->assertTrue($result);
    }
}
