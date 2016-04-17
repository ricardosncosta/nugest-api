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

    /**
     * Test meal creation
     */
    public function testMealCreation()
    {
        // Setup needed data
        $user = factory(User::class)->create();
        $dish = factory(Dish::class)->create(['user_id' => $user->id]);

        // Test validation
        $data = ['dish' => ''];
        $this->actingAs($user)
             ->post("/api/0.1/users/{user->username}/meals", $data)
             ->seeJsonEquals(['The dish id field is required.']);

        // Test functionality
        $datetime = new \DateTime();
        $data = ['dish_id' => $dish->id, 'datetime' => $datetime->format('Y-m-d H:i:s')];
        $this->actingAs($user)
             ->post("/api/0.1/users/{user->username}/meals", $data)
             ->seeJsonContains($data)
             ->seeStatusCode(201);

        $this->seeInDatabase('meals', $data);
    }

    /**
     * Test meal update
     */
    public function testMealUpdate()
    {
        // Setup needed data
        $user = factory(User::class)->create();
        $dishes = factory(Dish::class, 2)->create(['user_id' => $user->id]);
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
            'dish_id' => $dishes[0]->id
        ]);

        // Validation check
        $data = ['dish_id' => ''];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/meals/{$meal->id}", $data)
             ->seeJsonEquals(['The dish id field is required.']);

        // not found, 404
        $data = ['dish_id' => 5];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/meals/20", $data)
             ->seeJsonEquals(['Error' => 'Meal could not be found.'])
             ->seeStatusCode(404);

        // Dish not found check
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/meals/{$meal->id}", $data)
             ->seeJsonEquals(['Error' => 'Dish could not be found.'])
             ->seeStatusCode(404);

        // Functionality check
        // Obs: Used "(string) $dishes[1]->id" to match returning json data
        $dateTime = new \DateTime();
        $data = [
            'dish_id'  => (string) $dishes[1]->id,
            'datetime' => $dateTime->format('Y-m-d H:i:s')
        ];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/meals/{$meal->id}", $data)
             ->seeJsonContains($data)
             ->seeStatusCode(200);

        $this->seeInDatabase('meals', [
            'id'       => $meal->id,
            'user_id'  => $user->id,
            'dish_id'  => $data['dish_id'],
            'datetime' => $data['datetime']
        ]);
    }

}
