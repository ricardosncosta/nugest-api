<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserEmailChange extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users_email_change';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id' ,'email', 'token', 'created_at'];
}
