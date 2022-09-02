<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() :void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('phone', 15)->nullable();
            $table->string('email', 100)->unique();
            $table->enum('user_type', ['user', 'admin'])->default('user');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() :void
    {
        Schema::dropIfExists('users');
    }
}
