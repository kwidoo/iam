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
        Schema::create('emails', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('email');
            $table->uuid('user_uuid');
            $table->boolean('is_primary')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key relating emails to users
            $table->foreign('user_uuid')->references('uuid')->on('users');

            // Index for general querying
            $table->index(['email', 'user_uuid'], 'email_user_uuid_index');

            // Generated columns for conditional unique constraints
            $table->string('value_for_unique')->virtualAs('IF(deleted_at IS NULL, email, NULL)');
            $table->string('user_uuid_for_primary')->virtualAs('IF(deleted_at IS NULL AND is_primary = 1, user_uuid, NULL)');

            // Adjusting the unique index to ensure global uniqueness of emails when not deleted
            $table->unique('value_for_unique', 'emails_email_deleted_idx');

            // Unique index to ensure only one primary email per user when not deleted
            $table->unique('user_uuid_for_primary', 'emails_primary_user_uuid_deleted_idx');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emails');
    }
};
