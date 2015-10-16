<?php

use App\User;
use App\Dish;
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
    public function testDishCreationValidation()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
             ->visit(route('dish::create_get'))
             ->type('ja', 'name')
             ->type('s', 'calories')
             ->press('Create')
             ->seePageIs(route('dish::create_get'))
             ->see('The name must be at least 3 characters.')
             ->see('The calories must be between 1 and 4 digits.')
             ;

        $this->notSeeInDatabase('dishes', ['name' => 'ja']);
    }

    /**
     * Test dish creation works
     *
     * @return void
     */
    public function testDishCreation()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
             ->visit(route('dish::create_get'))
             ->type('Carbonara', 'name')
             ->type('250', 'calories')
             ->press('Create')
             ->seePageIs(route('dish::list'))
             ->see('Dish created.');

            $this->seeInDatabase('dishes', [
                'name'    => 'Carbonara',
                'user_id' => $user->id
            ]);
    }

    /**
     * Test dish update throws 404 when not found
     *
     * Obs: Couldn't do it using $this->visit('pagename').
     * Error: "Session missing key: errors", though validation has errors.
     *
     * @return void
     */
    public function testDishUpdateThrows404WhenNotFound()
    {
        $id = 0;
        $user = factory(User::class)->create();

        $this->actingAs($user)
             ->call('GET', route('dish::update_get', ['id' => $id]));

        $this->assertResponseStatus(404);
        $this->notSeeInDatabase('dishes', ['id' => $id]);
    }

    /**
     * Test dish update validation work
     *
     * Obs: Couldn't do it using $this->visit('pagename').
     * Error: "Session missing key: errors", though validation has errors.
     *
     * @return void
     */
    public function testDishUpdateValidation()
    {
        $user = factory(User::class)->create();
        $user->save();

        $dish = factory(Dish::class)->make(['user_id' => $user->id]);
        $dish->save();

        $this->actingAs($user)
             ->visit(route('dish::update_get', ['id' => $dish->id]))
             ->type('ja', 'name')
             ->type('s', 'calories')
             ->press('Update')
             ->seePageIs(route('dish::update_get', ['id' => $dish->id]))
             ->see('The name must be at least 3 characters.');

        $this->notSeeInDatabase('dishes', ['name' => 'ja']);
    }

    /**
     * Test dish update works
     *
     * @return void
     */
    public function testDishUpdate()
    {
        $user = factory(User::class)->create();
        $user->save();

        $dish = factory(Dish::class)->make(['user_id' => $user->id]);
        $dish->save();

        $this->actingAs($user)
             ->visit(route('dish::update_get', ['id' => $dish->id]))
             ->type('New name', 'name')
             ->type('300', 'calories')
             ->press('Update')
             ->seePageIs(route('dish::list'))
             ->see('Dish updated.');

        $this->seeInDatabase('dishes', [
            'id'       => $dish->id,
            'user_id'  => $user->id,
            'name'     => 'New name',
            'calories' => '300'
        ]);
    }

    /**
     * Test dish removal throws 404 when doesn't exist
     *
     * @return void
     */
    public function testDishDeletionThrows404WhenNotFound()
    {
        $id = 0;
        $user = factory(User::class)->create();
        $this->actingAs($user)
             ->call('GET', '/user/dishes/delete/1');

        $this->assertResponseStatus(404);
        $this->notSeeInDatabase('dishes', ['id' => $id]);
    }

    /**
     * Test dish removal
     *
     * @return void
     */
    public function testDishDeletion()
    {
        $user = factory(User::class)->create();
        $user->save();
        $dish = factory(Dish::class)->make(['user_id' => $user->id]);
        $dish->save();
        $this->seeInDatabase('dishes', ['id' => $dish->id]);

        $this->actingAs($user)
             ->call('GET', route('dish::delete_get', ['id' => $dish->id]));
        $this->seeJson(['status' => 'success']);

        $this->notSeeInDatabase('dishes', ['id' => $dish->id]);
    }

}
