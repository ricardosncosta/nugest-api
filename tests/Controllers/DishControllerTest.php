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
     * Test dish list
     */
    public function testDishList()
    {
        $user = factory(User::class)->create();
        for ($i=0; $i < 5; $i++) {
            $dish = factory(Dish::class)->create(['user_id' => $user->id]);
            // This process is needed to match json's response
            $dishes[] = [
                'calories'   => (string) $dish->calories,
                'created_at' => $dish->created_at->format('Y-m-d H:i:s'),
                'id'         => (string) $dish->id,
                'name'       => $dish->name,
                'updated_at' => $dish->updated_at->format('Y-m-d H:i:s'),
                'user_id'    => (string) $dish->user_id,
            ];
        }

        $this->actingAs($user)
             ->get("/api/0.1/users/{user->username}/dishes")
             ->seeJsonEquals($dishes)
             ->seeStatusCode(200);
    }

    /**
     * Test dish creation
     */
    public function testDishCreation()
    {
        // Setup needed data
        $user = factory(User::class)->create();

        // Test validation
        $data = ['name' => 'as'];
        $this->actingAs($user)
             ->post("/api/0.1/users/{user->username}/dishes", $data)
             ->seeJsonEquals(['The name must be at least 3 characters.']);

        // Test functionality
        $data = ['name' => 'dish name #1', 'calories' => 100];
        $this->actingAs($user)
             ->post("/api/0.1/users/{user->username}/dishes", $data)
             ->seeJsonContains($data)
             ->seeStatusCode(201);

        $this->seeInDatabase('dishes', [
            'name'     => $data['name'],
            'calories' => $data['calories'],
        ]);
    }

    /**
     * Test dish update
     */
    public function testDishUpdate()
    {
        // Setup needed data
        $user = factory(User::class)->create();
        $dish = factory(Dish::class)->create(['user_id' => $user->id]);

        // not found, 404
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/dishes/20")
             ->seeStatusCode(404);

        // Validation check
        $data = ['name' => 'as'];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/dishes/{$dish->id}", $data)
             ->seeJsonEquals(['The name must be at least 3 characters.']);

        // Functionality check
        $data = ['name' => 'The most beautiful dish name ever', 'calories' => 200];
        $this->actingAs($user)
             ->put("/api/0.1/users/{user->username}/dishes/{$dish->id}", $data)
             ->seeJsonContains($data)
             ->seeStatusCode(200);

        $this->seeInDatabase('dishes', [
            'id'       => $dish->id,
            'name'     => $data['name'],
            'calories' => $data['calories'],
        ]);
    }
}
