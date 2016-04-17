<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'menus';

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
     * Get menu recomendation for user. Returns the most eaten menu that doesn't
     * match any of last week menus (by default), or the least eaten dish if all match
     * @param $skipMenus Number of (last) menus to not repeat
     */
     public function getRecommendation(User $user, $unrepeatdMenus = 14)
     {
        if (!is_int($unrepeatdMenus))
            throw new ErrorException("Argument $unrepeatdMenus must be of type integer.");

        $menus = $this->getLastMenus($user);
        $lastMenus = array_slice($menus->toArray(), count($menus) - $unrepeatdMenus);

        if (count($lastMenus)) {
            $sortedMenus = $this->sortMenus($menus->toArray());
            foreach ($sortedMenus as $dishId => $menuCount) {
                $exists = false;
                foreach ($lastMenus as $key => $lastDishId) {
                    if ($dishId === (int) $lastDishId) {
                        $exists = true;
                    }
                }

                if ($exists === false) {
                    return Dish::find($dishId);
                }
            }

            // If repeated, suggest the least eaten dish
            end($sortedMenus);
            return Dish::find(key($sortedMenus));
        } else {
            return null;
        }
     }

     /**
      * Returns last $menuCount menus.
      * 60 equals a month worth of menus (lunch, dinner)
      *
      * @param App\User object $user to look for menus
      * @param int $menuCount number of menus to retrieve
      *                       60 equals a month worth of menus (lunch and dinner)
      * @return array Ordered distinct dish and dish match count in $menuCount,
      *               from most eaten to least.
      */
     public function getLastMenus(User $user, $menuCount = 60)
     {
        return self::where('user_id', $user->id)
                   ->orderBy('datetime', 'DESC')
                   ->take($menuCount) // A month worth of menus
                   ->lists('dish_id');
     }

    /**
     * Count and sort menus according to distinct dish match count in descent order
     *
     * @param array $menus Menu data to parse
     * @return array Sorted menu count
     */
     public function sortMenus(Array $menus)
     {
        // Pass data onto array, along with dish count
        $menuCount = [];
        foreach ($menus as $key => $dishId) {
            if (isset($menuCount[$dishId]))
                $menuCount[$dishId] += 1;
            else
                $menuCount[$dishId] = 1;
        }

        // Sort menus by match count
        arsort($menuCount, SORT_NUMERIC);
        return $menuCount;
     }


}
