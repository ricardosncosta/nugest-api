<?php

namespace App\Http\Controllers\Meal;

use Illuminate\Http\Request;
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
	 * Shows meal creation page..
	 *
	 * @return Response
	 */
	public function getCreate()
	{
		$dishes = Dish::where('user_id', Auth::user()->id)->get();
		return view('meal/create', ['dishes' => $dishes]);
	}

	/**
	 * Create page data processor
	 *
	 * @return Response
	 */
	public function postCreate(Request $request)
	{
        $validator = Validator::make($request->all(), ['dish' => 'required',
													   'datetime' => 'required']);
		if ($validator->fails()) {
			return redirect()->route('meal::create_get')
						     ->withErrors($validator)
						     ->withInput();
		} else {
	        $meal = Meal::create([
	            'user_id'  => Auth::user()->id,
	            'dish_id'  => $request->input('dish'),
	            'datetime' => new \DateTime($request->input('datetime')),
	        ]);

			$this->setFlashMessage('success', 'Meal created.');
		}

		return redirect()->route('meal::list_get');
	}

	/**
	 * User meal update page.
	 *
	 * @return Response
	 */
	public function getUpdate(Request $request, $id)
	{
		try {
			return view('meal/update', [
				'meal'   => Meal::findOrFail($id),
				'dishes' => Dish::where('user_id', Auth::user()->id)->get()
			]);
		} catch (ModelNotFoundException $e) {
			abort(404, 'Item not found.');
		}
	}
	/**
	 * User meal update page data processor..
	 *
	 * @return Response
	 */
	public function postUpdate(Request $request, $id)
	{
        $validator = Validator::make($request->all(), ['dish' => 'required']);
		if ($validator->fails()) {
			return redirect()->route('meal::update_get', ['id' => $id])
						     ->withErrors($validator);
		} else {
			$meal = Meal::find($id);
	        $meal->dish_id = $request->input('dish');
	        $meal->datetime = new \DateTime($request->input('datetime'));
			$meal->save();

			$this->setFlashMessage('success', 'Meal updated.');
		}

		return redirect()->route('meal::list_get');
	}

	/**
	 * User meal removal ajax processor
	 *
	 * @return Json response
	 */
	public function getDelete(Request $request, $id)
	{
		try {
			$meal = Meal::findOrFail($id);
			$meal->delete();

			return response()->json(['status' => 'success']);
		} catch (ModelNotFoundException $e) {
			abort(404, 'Item not found.');
		}
	}

}
