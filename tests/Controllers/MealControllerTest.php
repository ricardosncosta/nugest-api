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
     * Test meal creation
     *     *
     * @return void
     */
    public function testMealCreation()
    {
        $user = factory(User::class)->create();
        $dish = factory(Dish::class)->create(['user_id' => $user->id]);
        $dateTime = new \DateTime();
        $dateTimeStr = $dateTime->format('Y-m-d H:i:s');
        $this->actingAs($user)
             ->visit(route('meal::create_get'))
             ->select($dish->id, 'dish')
             ->select($dateTimeStr, 'datetime')
             ->press('Create')
             ->seePageIs(route('meal::list_get'))
             ->see('Meal created.');

        $this->seeInDatabase('meals', [
            'user_id'  => $user->id,
            'dish_id'  => $dish->id,
            'datetime' => $dateTimeStr
        ]);
    }

    /**
     * Test meal creation
     *     *
     * @return void
     */
    public function testMealUpdate()
    {
        # User
        $user = factory(User::class)->create();

        # Diash #1 abd #2
        $dish = factory(Dish::class)->create(['user_id' => $user->id]);
        $dish2 = factory(Dish::class)->create(['user_id' => $user->id]);

        # DateTime
        $dateTime = new \DateTime('-2 days');
        $dateTimeStr = $dateTime->format('Y-m-d H:i:s');

        # Meal
        $meal = factory(Meal::class)->create([
            'user_id' => $user->id,
            'dish_id' => $dish->id
        ]);

        // Test date times are different.
        $this->assertTrue($meal->datetime != $dateTime);

        // Update data using form
        $this->actingAs($user)
             ->visit(route('meal::update_get', ['id' => $dish->id]))
             ->select($dish2->id, 'dish')
             ->select($dateTimeStr, 'datetime')
             ->press('Update')
             ->seePageIs(route('meal::list_get'))
             ->see('Meal updated.');

        // Test meal updated with $dish (#2) associated to it and a new datetime.
        $this->seeInDatabase('meals', [
            'user_id'  => $user->id,
            'dish_id'  => $dish2->id,
            'datetime' => $dateTimeStr
        ]);
    }

}
