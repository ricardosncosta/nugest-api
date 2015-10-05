<?php

namespace App\Providers;

use Validator;
use Auth;
use Hash;
use Illuminate\Support\ServiceProvider;

class PasswordValidationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('checkauth', function($attribute, $value, $parameters) {
            return Hash::check($value, Auth::user()->password);
		});

        Validator::replacer('checkauth', function($message, $attribute, $rule, $parameters) {
            return str_replace(
                'validation.checkauth', 'Password is incorrect.', $message
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
