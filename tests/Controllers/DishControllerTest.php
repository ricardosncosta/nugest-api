<?php

use App\User;
use App\UserEmailChange;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DishControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test user registration validations work
     *
     * Obs: Couldn't do it using $this->visit('pagename').
     * Error: "Session missing key: errors", though validation has errors.
     *
     * @return void
     */
    public function testDishCreationValidationWorks()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
             ->visit(route('dish::create_get'))
             ->type('ja', 'name')
             ->type('s', 'calories')
             ->press('Create')
             ->seePageIs(route('dish::create_get'))
             ->see('The name must be at least 3 characters.')
             //->see('The calories field is required.')
             ;

        $this->notSeeInDatabase('dishes', ['name' => 'ja']);
    }

    /**
     * Test dish creation works
     *
     * @return void
     */
    public function testUserRegistrationWorks()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
             ->visit(route('dish::create_get'))
             ->type('Carbonara', 'name')
             ->type('250', 'calories')
             ->press('Create')
             ->seePageIs(route('dish::list'));

            $this->seeInDatabase('dishes', [
                'name'    => 'Carbonara',
                'user_id' => $user->id
            ]);
    }

}
