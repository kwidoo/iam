<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationUserTable extends Migration
{
    public function up()
    {
        Schema::create('organization_user', function (Blueprint $table) {
            $table->uuid('organization_id');
            $table->uuid('user_id');
            $table->string('role')->default('member'); // Predefined roles: owner, admin, member.
            $table->timestamps();

            $table->primary(['organization_id', 'user_id']);
            $table->foreign('organization_id')
                ->references('uuid')->on('organizations')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('uuid')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('organization_user');
    }
}
