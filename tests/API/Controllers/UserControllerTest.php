<?php

use App\User;
use App\UserEmailChange;
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

        $this->put('/api/0.1/user/' . $data['username'], $data)
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
        $this->put('/api/0.1/user/' . $data['username'], $data)
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
             ->put("/api/0.1/user/{$user->username}", $data)
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
             ->put("/api/0.1/user/{$user->username}", $data);

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
             ->put("/api/0.1/user/update/email/{$user->id}", $data);

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

}
