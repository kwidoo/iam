<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('fname');
            $table->string('lname');
            $table->date('dob')->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->uuid('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('full_name')->virtualAs("CONCAT(fname, ' ', lname)");
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unique(['user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('profiles');
    }
};
