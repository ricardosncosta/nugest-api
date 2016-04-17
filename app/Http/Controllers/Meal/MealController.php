<?php

namespace App\Http\Controllers\Meal;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\Meal;
use App\Dish;

class MealController extends Controller
{

	/**
	 * List meals.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$results = $request->query('results', 14);
		$page = $request->query('page', 0) * $results;

		return Meal::where('user_id', Auth::user()->id)
				   ->orderBy('datetime', 'DESC')
				   ->take($results)
				   ->skip($page)
				   ->get();
	}

	/**
	 * Create resource
	 *
	 * @param  Request $request Request object
	 * @return Response
	 */
	public function store(Request $request)
	{
        $validator = Validator::make($request->all(), ['dish_id' => 'required']);
		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
	        $meal = Meal::create([
	            'user_id'  => Auth::user()->id,
	            'dish_id'  => $request->input('dish_id'),
	            'datetime' => new \DateTime($request->input('datetime')),
	        ]);

			return new Response($meal, 201);
		}
	}

	/**
	 * Update resource
	 *
	 * @param  Request $request Request object
	 * @param  String  $mealId  meals table id field
	 * @return Response object
	 */
	public function update(Request $request, $username, $mealId)
	{
        $validator = Validator::make($request->all(), ['dish_id' => 'required']);
		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
			// Find meal or throw a 404
			try {
				$meal = Meal::where('id', $mealId)
							->where('user_id', Auth::user()->id)
							->firstOrFail();
			} catch (ModelNotFoundException $e) {
					return new Response(['Error' => 'Meal could not be found.'], 404);
			}

			// Find dish or throw a 404
			try {
				$dish = Dish::where('id', $request->input('dish_id'))
							->where('user_id', Auth::user()->id)
							->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return new Response(['Error' => 'Dish could not be found.'], 404);
			}

	        $meal->dish_id = $dish->id;
	        $meal->datetime = new \DateTime($request->input('datetime'));
			$meal->save();

			return new Response($meal, 200);
		}
	}

	/**
	 * Destroy resource
	 *
	 * @param  Request object $request
	 * @param  string $username table users username field
	 * @param  int $dishId dishes table id field
	 * @return Response object
	 */
	public function destroy(Request $request, $username, $mealId)
	{
		try {
			$meal = Meal::where('id', $mealId)
						->where('user_id', Auth::user()->id)
						->firstOrFail();
			$meal->delete();

			return new Response(null, 410);
		} catch (ModelNotFoundException $e) {
			return new Response('Item not found.', 404);
		}
	}

}
