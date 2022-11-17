<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtherColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telephone')->nullable();
            $table->string('address')->nullable();
            $table->string('additional_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('ville')->nullable();
            $table->enum('role',[env("SIMPLE_USER_ROLE"),env("ADMIN_ROLE"),env("EMPLOYEE_ROLE")])->default(env("SIMPLE_USER_ROLE"));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
