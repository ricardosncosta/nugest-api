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
	public function signIn(Request $request)
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

	/**
	 * Restore user session
	 * @param  Request $request Request object
	 * @return Response object
	 */
	public function restore(Request $request)
	{
		$token = $request->get('token', null);

	    try {
	        if (!$user = JWTAuth::parseToken($request->get('token'))->authenticate()) {
	            return response()->json(['error' => 'User not found'], 404);
	        }
	    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
	        return response()->json(['error' => 'Token Expired'], $e->getStatusCode());
	    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
	        return response()->json(['error' => 'Token Invalid'], $e->getStatusCode());
	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
	        return response()->json(['error' => 'Token Absent'], $e->getStatusCode());
	    }

	    // the token is valid and we have found the user via the sub claim
		return response()->json(['user' => $user, 'token' => $token], 200);
	}
}
