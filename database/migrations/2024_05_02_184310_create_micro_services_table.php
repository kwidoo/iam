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
        Schema::create('micro_services', function (Blueprint $table) {
            $table->uuid();
            $table->string('name');
            $table->string('slug');
            $table->uuid('client_id');
            $table->foreign('client_id')->references('id')->on('oauth_clients');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_services');
    }
};
