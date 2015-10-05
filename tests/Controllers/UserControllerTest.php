<?php

use App\User;
use App\UserEmailChange;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test landing page redirects to Login if user is unauthenticated
     *
     * @return void
     */
    public function testLandingRedirectsToLoginIfNotAuthenticated()
    {
        $this->visit(route('home'))
             ->seePageIs(route('signin_get'));
    }

    /**
     * Test landing page redirects to Home if user is authenticated
     *
     * @return void
     */
    public function testLandingRedirectsToHomeIfUserIsAuthenticated()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
             ->visit(route('home'))
             ->seePageIs(route('home'));
    }

    /**
     * Test user registration validations work
     *
     * Obs: Couldn't do it using $this->visit('pagename').
     * Error: "Session missing key: errors", though validation has errors.
     *
     * @return void
     */
    public function testUserRegistrationValidationWorks()
    {
        $this->visit(route('user::signup_get'))
             ->type('jo', 'first_name')
             ->type('', 'last_name')
             ->type('j@com', 'email')
             ->type('12345', 'password')
             ->type('44444', 'password_confirmation')
             ->press('Register')
             ->seePageIs(route('user::signup_get'))
             ->see('The first name must be at least 3 characters.')
             ->see('The last name field is required.')
             ->see('The email must be a valid email address.')
             ->see('The password confirmation does not match.')
             ->see('The password must be at least 6 characters.');

        $this->notSeeInDatabase('users', ['email' => 'j@com']);
    }

    /**
     * Test user registration works
     *
     * @return void
     */
    public function testUserRegistrationWorks()
    {
        $this->visit(route('user::signup_get'))
             ->type('João', 'first_name')
             ->type('Antunes', 'last_name')
             ->type('joantu@email.com', 'email')
             ->type('123456', 'password')
             ->type('123456', 'password_confirmation')
             ->press('Register')
             ->seePageIs(route('signin_get'));

            $this->seeInDatabase('users', ['email' => 'joantu@email.com']);
    }

    /**
     * Test email confirmation
     *
     * @return void
     */
    public function testUserEmailConfirmationWorks()
    {
        $user = factory(User::class)->create();
        $emailChange = factory(UserEmailChange::class)->make([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'confirmed' => false
        ]);
        $user->save();
        $emailChange->save();

        // With invalid token and email
        $getParams = array('email' => $emailChange->email,'token' => 'WrongEmailToken');
        $this->visit(route('user::email_confirmation_get', $getParams))
             ->see('confirm your email address');
        $getParams = array('email' => "someEmail@email.com",'token' => $emailChange->token);
        $this->visit(route('user::email_confirmation_get', $getParams))
             ->see('confirm your email address');

        // With valid token and email
        $getParams = array('email' => $emailChange->email,'token' => $emailChange->token);
        $this->visit(route('user::email_confirmation_get', $getParams))
             ->see('Your email is verified')
             ->seePageIs(route('signin_get'));
        $results = UserEmailChange::where('email', '=', $user->email);
        $this->assertEquals(1, count($results));
        $this->seeInDatabase('users_email_change', [
            'email'     => $user->email,
            'token'     => $emailChange->token,
            'confirmed' => true
        ]);
    }

    /**
     * Test user account update validation
     *
     * @return void
     */
    public function testUserAccountUpdateValidation()
    {
        $user = factory(User::class)->create();
        $user->save();

        $firstName = 'Fi';
        $lastName = '';
        $this->actingAs($user)
             ->visit(route('user::update_get'))
             ->type($firstName, 'first_name')
             ->type($lastName, 'last_name')
             ->press('Update')
             ->seePageIs(route('user::update_get'))
             ->see('The last name field is required.')
             ->see('The first name must be at least 3 characters');

        $this->notSeeInDatabase('users', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $user->email,
        ]);
    }

    /**
     * Test user account update
     *
     * @return void
     */
    public function testUserAccountUpdate()
    {
        $user = factory(User::class)->create();
        $emailChange = factory(UserEmailChange::class)->make([
            'user_id' => $user->id,
            'confirmed' => false
        ]);

        $firstName = 'Firstname';
        $lastName = 'Lastname';
        $this->actingAs($user)
             ->visit(route('user::update_get'))
             ->type($firstName, 'first_name')
             ->type($lastName, 'last_name')
             ->press('Update')
             ->seePageIs(route('home'));

            $this->seeInDatabase('users', [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
            ]);
    }

    /**
     * Test user account password update validation
     *
     * @return void
     */
    public function testUserAccountPwUpdateValidation()
    {
        $user = factory(User::class)->create();

        $curPw = 'SomeWrongPW';
        $newPw = '12345';

        $this->actingAs($user)
             ->visit(route('user::update_password_get'))
             ->type($curPw, 'current_password')
             ->type($newPw, 'password')
             ->type($newPw.'1', 'password_confirmation')
             ->press('Update password')
             ->seePageIs(route('user::update_password_get'))
             ->see('Password is incorrect')
             ->see('The password confirmation does not match')
             ->see('The password must be at least 6 characters.');

        // Check password didn't change.
        $this->seeInDatabase('users', [
            'email' => $user->email,
            'password' => $user->password,
        ]);
    }

    /**
     * Test user account password update
     *
     * @return void
     */
    public function testUserAccountPwUpdate()
    {
        $curPw = "SomePassword";
        $newPw = '123456789abcdef';
        $user = factory(User::class)->make(['password' => bcrypt($curPw)]);

        // Assert old password match
        $this->assertFalse(\Hash::check($newPw, $user->password));

        $this->actingAs($user)
             ->visit(route('user::update_password_get'))
             ->type($curPw, 'current_password')
             ->type($newPw, 'password')
             ->type($newPw, 'password_confirmation')
             ->press('Update password')
             ->see('Password updated.')
             ->seePageIs(route('home'));

        // Assert new password match
        $user = User::where('email', '=', $user->email)->first();
        $this->assertTrue(\Hash::check($newPw, $user->password));
    }

    /**
     * Test user account email update validation
     *
     * @return void
     */
    public function testUserAccountEmailUpdateValidation()
    {
        $user = factory(User::class)->create();
        $user->save();
        $emailChange = factory(UserEmailChange::class)->make([
            'user_id' => $user->id,
            'email' => $user->email,
            'confirmed' => true
        ]);
        $emailChange->save();

        $newEmail = 'wrongemailaddress@com';
        $this->actingAs($user)
             ->visit(route('user::update_email_get'))
             ->type('SomeWrongPassword', 'current_password')
             ->type($newEmail, 'email')
             ->type($newEmail."1", 'email_confirmation')
             ->press('Update email')
             ->see('Password is incorrect')
             ->see('The email must be a valid email address.')
             ->see('The email confirmation does not match.')
             ->seePageIs(route('user::update_email_get'));

        $this->notSeeInDatabase('users_email_change', [
            'user_id' => $user->id,
            'email' => $newEmail
        ]);
    }

    /**
     * Test user account email update validation
     *
     * @return void
     */
    public function testUserAccountEmailUpdate()
    {
        $curPw = 'Sampl3P4ssword';
        $user = factory(User::class)->make(['password' => bcrypt($curPw)]);
        $user->save();
        $emailChg = factory(UserEmailChange::class)->make([
            'user_id'   => $user->id,
            'email'     => $user->email,
            'confirmed' => true
        ]);
        $emailChg->save();

        $newEmail = 'validemail@somemail.com';
        $this->actingAs($user)
             ->visit(route('user::update_email_get'))
             ->type($curPw, 'current_password')
             ->type($newEmail, 'email')
             ->type($newEmail, 'email_confirmation')
             ->press('Update email')
             ->see('Email address updated.')
             ->seePageIs(route('home'));

        $newEmailChg = UserEmailChange::where('email', '=', $newEmail)
                                      ->where('confirmed', '=', false)
                                      ->orderBy('created_at', 'desc')
                                      ->first();
        $this->assertTrue($newEmailChg instanceof UserEmailChange);

        // Test email confirmation expiry date (a week, 7 days)
        $expiredDate = $newEmailChg->created_at;
        $expiredDate->sub(new \DateInterval('P8D'));
        $newEmailChg->created_at = $expiredDate;
        $newEmailChg->save();

        $getParams = ['email' => $newEmailChg->email, 'token' => $newEmailChg->token];
        $this->visit(route('user::email_confirmation_get', $getParams))
             ->seePageIs(route('home'))
             ->see('Your request is no longer valid');

        // Test email confirmation
        $validDate = $newEmailChg->created_at;
        $validDate->add(new \DateInterval('P1D'));
        $newEmailChg->created_at = $validDate;
        $newEmailChg->save();

        $this->visit(route('user::email_confirmation_get', $getParams))
             ->seePageIs(route('home'))
             ->see('Your email is verified');

        $this->seeInDatabase('users_email_change', [
            'id'        => $newEmailChg->id,
            'email'     => $newEmail,
            'confirmed' => true
        ]);
    }

}
