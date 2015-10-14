<?php

namespace App\Http\Controllers\Dish;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
	public function getList()
	{
		$dishes = Dish::where('user_id', '=', Auth::user()->id)->get();
		return view('dish/list', ['dishes' => $dishes]);
	}

	public function getCreate()
	{
		// get user
		return view('dish/create');
	}

	public function postCreate(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'name'     => 'required|min:3|max:255',
            'calories' => 'digits_between:0,5000',
        ]);

		if ($validator->fails()) {
			return redirect()->route('dish::create_get')
						     ->withErrors($validator)
						     ->withInput();
		} else {
	        $dish = Dish::create([
	            'user_id'  => Auth::user()->id,
	            'name'     => $request->input('name'),
	            'calories' => $request->input('calories', null),
	        ]);

			$this->setFlashMessage(
				'success', 'Dish created!'
			);
		}

		return redirect()->route('dish::list');
	}

}
