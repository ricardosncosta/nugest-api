<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPasswordReset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_password_reset';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'token'];
}
