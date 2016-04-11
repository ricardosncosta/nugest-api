<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Meal routes
Route::group(['prefix' => 'user/meals', 'middleware' => 'auth', 'as' => 'meal::'], function () {
    // List (get) and delete (post)
    Route::get('/', [
        'as'   => 'list_get',
        'uses' => 'Meal\MealController@getList',
    ]);

    // Create
    Route::get('/create', [
        'as'   => 'create_get',
        'uses' => 'Meal\MealController@getCreate',
    ]);
    Route::post('/create', [
        'as'   => 'create_post',
        'uses' => 'Meal\MealController@postCreate',
    ]);

    // Update
    Route::get('/update/{id}', [
        'as' => 'update_get', 'uses' => 'Meal\MealController@getUpdate',
    ])->where('id', '[0-9]+');
    Route::post('/update/{id}', [
        'as' => 'update_post', 'uses' => 'Meal\MealController@postUpdate',
    ])->where('id', '[0-9]+');

    // Delete
    Route::get('/delete/{id}', [
        'as' => 'delete_get', 'uses' => 'Meal\MealController@getDelete',
    ])->where('id', '[0-9]+');
});

/**
 * API v0.1 routes
 */
Route::group(['prefix' => '/api/0.1', 'middleware' => 'cors'], function () {
    Route::post('/signin', ['uses' => 'User\UserController@signin']);
    Route::get('/signout', ['uses' => 'Auth\AuthController@getLogout']);

    // Users
    Route::group(['prefix' => '/users'], function () {
        // Create
        Route::post('', ['uses' => 'User\UserController@store']);

        // Confirm
        Route::put('/confirm/{email}/{token}', [
            'uses' => 'User\UserController@emailConfirmation'
        ]);

        // Authenticated users
        Route::group(['prefix' => '/{username}'], function () {
            // User
            Route::put('', ['uses' => 'User\UserController@update']);
            Route::put('/email', ['uses' => 'User\UserController@updateEmail']);
            Route::put('/password', ['uses' => 'User\UserController@updatePassword']);
            Route::delete('', ['uses' => 'User\UserController@destroy']);

            // User dishes
            Route::group(['prefix' => '/dishes'], function () {
                // List
                Route::get('', ['uses' => 'Dish\DishController@index']);

                // Create
                Route::post('', ['uses' => 'Dish\DishController@store']);
            });
        });

        // Password reset link request and reset routes
        Route::post('/passwordreset', ['uses' => 'User\UserController@passwordResetRequest']);
        Route::put('/passwordreset/{token}', ['uses' => 'User\UserController@passwordReset']);
    });

    // Meals
    Route::group(['prefix' => 'user/meals', 'middleware' => 'auth', 'as' => 'meal::'], function () {
        // List (get) and delete (post)
        Route::get('/', ['uses' => 'Meal\MealController@getList']);

        // Create
        Route::get('/create', ['uses' => 'Meal\MealController@getCreate']);
        Route::post('/create', ['uses' => 'Meal\MealController@postCreate']);
        // Update
        Route::get('/update/{id}', ['uses' => 'Meal\MealController@getUpdate'])->where('id', '[0-9]+');
        Route::post('/update/{id}', ['uses' => 'Meal\MealController@postUpdate'])->where('id', '[0-9]+');

        // Delete
        Route::get('/delete/{id}', ['uses' => 'Meal\MealController@getDelete'])->where('id', '[0-9]+');
    });
});
