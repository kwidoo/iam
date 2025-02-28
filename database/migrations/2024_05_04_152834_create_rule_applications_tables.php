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
        Schema::create('entity_rules', function (Blueprint $table) {
            $table->unsignedBigInteger('rule_group_id');
            $table->string('entity_type');
            $table->uuid('entity_uuid');

            $table->foreign('rule_group_id')->references('id')->on('rule_groups');
            $table->primary(['rule_group_id', 'entity_type', 'entity_uuid']);
        });
    }
};
