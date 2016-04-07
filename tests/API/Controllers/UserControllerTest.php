<?php

use App\User;
use App\UserEmailChange;
use App\UserPasswordReset;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test user creation validations
     */
    public function testUserCreationValidation()
    {
        $data = [
			'username'              => 'j',
			'email'                 => 'j@com',
			'password'              => '12345',
			'password_confirmation' => '44444',
            'first_name'            => 'jo',
            'last_name'             => ''
        ];

        $this->post('/api/0.1/users', $data)
             ->seeJsonEquals([
                'The username must be at least 2 characters.',
                'The email must be a valid email address.',
                'The password confirmation does not match.',
                'The password must be at least 6 characters.',
                'The first name must be at least 3 characters.',
                'The last name field is required.',
		    ]);

        $this->notSeeInDatabase('users', ['username' => $data['username']]);
    }

    /**
     * Test User creation
     */
	public function testUserCreation()
	{
        $data = [
			'username' 		        => 'johndoe',
			'first_name' 			=> 'John',
		 	'last_name' 			=> 'Doe',
		 	'email'					=> 'joantu@email.co',
		 	'password'              => '123456',
		 	'password_confirmation' => '123456',
		];
        $this->post('/api/0.1/users', $data)
             ->seeStatusCode(201);

        $this->seeInDatabase('users', ['username' => $data['username']]);
	}

    /**
     * Test user account update validations
     *
     * @return void
     */
    public function testUserAccountUpdateValidations()
    {
        $user = factory(User::class)->create(['password' => bcrypt('somepassword')]);
        $emailChange = factory(UserEmailChange::class)->create([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'confirmed' => false
        ]);

        // Change user data
        $data = [
			'first_name' => 'Jo',
		 	'last_name'  => 'Do',
		];
        $this->actingAs($user)
             ->put("/api/0.1/users/{$user->username}", $data)
             ->seeJsonEquals([
                'The first name must be at least 3 characters.',
                'The last name must be at least 3 characters.',
             ]);

        $this->notSeeInDatabase('users', [
            'username'   => $user->username,
            'first_name' => $data['first_name'],
            'last_name'  =>  $data['last_name'],
        ]);
    }

    /**
     * Test user account update
     *
     * @return void
     */
    public function testUserAccountUpdate()
    {
        $user = factory(User::class)->create(['password' => bcrypt('somepassword')]);
        $emailChange = factory(UserEmailChange::class)->create([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'confirmed' => false
        ]);

        // Change user data
        $data = [
			'first_name' => 'John',
		 	'last_name'  => 'Doe',
		];
        $this->actingAs($user)
             ->put("/api/0.1/users/{$user->username}", $data);

        $this->seeInDatabase('users', [
            'username'   => $user->username,
            'first_name' => $data['first_name'],
            'last_name'  =>  $data['last_name'],
        ]);
    }

    /**
     * Test email change and confirmation
     *
     * @return void
     */
    public function testUserEmailChangeAndConfirmation()
    {
        $user = factory(User::class)->create(['password' => bcrypt('somepassword')]);
        $emailChange = factory(UserEmailChange::class)->create([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'confirmed' => false
        ]);

        // Wrong token
        $wrongUrl = "/api/0.1/email/confirm/{$emailChange->email}/WrongEmailToken";
        $this->put($wrongUrl)
             ->seeJsonEquals(['We could not confirm your email address. Please try again.']);

        // Wrong email
        $wrongUrl = "/api/0.1/email/confirm/somerandom@email.com/{$emailChange->token}";
        $this->put($wrongUrl)
             ->seeJsonEquals(['We could not confirm your email address. Please try again.']);

        // Test confirmation works
        $rightUrl = "/api/0.1/email/confirm/{$emailChange->email}/{$emailChange->token}";
        $this->put($rightUrl)
             ->seeJsonEquals(['Your email is verified']);

        $this->seeInDatabase('users_email_change', [
            'email'     => $user->email,
            'token'     => $emailChange->token,
            'confirmed' => true
        ]);

        // Change email and test date expiry validation
        $data = [
            'current_password'   => 'somepassword',
            'email'              => 'another@email.com',
            'email_confirmation' => 'another@email.com'
        ];

        $this->actingAs($user)
             ->put("/api/0.1/users/{$user->username}/email/", $data);

        $expiredDate = new \DateTime();
        $expiredDate->sub(new \DateInterval('P8D'));
		$emailChange = UserEmailChange::where('user_id', $user->id)
                                      ->where('confirmed', false)
                                      ->orderBy('created_at', 'desc')
                                      ->first();
        $emailChange->created_at = $expiredDate;
        $emailChange->save();

        // Test confirmation works
        $rightUrl = "/api/0.1/email/confirm/{$emailChange->email}/{$emailChange->token}";
        $this->put($rightUrl)
             ->seeJsonEquals(['Your request is no longer valid. Please contact us or submit a new one']);

        $expiredDate = new \DateTime();
        $expiredDate->sub(new \DateInterval('P1D'));
        $emailChange->created_at = $expiredDate;
        $emailChange->save();

        // Test confirmation works
        $rightUrl = "/api/0.1/email/confirm/{$emailChange->email}/{$emailChange->token}";
        $this->put($rightUrl)
             ->seeJsonEquals(['Your email is verified']);

    }

    /**
     * Test password change
     *
     * @return void
     */
    public function testUserPasswordChange()
    {
        // Create dummy user
        $defaultPassword = 'somepassword';
        $user = factory(User::class)->create(['password' => bcrypt($defaultPassword)]);

        // Change password and test validation
        $data = [
            'current_password'      => 'WrongPassword',
            'password'              => '12345',
            'password_confirmation' => '44444'
        ];

        // Test validation and make sure password hasn't changed
        $this->actingAs($user)
             ->put("/api/0.1/users/{$user->username}/password", $data)
             ->seeJsonEquals([
                 'The password is incorrect.',
                 'The password must be at least 6 characters.',
                 'The password confirmation does not match.',
             ]);

        $user = User::find($user->id);
        $this->assertTrue(Hash::check($defaultPassword, $user->password));

        // Test password update
        $data = [
            'current_password'      => 'somepassword',
            'password'              => '123456789abcdef',
            'password_confirmation' => '123456789abcdef'
        ];
        $this->actingAs($user)
             ->put("/api/0.1/users/{$user->username}/password", $data)
             ->seeStatusCode(200);

        $user = User::find($user->id);
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    /**
     * Test password reset
     *
     * @return void
     */
    public function testUserPasswordReset()
    {
        // Create dummy user
        $defaultPW = 'somepassword';
        $newPW = 'ANewPassword2';
        $user = factory(User::class)->create(['password' => bcrypt($defaultPW)]);

        // Change password, test validation and check no record is found
        $this->post("/api/0.1/users/passwordreset", ['email' => 'wrong@emailaddress.com'])
             ->seeStatusCode(200);
        $this->notSeeInDatabase('users_password_reset', ['email' => $user->email]);

        // Send email and check for records
        $this->post("/api/0.1/users/passwordreset", ['email' => $user->email])
             ->seeStatusCode(200);
        $pwReset = UserPasswordReset::where('email', $user->email)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
        $this->assertTrue($pwReset instanceof UserPasswordReset);

        // Test reset password with wrong token
        $this->put("/api/0.1/users/passwordreset/somewrongtoken")
             ->seeStatusCode(404);

        // Test reset password validations
        $data = [
            'password'              => '12345',
            'password_confirmation' => '11111'
        ];
        $this->put("/api/0.1/users/passwordreset/{$pwReset->token}", $data)
             ->seeStatusCode(200);

        // Test reset password token and expired date
        $expiredDate = new \DateTime();
        $expiredDate->sub(new \DateInterval('P8D'));
        $pwReset->created_at = $expiredDate;
        $pwReset->save();

        $data = [
            'password'              => $newPW,
            'password_confirmation' => $newPW
        ];
        $this->put("/api/0.1/users/passwordreset/{$pwReset->token}", $data)
             ->seeStatusCode(410);

        // Test reset password token using same $data but diff timestamp
        $expiredDate = new \DateTime();
        $expiredDate->sub(new \DateInterval('P1D'));
        $pwReset->created_at = $expiredDate;
        $pwReset->save();

        $this->put("/api/0.1/users/passwordreset/{$pwReset->token}", $data)
             ->seeStatusCode(200);

        $user = User::find($user->id);
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

}
