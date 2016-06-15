<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use JWTAuth;
use Validator;
use Mail;
use Redirect;
use App\User;
use App\UserEmailChange;
use App\UserPasswordReset;

class AuthController extends Controller
{

	/**
	 * Signs user into the system
	 * @param  Request $request Request object
	 * @return Response object
	 */
	public function signin(Request $request)
	{
		$credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email'    => 'required',
            'password' => 'required',
        ]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->all()], 422);
		} else {
			if (Auth::attempt($credentials)) {
				$user = Auth::user();
				$token = JWTAuth::fromUser($user);

				return response()->json(['user' => $user, 'token' => $token], 200);
			} else {
				return response()->json(['error' => 'Wrong credentials'], 400);
			}
		}
	}
}
