
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->uuid('organization_id')->nullable()->after('id')->index();
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->uuid('organization_id')->nullable()->after('id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('organization_id');
        });
    }
};
