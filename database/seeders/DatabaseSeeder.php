<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() :void
    {
        /*
         * Truncate tables before seeding the tables
         * */

        User::truncate();
        Post::truncate();

        /*
         * First create user with Admin role : This will be the default user
         * */

        User::create([
            'name' => "Little Rock",
            'email' => "littlerock@squared.com",
            'phone' => "255789016235",
            'user_type' =>"admin",
            'password' => bcrypt("!!Sisitu12"),
        ]);

        /*
         * Seed the Posts Table : As it stands all Posts created will have s single Author
         * */

        Post::factory(10)->create();

    }
}
