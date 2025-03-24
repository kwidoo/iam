<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->uuid('owner_id')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')
                ->references('uuid')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
