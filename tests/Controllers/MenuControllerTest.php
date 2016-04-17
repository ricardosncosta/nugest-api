<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use App\Dish;
use App\Menu;

class MenuControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test resource list
     */
    public function testMenusList()
    {
        // Getting data from seeders
        Artisan::call('migrate');
        Artisan::call('db:seed', array('--class' => 'DatabaseSeeder'));

        $user = User::find(1);
        $this->actingAs($user)
             ->get("/api/0.1/users/{user->username}/menus")
             ->seeJson()
             ->seeStatusCode(200);
    }

    /**
     * Test menu creation
     */
    public function testMenuCreation()
    {
        // Setup needed data
        $user = factory(User::class)->create();
        $dish = factory(Dish::class)->create(['user_id' => $user->id]);

        // Test validation
        $data = ['dish' => ''];
        $this->actingAs($user)
             ->post("/api/0.1/users/{user->username}/menus", $data)
             ->seeJsonEquals(['The dish id field is required.']);

        // Test functionality
        $datetime = new \DateTime();
        $data = ['dish_id' => $dish->id, 'datetime' => $datetime->format('Y-m-d H:i:s')];
        $this->actingAs($user)
             ->post("/api/0.1/users/{user->username}/menus", $data)
             ->seeJsonContains($data)
             ->seeStatusCode(201);

        $this->seeInDatabase('menus', $data);
    }

    /**
     * Test menu update
     */
    public function testMenuUpdate()
    {
        // Setup needed data
        $user = factory(User::class)->create();
        $dishes = factory(Dish::class, 2)->create(['user_id' => $user->id]);
        $menu = factory(Menu::class)->create([
            'user_id' => $user->id,
            'dish_id' => $dishes[0]->id
        ]);

        // Validation check
        $data = ['dish_id' => ''];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/menus/{$menu->id}", $data)
             ->seeJsonEquals(['The dish id field is required.']);

        // not found, 404
        $data = ['dish_id' => 5];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/menus/20", $data)
             ->seeJsonEquals(['Error' => 'Menu could not be found.'])
             ->seeStatusCode(404);

        // Dish not found check
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/menus/{$menu->id}", $data)
             ->seeJsonEquals(['Error' => 'Dish could not be found.'])
             ->seeStatusCode(404);

        // Functionality check
        // Obs: Used "(string) $dishes[1]->id" to match returning json data
        $dateTime = new \DateTime();
        $data = [
            'dish_id'  => (string) $dishes[1]->id,
            'datetime' => $dateTime->format('Y-m-d H:i:s')
        ];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/menus/{$menu->id}", $data)
             ->seeJsonContains($data)
             ->seeStatusCode(200);

        $this->seeInDatabase('menus', [
            'id'       => $menu->id,
            'user_id'  => $user->id,
            'dish_id'  => $data['dish_id'],
            'datetime' => $data['datetime']
        ]);
    }

    /**
     * Test menu removal
     */
    public function testMenuRemoval()
    {
        // Setup needed data
        $user = factory(User::class)->create();
        $dish = factory(Dish::class)->create(['user_id' => $user->id]);
        $menu = factory(Menu::class)->create([
            'user_id' => $user->id,
            'dish_id' => $dish->id
        ]);

        // not found, throw 404
        $this->actingAs($user)
             ->delete("/api/0.1/users/{user->username}/menus/20")
             ->seeStatusCode(404);

        // Functionality check
        $this->actingAs($user)
             ->delete("/api/0.1/users/{user->username}/menus/{$menu->id}")
             ->seeStatusCode(410);

        $this->notSeeInDatabase('menus', ['id' => $menu->id]);
    }

}
