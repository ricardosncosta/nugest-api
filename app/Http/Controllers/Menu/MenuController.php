<?php

namespace App\Http\Controllers\Menu;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use App\Menu;
use App\Dish;

class MenuController extends Controller
{

	/**
	 * List menus.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$results = $request->query('results', 14);
		$page = $request->query('page', 0) * $results;

		return Menu::where('user_id', Auth::user()->id)
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
	        $menu = Menu::create([
	            'user_id'  => Auth::user()->id,
	            'dish_id'  => $request->input('dish_id'),
	            'datetime' => new \DateTime($request->input('datetime')),
	        ]);

			return new Response($menu, 201);
		}
	}

	/**
	 * Update resource
	 *
	 * @param  Request $request Request object
	 * @param  String  $menuId  menus table id field
	 * @return Response object
	 */
	public function update(Request $request, $username, $menuId)
	{
        $validator = Validator::make($request->all(), ['dish_id' => 'required']);
		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
			// Find menu or throw a 404
			try {
				$menu = Menu::where('id', $menuId)
							->where('user_id', Auth::user()->id)
							->firstOrFail();
			} catch (ModelNotFoundException $e) {
					return new Response(['Error' => 'Menu could not be found.'], 404);
			}

			// Find dish or throw a 404
			try {
				$dish = Dish::where('id', $request->input('dish_id'))
							->where('user_id', Auth::user()->id)
							->firstOrFail();
			} catch (ModelNotFoundException $e) {
				return new Response(['Error' => 'Dish could not be found.'], 404);
			}

	        $menu->dish_id = $dish->id;
	        $menu->datetime = new \DateTime($request->input('datetime'));
			$menu->save();

			return new Response($menu, 200);
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
	public function destroy(Request $request, $username, $menuId)
	{
		try {
			$menu = Menu::where('id', $menuId)
						->where('user_id', Auth::user()->id)
						->firstOrFail();
			$menu->delete();

			return new Response(null, 410);
		} catch (ModelNotFoundException $e) {
			return new Response('Item not found.', 404);
		}
	}

}
