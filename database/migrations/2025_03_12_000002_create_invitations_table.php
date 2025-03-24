<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvitationsTable extends Migration
{
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('organization_id');
            $table->uuid('invited_by'); // The user who sent the invite.
            // Selectable contact options: email, phone, or code.
            $table->enum('contact_type', ['email', 'phone', 'code']);
            $table->string('contact_value');
            $table->string('token')->unique();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('uuid')->on('organizations')
                ->onDelete('cascade');
            $table->foreign('invited_by')
                ->references('uuid')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('invitations');
    }
}
