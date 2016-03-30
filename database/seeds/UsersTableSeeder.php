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
            'username'   => 'testuser',
            'email'      => 'testuser@email.com',
            'password'   => bcrypt('testpassword'),
            'first_name' => 'Test',
            'last_name'  => 'User',
        ]);
    }
}
