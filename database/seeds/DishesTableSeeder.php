<?php

use Illuminate\Database\Seeder;

class DishesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dishNames = [
            'Chicken Parmesano',
            'Salad Niçoise',
            'Turkey Marco Polo',
            'Tuna Marinara',
            'Salmon Louis Salad',
            'Chicken Kiev',
            'Pasta Primavera',
            'Stroganoff',
            'Duck à l\'orange',
            'Spaghetti Carbonara',
            'Creme du Barry',
            'Turkey Cordon Bleu',
            'Salsa Verde',
            'Mexican Burrito'
        ];

        foreach ($dishNames as $dishName) {
            factory(App\Dish::class)->create([
                'user_id' => 1,
                'name'    => $dishName
            ]);
        }

    }
}
