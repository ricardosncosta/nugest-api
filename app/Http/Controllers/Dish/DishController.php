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
	 * List user dishes.
	 *
	 * @return Response
	 */
	public function index()
	{
		return Dish::where('user_id', Auth::user()->id)
				   ->orderBy('created_at', 'desc')
				   ->get();
	}

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

	public function getUpdate(Request $request, $id)
	{
		try {
			$dish = Dish::findOrFail($id);
			return view('dish/update', ['dish' => $dish]);
		} catch (ModelNotFoundException $e) {
			abort(404, 'Item not found.');
		}
	}

	public function postUpdate(Request $request, $id)
	{
        $validator = Validator::make($request->all(), [
            'name'     => 'required|min:3|max:255',
            'calories' => 'digits_between:1,4',
        ]);

		if ($validator->fails()) {
			return redirect()->route('dish::update_get', ['id' => $id])
						     ->withErrors($validator)
						     ->withInput();
		} else {
			$dish = Dish::find($id);
	        $dish->name = $request->input('name');
	    	$dish->calories = $request->input('calories', null);
			$dish->save();

			$this->setFlashMessage('success', 'Dish updated.');
		}

		return redirect()->route('dish::list');
	}

	// Ajax action
	public function getDelete(Request $request, $id)
	{
		try {
			$dish = Dish::findOrFail($id);
			$dish->delete();

			return response()->json(['status' => 'success']);
		} catch (ModelNotFoundException $e) {
			abort(404, 'Item not found.');
		}
	}

}
