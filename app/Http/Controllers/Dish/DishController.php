<?php

namespace App\Http\Controllers\Dish;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\Dish;

class DishController extends Controller
{

	/**
	 * List resources.
	 *
	 * @return Response object
	 */
	public function index(Request $request)
	{
		$results = $request->query('results', 15);
		$page = $request->query('page', 0) * $results;

		$dishes = Dish::where('user_id', Auth::user()->id)
				  ->orderBy('created_at', 'desc')
				  ->take($results)
				  ->skip($page)
				  ->get();

		return response()->json($dishes, 200);
	}

	/**
	 * Create resource
	 *
	 * @param  Request $request Request object
	 * @return Response object
	 */
	public function store(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'name'     => 'required|min:3|max:255',
            'calories' => 'digits_between:1,4',
        ]);

		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
	        $dish = Dish::create([
	            'user_id'  => Auth::user()->id,
	            'name'     => $request->input('name'),
	            'calories' => $request->input('calories', null),
	        ]);

			return new Response($dish, 201);
		}
	}

	/**
	 * Update resource
	 *
	 * @param  Request $request Request object
	 * @param  String  $dishId  dishes table id field
	 * @return Response object
	 */
	public function update(Request $request, $username, $dishId)
	{
		// Validate data
        $validator = Validator::make($request->all(), [
            'name'     => 'required|min:3|max:255',
            'calories' => 'digits_between:1,4',
        ]);

		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
			// Get dish or 404
			try {
				$dish = Dish::where('id', $dishId)
							->where('user_id', Auth::user()->id)
							->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return new Response('Item not found.', 404);
			}

	        $dish->name = $request->input('name');
	    	$dish->calories = $request->input('calories', null);
			$dish->save();

			return new Response($dish, 200);
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
	public function destroy(Request $request, $username, $dishId)
	{
		try {
			$dish = Dish::where('id', $dishId)
						->where('user_id', Auth::user()->id)
						->firstOrFail();
			$dish->delete();
			return new Response(null, 410);
		} catch (ModelNotFoundException $e) {
			return new Response('Item not found.', 404);
		}
	}

}
