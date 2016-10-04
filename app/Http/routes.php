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

/**
 * API v0.1 routes
 */
Route::group(['prefix' => '/api/0.1', 'middleware' => 'cors'], function () {
    Route::post('/auth/signin', ['uses' => 'User\AuthController@signIn']);
    Route::post('/auth/restore', ['uses' => 'User\AuthController@restore']);

    // Users
    Route::group(['prefix' => '/users'], function () {
        // Create
        Route::post('', ['uses' => 'User\UserController@store']);

        // Confirm
        Route::put('/confirm/{email}/{token}', [
            'uses' => 'User\UserController@emailConfirmation',
            'as' => 'email_confirmation'
        ]);

        // Authenticated users
        Route::group(['prefix' => '/{username}', 'middleware' => 'auth'], function () {
            // User
            Route::put('', ['uses' => 'User\UserController@update']);
            Route::put('/email', ['uses' => 'User\UserController@updateEmail']);
            Route::put('/password', ['uses' => 'User\UserController@updatePassword']);
            Route::delete('', ['uses' => 'User\UserController@destroy']);

            // Dishes
            Route::group(['prefix' => '/dishes'], function () {
                // List and create
                Route::get('', ['uses' => 'Dish\DishController@index']);
                Route::post('', ['uses' => 'Dish\DishController@store']);

                // Update, Delete
                Route::group(['prefix' => '/{dishId}'], function () {
                    Route::put('', ['uses' => 'Dish\DishController@update']);
                    Route::delete('', ['uses' => 'Dish\DishController@destroy']);
                });
            });

            // Menus
            Route::group(['prefix' => '/menus'], function () {
                // List (GET) and create (POST)
                Route::get('', ['uses' => 'Menu\MenuController@index']);
                Route::post('', ['uses' => 'Menu\MenuController@store']);

                // Update
                Route::group(['prefix' => '/{menuId}'], function () {
                    Route::put('', ['uses' => 'Menu\MenuController@update']);
                    Route::delete('', ['uses' => 'Menu\MenuController@destroy']);
                });
            });

        });

        // Password reset link request and reset routes
        Route::post('/passwordreset', ['uses' => 'User\UserController@passwordResetRequest']);
        Route::put('/passwordreset/{token}', ['uses' => 'User\UserController@passwordReset']);
    });
});
