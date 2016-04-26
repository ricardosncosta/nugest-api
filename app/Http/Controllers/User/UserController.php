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
use App\UserPasswordReset;

class UserController extends Controller
{

	/**
	 * Creates user account
	 * @param  Request $request Request object
	 * @return Response object
	 */
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username'   => 'min:2|unique:users',
			'email'      => 'required|email|max:255|unique:users',
			'password'   => 'required|confirmed|min:6',
			'first_name' => 'required|min:3|max:255',
			'last_name'  => 'required|min:3|max:255',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->all()], 422);
		} else {
			$user = User::create([
				'username'   => $request->input('username'),
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
		return response()->json(['success' => 'Account created'], 201);
	}

	/**
	 * Updated user account data
	 * @param  string $username
	 * @param  Request $request Request object
	 * @return Response object
	 */
	public function update($username, Request $request)
	{
		// Updating
		$validator = Validator::make($request->all(), [
			'first_name' => 'required|min:3|max:255',
			'last_name'  => 'required|min:3|max:255',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->all()], 422);
		} else {
			$user = Auth::user();
			$user->first_name = $request->input('first_name');
			$user->last_name  = $request->input('last_name');
			$user->save();
		}

		return response()->json($user, 200);
	}

	/**
	 * Validates and Updates user password.
	 *
	 * @return Response
	 */
	public function updatePassword(Request $request)
	{
		// validate
		$validator = Validator::make($request->all(), array(
			'current_password'      => 'required|checkauth',
			'password'              => 'required|confirmed|min:6',
			'password_confirmation' => 'required',
		));

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->all()], 422);
		} else {
			// Change password
			$user = Auth::user();
			$user->password = bcrypt($request->input('password'));
			$user->save();
		}

		return response()->json(['success' => 'Password updated.'], 200);
	}

	/**
	 * Validates and Updates user email changes.
	 *
	 * @return Response
	 */
	public function updateEmail(Request $request, $username)
	{
		$validator = Validator::make($request->all(), array(
	        'current_password'   => 'required|checkauth',
	        'email'				 => 'required|confirmed|email|unique:users|max:255',
	        'email_confirmation' => 'required',
	    ));

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->all()], 422);
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

				return response()->json(['success' => 'Email updated.'], 200);
			} else {
				return response()->json(['error' => 'User not found.'], 404);
			}
		}
	}

	/**
	 * Confirms email and token then updates user email address status.
	 *
	 * @return Response
	 */
	public function emailConfirmation($email, $token)
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

				return response()->json(['success' => 'Email verified.']);
			} else {
				return response()->json(['error' => 'Request no longer valid. Please contact us or submit a new one.'], 410);
			}
		} else {
			return response()->json(['error' => 'We could not confirm your email address. Please try again.'], 400);
		}
	}

	/**
	 * Create user password reset request.
	 *
	 * @return Response
	 */
	public function passwordResetRequest(Request $request)
	{
		$email = $request->input('email', null);
		if ($email != null) {
			try {
				$user = User::where('email', $request->input('email'))->first();
			} catch (Exception $e) {
				$user = null;
			}
		}

		if ($user instanceof User) {
			$pwReset = new UserPasswordReset();
			$pwReset->email = $user->email;
			$pwReset->token = bin2hex(openssl_random_pseudo_bytes(16));
			$pwReset->save();

			// Send reset link
			Mail::send('emails/user/passwordreset', array('token' => $pwReset->token),
				function($message) use ($user) {
					$message->to($user->email, $user->first_name)
					->subject('Password reset request');
				}
			);
		}

		return response()->json(['success' => 'Requested password reset.'], 200);
	}

	/**
	 * Resets user password.
	 *
	 * @return Response
	 */
	public function passwordReset(Request $request, $token)
	{
		$pwReset = UserPasswordReset::where('token', $token)
									->orderBy('created_at', 'desc')
									->first();

		if ($pwReset instanceof UserPasswordReset) {
			// validate
			$validator = Validator::make($request->all(), array(
				'password'              => 'required|confirmed|min:6',
				'password_confirmation' => 'required',
			));

			if ($validator->fails()) {
				return response()->json(['error' => $validator->errors()->all()], 422);
			} else {
				$limitDateTime = new \DateTime();
				$limitDateTime->sub(new \DateInterval('P7D'));

				if ($pwReset->created_at >= $limitDateTime) {
					// Update user password
					$user = User::where('email', $pwReset->email)->first();
					$user->password = bcrypt($request->input('password'));
					$user->save();

					return response()->json(['success' => 'Password reset'], 200);
				} else {
					return response()->json(['error' => 'Your request is no longer valid. Please contact us or submit a new one.'], 410);
				}
			}

		} else {
			return response()->json(['error' => 'We could not confirm your request. Please try again.'], 400);
		}
	}

	/**
	 * Resets user password.
	 *
	 * @return Response
	 */
	public function destroy(Request $request)
	{
		$validator = Validator::make($request->all(), array(
	        'current_password'   => 'required|checkauth',
	        'confirm'			 => 'required|boolean:true',
	    ));

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()->all()], 422);
		} else {
			Auth::user()->delete();
			return new Response(null, 410);
		}
	}
}
