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
// Home
Route::get('/', ['as' => 'home',
                 'middleware' => 'auth',
                 'uses' => 'User\UserController@home']);

// Auth routes...

Route::get('/signin', ['as' => 'signin_get','uses' => 'Auth\AuthController@getLogin',]);
Route::post('/signin', ['as' => 'signin_post', 'uses' => 'Auth\AuthController@postLogin',]);
Route::get('/signout', ['as' => 'signout', 'uses' => 'Auth\AuthController@getLogout']);
Route::get('/signup', ['as' => 'user::signup_get', 'uses' => 'User\UserController@getRegister']);
Route::post('/signup', ['as' => 'user::signup_post', 'uses' => 'User\UserController@postRegister']);

Route::get('/user/email/confirm/{email}/{token}', [
    'as'   => 'user::email_confirmation_get',
    'uses' => 'User\UserController@getEmailConfirmation',
]);

Route::group(['prefix' => 'user', 'middleware' => 'auth', 'as' => 'user::'], function () {
    // Password reset link request and reset routes
    Route::get('/password/email', [
        'as'   => 'pw_reset_email_get',
        'uses' => 'Auth\PasswordController@getEmail'
    ]);
    Route::post('/password/email', [
        'as'   => 'pw_reset_email_post',
        'uses' => 'Auth\PasswordController@postEmail'
    ]);

    Route::get('/password/reset/{token}', [
        'as'   => 'pw_reset_get',
        'uses' => 'Auth\PasswordController@getReset'
    ]);
    Route::post('/password/reset', [
        'as'   => 'pw_reset_post',
        'uses' => 'Auth\PasswordController@postReset',
    ]);

    // Update
    Route::get('/update', [
        'as'   => 'update_get',
        'uses' => 'User\UserController@getUpdate',
    ]);
    Route::post('/update', [
        'as'   => 'update_post',
        'uses' => 'User\UserController@postUpdate',
    ]);

    // Email Update
    Route::get('/update/email', [
        'as'   => 'update_email_get',
        'uses' => 'User\UserController@getUpdateEmail',
    ]);
    Route::post('/update/email', [
        'as'   => 'update_email_post',
        'uses' => 'User\UserController@postUpdateEmail',
    ]);

    // Password Update
    Route::get('/update/password', [
        'as'   => 'update_password_get',
        'uses' => 'User\UserController@getUpdatePassword',
    ]);
    Route::post('/update/password', [
        'as'   => 'update_password_post',
        'uses' => 'User\UserController@postUpdatePassword',
    ]);
});

// Dish routes
Route::group(['prefix' => 'user/dishes', 'middleware' => 'auth', 'as' => 'dish::'], function () {
    // List (get) and delete (post)
    Route::get('/', [
        'as'   => 'list',
        'uses' => 'Dish\DishController@getList',
    ]);
    Route::post('/', [
        'as'   => 'list',
        'uses' => 'Dish\DishController@postList',
    ]);

    // Create
    Route::get('/create', [
        'as'   => 'create_get',
        'uses' => 'Dish\DishController@getCreate',
    ]);
    Route::post('/create', [
        'as'   => 'create_post',
        'uses' => 'Dish\DishController@postCreate',
    ]);

    // Update
    Route::get('/update/{id}', [
        'as' => 'update_get', 'uses' => 'Dish\DishController@getUpdate',
    ])->where('id', '[0-9]+');
    Route::post('/update/{id}', [
        'as' => 'update_post', 'uses' => 'Dish\DishController@postUpdate',
    ])->where('id', '[0-9]+');

    // Delete
    Route::get('/delete/{id}', [
        'as' => 'delete_get', 'uses' => 'Dish\DishController@getDelete',
    ])->where('id', '[0-9]+');
});

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
