<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iam:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the IAM service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing IAM service...');
        $userReadModel = config('iam.user_read_model');
        $userWriteModel = config('iam.user_write_model');
        $this->info('User model: ' . config('iam.user_read_model'));

        $stubPath = app_path('Console/Commands/stubs/CreateUserProfile.php.stub');
        $destinationPath = app_path("Models/$userReadModel.php");

        if (!File::exists($stubPath)) {
            $this->error('Stub file does not exist: ' . $stubPath);
            return;
        }

        $stub = File::get($stubPath);

        // Determine the base model class
        $modelBaseClass = config('iam.read_connection') === 'mongodb'
            ? 'MongoDB\Laravel\Eloquent\Model'
            : 'Illuminate\Database\Eloquent\Model';

        $tableType = config('iam.read_connection') === 'mongodb' ? 'collection' : 'table';

        $database = config('iam.read_connection');

        $table = Str::plural(Str::snake($userReadModel));

        // Replace placeholders
        $stub = str_replace('{{modelBaseClass}}', $modelBaseClass, $stub);
        $stub = str_replace('{{database}}', $database, $stub);
        $stub = str_replace('{{tableType}}', $tableType, $stub);
        $stub = str_replace('{{table}}', $table, $stub);
        $stub = str_replace('{{modelName}}', $userReadModel, $stub);


        File::put($destinationPath, $stub);

        // Generate migration for MariaDB
        if ($database !== 'mongodb') {
            $migrationPath = database_path('migrations/' . date('Y_m_d_His') . '_create_user_profiles_table.php');
            $migrationStubPath = app_path('Console/Commands/stubs/CreateUserProfileMariaDB.php.stub');

            if (!File::exists($migrationStubPath)) {
                $this->error('Migration stub file does not exist: ' . $migrationStubPath);
                return;
            }

            $migrationStub = File::get($migrationStubPath);
            $migrationStub = str_replace('{{table}}', $table, $migrationStub);

            File::put($migrationPath, $migrationStub);

            $this->info('Migration for user profiles table successfully generated.');
        }

        $this->info("UserProfile model successfully generated for {$modelBaseClass}.");
    }
}
