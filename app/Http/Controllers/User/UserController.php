<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use Validator;
use Mail;
use Redirect;
use App\User;
use App\UserEmailChange;

class UserController extends Controller
{

	public function getSignin(Request $request)
	{
		$credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email'    => 'required',
            'password' => 'required',
        ]);

		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
			$remember = $request->input('remember_me', null);

			if (Auth::attempt($credentials, $remember)) {
				return Auth::user();
			} else {
				return ['Wrong credentials'];
			}
		}
	}

	public function putCreateUpdate($username, Request $request)
	{
		// Creating
		$user = User::where('username', $username)->first();
		if (!$user instanceof User) {
			$validator = Validator::make(
				array_merge($request->all(), ['username' => $username]), [
					'username'   => 'min:2|unique:users',
					'email'      => 'required|email|max:255|unique:users',
					'password'   => 'required|confirmed|min:6',
					'first_name' => 'required|min:3|max:255',
					'last_name'  => 'required|min:3|max:255',
			]);

			if ($validator->fails()) {
				return $validator->errors()->all();
			} else {
				$user = User::create([
					'username'   => $username,
					'email'      => $request->input('email'),
					'password'   => bcrypt($request->input('password')),
					'first_name' => $request->input('first_name'),
					'last_name'  => $request->input('last_name'),
				]);

				// Create User email change
				$emailChange = new UserEmailChange();
				$emailChange->user_id = $user->id;
				$emailChange->email = $user->email;
				$emailChange->token = str_random();
				$emailChange->save();

				// Confirm email address
				Mail::send('emails/user/register', array('emailChange' => $emailChange),
					function($message) use ($user) {
						$message->to($user->email, $user->first_name)
						->subject('Email address confirmation');
					}
				);
			}
			$response = new Response(null, 201);
		} else {
			// Updating
			$validator = Validator::make($request->all(), [
				'first_name' => 'required|min:3|max:255',
				'last_name'  => 'required|min:3|max:255',
			]);

			if ($validator->fails()) {
				return $validator->errors()->all();
			} else {
				$user->first_name = $request->input('first_name');
				$user->last_name  = $request->input('last_name');
				$user->save();

			$response = new Response(null, 200);
			}
		}

		return $response;
	}

	/**
	 * Show user password update form.
	 *
	 * @return Response
	 */
	public function getUpdatePassword()
	{
		return view('user/update_password');
	}

	/**
	 * Validates and Updates user password.
	 *
	 * @return Response
	 */
	public function postUpdatePassword(Request $request)
	{
		// validate
		$validator = Validator::make($request->all(), array(
			'current_password'      => 'required|checkauth',
			'password'              => 'required|confirmed|min:6',
			'password_confirmation' => 'required',
		));

		if ($validator->fails()) {
			return redirect()->route('user::update_password_get')
						   ->withErrors($validator);
		} else {
			// Change password
			$user = Auth::user();
			$user->password = bcrypt($request->input('password'));
			$user->save();

			$this->setFlashMessage('success', 'Password updated.');
		}

		return redirect()->route('home');
	}


	/**
	 * Validates and Updates user email changes.
	 *
	 * @return Response
	 */
	public function putUpdateEmail(Request $request, $username)
	{
		$validator = Validator::make($request->all(), array(
	        'current_password'   => 'required|checkauth',
	        'email'				 => 'required|confirmed|email|unique:users|max:255',
	        'email_confirmation' => 'required',
	    ));

		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
			$user = User::where('username', $username)->first();
			if ($user instanceof User) {
				$emailChange = new UserEmailChange();
				$emailChange->user_id = $user->id;
				$emailChange->email = $request->input('email');
				$emailChange->token = str_random();
				$emailChange->save();

				// Confirm email address
				Mail::send('emails/user/register', array('emailChange' => $emailChange),
					function($message) use ($emailChange, $user) {
						$message->to($emailChange->email, $user->first_name)
								->subject('New email address confirmation');
					}
				);
			} else {
				return new Response(null, 404);
			}
		}

		return new Response(null, 200);
	}

	/**
	 * Confirms email and token then updates user email address status.
	 *
	 * @return Response
	 */
	public function putEmailConfirmation($email, $token)
	{
		$emailChg = UserEmailChange::where('email', '=', $email)
								   ->where('token', '=', $token)
								   ->where('confirmed', '=', false)
								   ->orderBy('created_at', 'desc')
								   ->first();

		if ($emailChg instanceof UserEmailChange) {
			$limitDateTime = new \DateTime();
			$limitDateTime->sub(new \DateInterval('P7D'));

			$user = Auth::check() ? Auth::user() : User::find($emailChg->user_id);
			// If $user->email == $emailChg->email, meants it's the first time.
			// There's no need to check expiration date then
			if ($user->email == $emailChg->email or $emailChg->created_at >= $limitDateTime) {
				// Update user email, if changed since registration
				if ($user->email != $emailChg->email) {
					$user->email = $emailChg->email;
					$user->save();
				}

				// Update email status
				$emailChg->confirmed = true;
				$emailChg->save();

				return new Response(['Your email is verified']);
			} else {
				return new Response(['Your request is no longer valid. Please contact us or submit a new one']);
			}
		} else {
			return new Response(['We could not confirm your email address. Please try again.']);
		}
	}
}
