<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Dish;
use App\Meal;

class MealControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test resource list
     */
    public function testDishList()
    {
        // Getting data from seeders
        Artisan::call('migrate');
        Artisan::call('db:seed', array('--class' => 'UsersTableSeeder'));
        Artisan::call('db:seed', array('--class' => 'DishesTableSeeder'));
        Artisan::call('db:seed', array('--class' => 'MealsTableSeeder'));

        $user = User::find(1);
        $this->actingAs($user)
             ->get("/api/0.1/users/{user->username}/meals")
             ->seeJson()
             ->seeStatusCode(200);
    }

}
