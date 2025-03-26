<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('organization_profile', function (Blueprint $table) {
            $table->uuid('organization_id');
            $table->uuid('profile_id');

            $table->unique(['organization_id', 'profile_id']);

            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade');
            $table->foreign('profile_id')
                ->references('id')->on('profiles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profile');
    }
};
