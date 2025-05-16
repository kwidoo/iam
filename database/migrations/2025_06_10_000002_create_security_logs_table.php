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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->string('event_type')->nullable();
            $table->uuid('client_id')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->string('request_method')->nullable();
            $table->string('status')->default('success');
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index('endpoint');
            $table->index('event_type');
            $table->index('client_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('ip_address');
            $table->index('created_at');

            // Add foreign keys, but make them nullable to allow logging even if user/client is deleted
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('client_id')
                ->references('id')
                ->on('oauth_clients')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
