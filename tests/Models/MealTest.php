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

    /**
     * Dish creation helper method
     */
    private function batchDishCreate(User $user, $dishCount = 5)
    {
        for ($i=0; $i < $dishCount; $i++) {
            $dish = factory(Dish::class)->make(['user_id' => $user->id]);
            $dish->save();

            $dishes[] = $dish;
        }

        return $dishes;
    }

    /**
     * Meal creation helper method
     *
     * @param App/User object $user The User the meal will be associated with
     * @param DateTime object $startDate The starting Date to create meals from.
     *                                   If $startDate is one month ago it
     *                                   will create a month of meals until now,
     *                                   (Lunch and Dinner)
     */
    private function batchMealCreate(User $user, $dishes, DateTime $startDate)
    {
        if (!is_array($dishes))
            throw new Exception("Argument $dishes must be of type array.");

        $dishCount = count($dishes) - 1;
        $curDateTime = clone($startDate);
        $dayDiff = $startDate->diff(new DateTime())->format('%a');
        for ($i=0; $i <= $dayDiff; $i++) {
            $changeDay = false;
            while ($changeDay == false) {
                // Get a mew dish, different than the one before, preferably
                $dish = $dishes[rand(0, $dishCount)];
                if (isset($meals[$i-1])) {
                    while ($dish->id == $meals[$i-1]->dish_id) {
                        $dish = $dishes[rand(0, $dishCount)];
                    }
                }

                // Check time for lunch or dinner,
                if ($curDateTime->format('H') == 0) {
                    $curDateTime->setTime(rand(12,14), rand(0,60));
                } else {
                    $curDateTime->setTime(rand(19,21), rand(0,60));
                    $changeDay = true;
                }

                $meal = factory(Meal::class)->make([
                    'user_id'    => $user->id,
                    'dish_id'    => $dish->id,
                    'created_at' => $curDateTime
                ]);
                $meal->save();
                $meals[] = $meal;
            }

            $curDateTime->add(new \DateInterval('P1D'));
            $curDateTime->setTime(0, 0);
        }

        return $meals;
    }


    /**
     * Test getLastMeals method
     *
     * @return void
     */
    public function testGetLastMealsMethod()
    {
        $user = factory(User::class)->create();
        $dishes = $this->batchDishCreate($user, 8);

        $startDate = new \DateTime();
        $startDate->sub(new \DateInterval('P1M'));
        $this->batchMealCreate($user, $dishes, $startDate);

        $mealModel = new Meal();
        $meals = $mealModel->getLastMeals($user);
        $this->assertCount(60, $meals);
    }

    /**
     * Test getRecommendation method
     *
     * @return void
     */
    public function testGetRecommendationMethod()
    {
        $user = factory(User::class)->create();
        $dishes = $this->batchDishCreate($user, 16);

        $startDate = new \DateTime();
        $startDate->sub(new \DateInterval('P1M'));
        $this->batchMealCreate($user, $dishes, $startDate);

        $mealModel = new Meal();
        $lastMeals = $mealModel->getLastMeals($user);

        $recommendedDish = $mealModel->getRecommendation($user);
        $this->assertTrue($recommendedDish instanceof Dish);

        $unrepeatedMeals = array_slice($lastMeals->toArray(), count($lastMeals) - 14);
        foreach ($unrepeatedMeals as $key => $dishId)
            $this->assertTrue($dishId != $recommendedDish->id);
    }

    /**
     * Test getRecommendation method
     *
     * @return void
     */
    public function testGetRecommendationReturnsNullIfNoResulFound()
    {
        $user = factory(User::class)->create();

        $mealModel = new Meal();
        $recommendedDish = $mealModel->getRecommendation($user);
        $this->assertTrue($recommendedDish == null);
    }

    /**
     * Test getRecommendation() method signature throws error exception
     *     *
     * @return void
     */
    public function testGetRecommendationMethodSignatureThrowsErrorExceptionWithNonIntValue()
    {
        $mealModel = new Meal();

        $this->setExpectedException('ErrorException');
        $recommendedDish = $mealModel->getRecommendation($user, '12');
    }
}
