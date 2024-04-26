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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('type');
            $table->uuid('user_uuid');
            $table->foreign('user_uuid')->references('uuid')->on('users');
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->string('type_for_unique')->virtualAs('IF(deleted_at IS NULL, type, NULL)');

            // Adding a unique index on user_uuid, type, and deleted_at
            $table->unique(['user_uuid', 'type_for_unique'], 'user_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
