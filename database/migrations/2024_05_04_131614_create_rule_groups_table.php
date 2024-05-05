<?php

use App\Enums\RuleGroupType;
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
        Schema::create('rule_groups', function (Blueprint $table) {
            $table->id('id');
            $table->uuid('uuid')->unique()->default(DB::raw('UUID()'));
            $table->enum('type', array_column(RuleGroupType::cases(), 'value'))->default(RuleGroupType::inherit->value);
            $table->string('entity_type');
            $table->uuid('entity_uuid')->nullable();
            $table->uuid('user_uuid')->nullable();
            $table->foreign('user_uuid')->references('uuid')->on('users');
            $table->string('description')->nullable();
            $table->nestedSet();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared('
            CREATE TRIGGER before_insert_rule_groups
            BEFORE INSERT ON rule_groups
            FOR EACH ROW
            BEGIN
                IF NEW.user_uuid IS NOT NULL AND NEW.parent_id IS NULL THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Cannot set user_uuid when parent_id is NULL\';
                END IF;

                IF NEW.entity_uuid IS NOT NULL AND NEW.parent_id IS NULL THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Cannot set entity_uuid when parent_id is NULL\';
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER before_update_rule_groups
            BEFORE UPDATE ON rule_groups
            FOR EACH ROW
            BEGIN
                IF NEW.user_uuid IS NOT NULL AND NEW.parent_id IS NULL THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Cannot set user_uuid when parent_id is NULL\';
                END IF;

                IF NEW.entity_uuid IS NOT NULL AND NEW.parent_id IS NULL THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'Cannot set entity_uuid when parent_id is NULL\';
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_rule_groups');
        DB::unprepared('DROP TRIGGER IF EXISTS before_update_rule_groups');

        Schema::dropIfExists('rule_groups');
    }
};
