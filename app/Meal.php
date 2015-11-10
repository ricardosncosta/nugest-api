<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'meals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id' ,'dish_id'];

    /**
     * Get associated dish object
     */
     public function dish()
     {
         return $this->belongsTo('App\Dish');
     }
}
