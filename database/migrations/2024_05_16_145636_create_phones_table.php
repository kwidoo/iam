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
        Schema::create('phones', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('country_code');
            $table->string('phone');
            $table->uuid('user_uuid');
            // Adding a virtual field for concatenation
            $table->string('full_phone')->virtualAs("CONCAT(country_code, phone)");

            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key relating emails to users
            $table->foreign('user_uuid')->references('uuid')->on('users');

            // Index for general querying
            $table->index(['country_code', 'phone', 'user_uuid'], 'phone_user_uuid_index');

            // Adding a unique index on the concatenated field considering soft delete
            $table->unique(['country_code', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
