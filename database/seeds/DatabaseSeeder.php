<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Clear tables
        DB::table('menus')->delete();
        DB::table('dishes')->delete();
        DB::table('users')->delete();

        // Call seeder classes
        $this->call(UsersTableSeeder::class);
        $this->call(DishesTableSeeder::class);
        $this->call(MenusTableSeeder::class);

        Model::reguard();
    }
}
