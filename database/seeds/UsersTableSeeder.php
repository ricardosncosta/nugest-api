<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class)->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@email.com',
            'password' => bcrypt('testpassword'),
        ]);
    }
}
