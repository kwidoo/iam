<?php

use App\Enums\RuleAction;
use App\Enums\RuleOperator;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTable extends Migration
{
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->enum('operator', array_column(RuleOperator::cases(), 'value'))->default(RuleOperator::and);
            $table->enum('action', array_column(RuleAction::cases(), 'value'))->default(RuleAction::allow);
            $table->integer('order')->nullable();
            $table->json('conditions')->default('{}');
            $table->uuid('rule_group_uuid')->nullable();
            $table->foreign('rule_group_uuid')->references('uuid')->on('rule_groups');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rules');
    }
}
