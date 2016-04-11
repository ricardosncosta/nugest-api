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
}
