<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    // Define the command signature and description
    protected $signature = 'make:service {name : The model name for which to generate the service files}';
    protected $description = 'Generate a service contract and implementation files with binding snippet for AppServiceProvider';

    public function handle()
    {
        $name = $this->argument('name');
        $serviceName = ucfirst($name) . 'Service';
        $eventKey = strtolower($name);

        // Define file paths for the contract and the service class
        $contractPath = app_path("Contracts/Services/{$serviceName}.php");
        $servicePath = app_path("Services/{$serviceName}.php");

        // Check if the contract file already exists
        if (File::exists($contractPath)) {
            $this->error("Contract file already exists: {$contractPath}");
            return 1;
        }

        // Check if the service file already exists
        if (File::exists($servicePath)) {
            $this->error("Service file already exists: {$servicePath}");
            return 1;
        }

        // Generate the contract file content
        $contractContent = <<<PHP
<?php

namespace App\Contracts\Services;

use Kwidoo\Mere\Contracts\BaseService;

interface {$serviceName} extends BaseService {}
PHP;

        // Generate the service file content
        $serviceContent = <<<PHP
<?php

namespace App\Services;

use App\Contracts\Services\\{$serviceName} as {$serviceName}Contract;
use App\Contracts\Repositories\\{$serviceName}Repository;
use Kwidoo\Mere\Services\BaseService;
use Kwidoo\Mere\Contracts\MenuService;

class {$serviceName} extends BaseService implements {$serviceName}Contract
{
    public function __construct(MenuService \$menuService, {$serviceName}Repository \$repository)
    {
        parent::__construct(\$menuService, \$repository);
    }

    protected function eventKey(): string
    {
        return '{$eventKey}';
    }
}
PHP;

        // Ensure the target directories exist
        if (!File::isDirectory(app_path('Contracts/Services'))) {
            File::makeDirectory(app_path('Contracts/Services'), 0755, true);
        }
        if (!File::isDirectory(app_path('Services'))) {
            File::makeDirectory(app_path('Services'), 0755, true);
        }

        // Write the contract and service files
        File::put($contractPath, $contractContent);
        File::put($servicePath, $serviceContent);

        $this->info("Contract created at: {$contractPath}");
        $this->info("Service created at: {$servicePath}");

        // Provide the binding snippet for the AppServiceProvider
        $bindingSnippet = <<<PHP
// In your AppServiceProvider's register method, add:
use App\Contracts\Services\\{$serviceName} as {$serviceName}Contract;
use App\Services\\{$serviceName};

\$this->app->bind({$serviceName}Contract::class, {$serviceName}::class);
PHP;

        $this->info("\nAdd the following binding to your AppServiceProvider:\n");
        $this->line($bindingSnippet);

        return 0;
    }
}
