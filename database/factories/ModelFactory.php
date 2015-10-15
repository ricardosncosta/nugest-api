<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    $dateTime = $faker->dateTimeThisYear();
    return [
        'first_name' => $faker->firstName('male'),
        'last_name' => $faker->lastName('male'),
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
        'created_at' => $dateTime,
        'updated_at' => $dateTime
    ];
});

$factory->define(App\UserEmailChange::class, function (Faker\Generator $faker) {
    return [
        'email' => $faker->email,
        'token' => str_random(),
        'confirmed' => false,
        'created_at' => new DateTime(),
        'updated_at' => new DateTime()
    ];
});

$factory->define(App\Dish::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->sentence(rand(1, 5)),
        'calories' => rand(1, 2000),
    ];
});
