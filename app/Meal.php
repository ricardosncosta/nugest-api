<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'meals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id' ,'dish_id', 'datetime'];

    /**
     * The attributes that stops created_at/updated_at automatic behavior.
     *
     * @var array
     */
    public $timestamps = false;

    /**
     * Get associated dish object
     */
     public function dish()
     {
         return $this->belongsTo('App\Dish');
     }

    // Applying date mutator
    public function getDates()
    {
        return ['datetime'];
    }

    /**
     * Get meal recomendation for user. Returns the most eaten meal that doesn't
     * match any of last week meals (by default), or the least eaten dish if all match
     * @param $skipMeals Number of (last) meals to not repeat
     */
     public function getRecommendation(User $user, $unrepeatdMeals = 14)
     {
        if (!is_int($unrepeatdMeals))
            throw new ErrorException("Argument $unrepeatdMeals must be of type integer.");

        $meals = $this->getLastMeals($user);
        $lastMeals = array_slice($meals->toArray(), count($meals) - $unrepeatdMeals);

        if (count($lastMeals)) {
            $sortedMeals = $this->sortMeals($meals->toArray());
            foreach ($sortedMeals as $dishId => $mealCount) {
                $exists = false;
                foreach ($lastMeals as $key => $lastDishId) {
                    if ($dishId === (int) $lastDishId) {
                        $exists = true;
                    }
                }

                if ($exists === false) {
                    return Dish::find($dishId);
                }
            }

            // If repeated, suggest the least eaten dish
            end($sortedMeals);
            return Dish::find(key($sortedMeals));
        } else {
            return null;
        }
     }

     /**
      * Returns last $mealCount meals.
      * 60 equals a month worth of meals (lunch, dinner)
      *
      * @param App\User object $user to look for meals
      * @param int $mealCount number of meals to retrieve
      *                       60 equals a month worth of meals (lunch and dinner)
      * @return array Ordered distinct dish and dish match count in $mealCount,
      *               from most eaten to least.
      */
     public function getLastMeals(User $user, $mealCount = 60)
     {
        return self::where('user_id', $user->id)
                   ->orderBy('datetime', 'DESC')
                   ->take($mealCount) // A month worth of meals
                   ->lists('dish_id');
     }

    /**
     * Count and sort meals according to distinct dish match count in descent order
     *
     * @param array $meals Meal data to parse
     * @return array Sorted meal count
     */
     public function sortMeals(Array $meals)
     {
        // Pass data onto array, along with dish count
        $mealCount = [];
        foreach ($meals as $key => $dishId) {
            if (isset($mealCount[$dishId]))
                $mealCount[$dishId] += 1;
            else
                $mealCount[$dishId] = 1;
        }

        // Sort meals by match count
        arsort($mealCount, SORT_NUMERIC);
        return $mealCount;
     }


}
