<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test user registration validations
     */
    public function testUserRegistrationValidationWorks()
    {
        $data = [
			'first_name'            => 'jo',
			'last_name'             => '',
			'email'                 => 'j@com',
			'password'              => '12345',
			'password_confirmation' => '44444',
        ];

        $this->post('/api/0.1/signup', $data)
             ->seeJsonEquals([
                'The first name must be at least 3 characters.',
                'The last name field is required.',
                'The email must be a valid email address.',
                'The password confirmation does not match.',
                'The password must be at least 6 characters.'
		    ]);

        $this->notSeeInDatabase('users', ['email' => $data['email']]);
    }

    /**
     * Test User registration works
     */
	public function testUserRegistrationWorks()
	{
        $data = [
			'first_name' 			=> 'John',
		 	'last_name' 			=> 'Doe',
		 	'email'					=> 'joantu@email.co',
		 	'password'              => '123456',
		 	'password_confirmation' => '123456',
		];
        $this->post('/api/0.1/signup', $data)
             ->seeStatusCode(201);

        $this->seeInDatabase('users', ['email' => $data['email']]);
	}

}
