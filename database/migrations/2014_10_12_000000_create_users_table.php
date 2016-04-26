<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id'); // Default
            $table->string('username', 20)->unique(); // Default
            $table->string('password', 60); // Default
            $table->string('email')->unique(); // Default
            $table->string('first_name');
            $table->string('last_name');
            $table->string('api_token', 60)->unique()->default(str_random(60));
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
