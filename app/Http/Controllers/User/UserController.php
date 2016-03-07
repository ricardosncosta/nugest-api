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

	public function postRegister(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:3|max:255',
            'last_name'  => 'required|min:3|max:255',
            'email'      => 'required|email|max:255|unique:users',
            'password'   => 'required|confirmed|min:6',
        ]);

		if ($validator->fails()) {
			return $validator->errors()->all();
		} else {
	        $user = User::create([
	            'first_name' => $request->input('first_name'),
	            'last_name'  => $request->input('last_name'),
	            'email'      => $request->input('email'),
	            'password'   => bcrypt($request->input('password')),
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

			$this->setFlashMessage(
				'success', 'Account created! An email has been sent to your inbox.'
			);
		}

		$response = new Response(null, 201);
		return $response;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getUpdate()
	{
		// get user
		return view('user/update', ['user' => Auth::user()]);
	}


	/**
	 * Updates user information
	 *
	 * @return Response
	 */
	public function postUpdate(Request $request)
	{
		// validate
		$validator = Validator::make($request->all(), array(
			'first_name' => 'required|min:3',
			'last_name'  => 'required|min:3',
		));

		// process the login
		if ($validator->fails()) {
			return redirect()->route('user::update_get')->withErrors($validator);
		} else {
			// Update
			$user = User::find(Auth::user()->id);
			$user->first_name = $request->input('first_name');
			$user->last_name  = $request->input('last_name');
			$user->save();

			$this->setFlashMessage('success', 'Account Updated!');
		}

		return redirect()->route('home');
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
	 * Show user email update form.
	 *
	 * @return Response
	 */
	public function getUpdateEmail()
	{
		// get user
		return view('user/update_email', ['user' => Auth::user()]);
	}

	/**
	 * Validates and Updates user email.
	 *
	 * @return Response
	 */
	public function postUpdateEmail(Request $request)
	{
		$validator = Validator::make($request->all(), array(
	        'current_password'   => 'required|checkauth',
	        'email'				 => 'required|confirmed|email|unique:users|max:255',
	        'email_confirmation' => 'required',
	    ));

		if ($validator->fails()) {
			$request->session()->keep(['errors']);
			return redirect()->route('user::update_email_get')->withErrors($validator);
		} else {
			$emailChange = new UserEmailChange();
			$emailChange->user_id = Auth::user()->id;
			$emailChange->email = $request->input('email');
			$emailChange->token = str_random();
			$emailChange->save();

			// Confirm email address
			$user = Auth::user();
			Mail::send('emails/user/register', array('emailChange' => $emailChange),
				function($message) use ($emailChange, $user) {
				    $message->to($emailChange->email, $user->first_name)
							->subject('New email address confirmation');
				}
			);

			$this->setFlashMessage('success', 'Email address updated.');
		}

		return redirect()->route('home');
	}

	/**
	 * Confirms email and token then updates user email address status.
	 *
	 * @return Response
	 */
	public function getEmailConfirmation($email, $token)
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
			// If $user->email == $emailChg->email, it's the first time.
			// No need to check expiry date then
			if ($user->email == $emailChg->email or $emailChg->created_at >= $limitDateTime) {
				// Update user email, if changed since registration
				if ($user->email != $emailChg->email) {
					$user->email = $emailChg->email;
					$user->save();
				}

				// Update email status
				$emailChg->confirmed = true;
				$emailChg->save();

				$this->setFlashMessage('success', 'Your email is verified. You may now login if you didn\'t already.');
			} else {
				$this->setFlashMessage('danger', 'Your request is no longer valid. Please contact us or submit a new one.');
			}
		} else {
			// redirect
			$this->setFlashMessage('danger', 'We could not confirm your email address. Please try again.');
		}

		// Simple hack not to loose flash messages between redirects.
		// Authentication is already a built-in trait so no need to rewrite or
		// create a closure in route definition
		if (Auth::check()) {
			// If logged, get redirected to landing page
			return redirect()->route('home');
		} else {
			// If not, gets redirected to signin page
			return redirect()->route('signin_get');
		}
	}
}
