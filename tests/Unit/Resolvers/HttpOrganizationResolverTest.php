<?php

namespace Tests\Unit\Resolvers;

use App\Contracts\Repositories\OrganizationRepository;
use App\Contracts\Repositories\SystemSettingRepository;
use App\Enums\RegistrationFlow;
use App\Models\Organization;
use App\Resolvers\HttpOrganizationResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class HttpOrganizationResolverTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_resolve_returns_main_organization_when_force_main_org_enabled()
    {
        // Create a fake organization to be returned as the main organization.
        $mainOrganization = Organization::factory()->make(['id' => 1]);

        // Create a repository mock that expects getMainOrganization() to be called.
        $repositoryMock = Mockery::mock(OrganizationRepository::class);
        $repositoryMock->shouldReceive('getMainOrganization')
            ->once()
            ->andReturn($mainOrganization);

        // For this test the setting should indicate that the main organization is forced.
        $settingRepositoryMock = Mockery::mock(SystemSettingRepository::class);
        // Return a collection containing a setting object whose value will force the MAIN_ONLY branch.
        $setting = (object)['value' => RegistrationFlow::MAIN_ONLY];
        $settingRepositoryMock->shouldReceive('findByField')
            ->with('key', 'registration.force_main_org')
            ->andReturn(new Collection([$setting]));

        // Use a partial mock to override the protected settingBool() method.
        $resolver = $this->getMockBuilder(HttpOrganizationResolver::class)
            ->setConstructorArgs([$repositoryMock, $settingRepositoryMock])
            ->onlyMethods(['settingBool'])
            ->getMock();

        $resolver->method('settingBool')
            ->with('registration.force_main_org', 'iam.defaults.force_main_org')
            ->willReturn(RegistrationFlow::MAIN_ONLY);

        // The resolve() call should return the main organization.
        $result = $resolver->resolve('irrelevant');
        $this->assertEquals($mainOrganization, $result);
    }

    public function test_resolve_returns_organization_by_slug_when_force_main_org_disabled()
    {
        $slug = 'test-org';
        $expectedOrganization = Organization::factory()->make(['id' => 2]);

        // Use shouldIgnoreMissing() so that undefined methods like where() can be chained.
        $repositoryMock = Mockery::mock(OrganizationRepository::class)->shouldIgnoreMissing();

        // Create a query builder mock to simulate the chain: where() -> orWhere() -> first()
        $queryBuilderMock = Mockery::mock();
        $repositoryMock->shouldReceive('where')
            ->with('slug', $slug)
            ->once()
            ->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('orWhere')
            ->with('id', $slug)
            ->once()
            ->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('first')
            ->once()
            ->andReturn($expectedOrganization);

        // For this test the setting should indicate that force-main is disabled.
        $settingRepositoryMock = Mockery::mock(SystemSettingRepository::class);
        $settingRepositoryMock->shouldReceive('findByField')
            ->with('key', 'registration.force_main_org')
            ->andReturn(new Collection([]));

        // Use a partial mock to override settingBool()
        $resolver = $this->getMockBuilder(HttpOrganizationResolver::class)
            ->setConstructorArgs([$repositoryMock, $settingRepositoryMock])
            ->onlyMethods(['settingBool'])
            ->getMock();

        $resolver->method('settingBool')
            ->with('registration.force_main_org', 'iam.defaults.force_main_org')
            ->willReturn(false);

        $result = $resolver->resolve($slug);
        $this->assertEquals($expectedOrganization, $result);
    }

    public function test_resolve_uses_subdomain_when_name_and_route_not_provided()
    {
        $subdomain = 'suborg';
        $expectedOrganization = Organization::factory()->make(['id' => 3]);

        // Create a fake request with a host that includes a subdomain.
        $request = Request::create("http://{$subdomain}.example.com");
        $this->app['request'] = $request;

        $repositoryMock = Mockery::mock(OrganizationRepository::class)->shouldIgnoreMissing();

        // Create a query builder mock for the chained methods.
        $queryBuilderMock = Mockery::mock();
        $repositoryMock->shouldReceive('where')
            ->with('slug', $subdomain)
            ->once()
            ->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('orWhere')
            ->with('id', $subdomain)
            ->once()
            ->andReturn($queryBuilderMock);
        $queryBuilderMock->shouldReceive('first')
            ->once()
            ->andReturn($expectedOrganization);

        $settingRepositoryMock = Mockery::mock(SystemSettingRepository::class);
        $settingRepositoryMock->shouldReceive('findByField')
            ->with('key', 'registration.force_main_org')
            ->andReturn(new Collection([]));

        // Partial mock to override settingBool().
        $resolver = $this->getMockBuilder(HttpOrganizationResolver::class)
            ->setConstructorArgs([$repositoryMock, $settingRepositoryMock])
            ->onlyMethods(['settingBool'])
            ->getMock();

        $resolver->method('settingBool')
            ->with('registration.force_main_org', 'iam.defaults.force_main_org')
            ->willReturn(false);

        // With no name provided, the resolver should use the subdomain.
        $result = $resolver->resolve(null);
        $this->assertEquals($expectedOrganization, $result);
    }

    public function test_resolve_throws_exception_when_slug_not_provided()
    {
        $repositoryMock = Mockery::mock(OrganizationRepository::class)->shouldIgnoreMissing();
        $settingRepositoryMock = Mockery::mock(SystemSettingRepository::class);
        $settingRepositoryMock->shouldReceive('findByField')
            ->with('key', 'registration.force_main_org')
            ->andReturn(new Collection([]));

        // Partial mock to override settingBool() and force the branch where slug is needed.
        $resolver = $this->getMockBuilder(HttpOrganizationResolver::class)
            ->setConstructorArgs([$repositoryMock, $settingRepositoryMock])
            ->onlyMethods(['settingBool'])
            ->getMock();

        $resolver->method('settingBool')
            ->with('registration.force_main_org', 'iam.defaults.force_main_org')
            ->willReturn(false);

        // When neither a name, route parameter, nor subdomain is provided, an exception should be thrown.
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No organization provided.');
        $resolver->resolve(null);
    }
}
