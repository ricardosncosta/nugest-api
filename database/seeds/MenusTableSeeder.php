<?php

use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Each line represents a week of dinner & lunch dishes
        // There's no #4 in last menus, and it appears 6 times through the array
        // Dish #4 must be the suggested dish.
        $orderedDishes = [
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 4, 12, 13, 14,
            1, 2, 3, 4, 5, 6, 7, 4, 9, 10, 11, 12, 13, 14,
            1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14,
            1, 2, 3, 1
        ];

        $dateTime = new \DateTime('-1 day');
        $dishCount = count($orderedDishes);
        for ($i=0; $i < $dishCount; $i++) {
            // If $i is even set time for lunch, else for dinner and change day
            $changeDay = false;
            if ($i % 2 === 0) {
                $dateTime->setTime(rand(12,14), rand(0,60));
            } else {
                $dateTime->setTime(rand(19,21), rand(0,60));
                $changeDay = true;
            }

            factory(App\Menu::class)->create([
                'user_id'  => 1,
                'dish_id'  => $orderedDishes[$i],
                'datetime' => $dateTime,
            ]);

            if ($changeDay === true) {
                $dateTime->sub(new \DateInterval('P1D'));
            }
        }

    }
}
