<?php

use App\User;
use App\Dish;
use App\Meal;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MealTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->setupDb();
    }

    /**
     * Sets up DB data to test
     */
    public function setupDB()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed', array('--class' => 'UsersTableSeeder'));
        Artisan::call('db:seed', array('--class' => 'DishesTableSeeder'));
        Artisan::call('db:seed', array('--class' => 'MealsTableSeeder'));
    }

    /**
     * Test getLastMeals method
     *
     * @return void
     */
    public function testGetLastMealsMethod()
    {
        $user = App\User::find(1);

        $mealModel = new Meal();
        $this->assertCount(60, $mealModel->getLastMeals($user));
    }

    /**
     * Test getRecommendation method
     *
     * @return void
     */
    public function testGetRecommendationMethod()
    {
        $user = User::find(1);

        $mealModel = new Meal();
        $recommendedDish = $mealModel->getRecommendation($user);
        $this->assertEquals(4, $recommendedDish->id);
    }

    /**
     * Test getRecommendation method returns null if no results is found
     *
     * @return void
     */
    public function testGetRecommendationReturnsNullIfNoResulFound()
    {
        // Create a new user, without any related dishes
        $user = factory(User::class)->create();

        $mealModel = new Meal();
        $recommendedDish = $mealModel->getRecommendation($user);
        $this->assertTrue($recommendedDish == null);
    }

    /**
     * Test getRecommendation() method signature throws error exception
     *
     * @return void
     */
    public function testGetRecommendationMethodSignatureThrowsErrorExceptionWithNonIntValue()
    {
        $mealModel = new Meal();

        $this->setExpectedException('ErrorException');
        $recommendedDish = $mealModel->getRecommendation($user, '12');
    }
}
