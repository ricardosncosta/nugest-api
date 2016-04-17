<?php

use App\User;
use App\Dish;
use App\Menu;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MenuTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();
        $this->setupDb();
    }

    /**
     * Sets up DB data to test
     */
    public function setupDB()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed', array('--class' => 'UsersTableSeeder'));
        Artisan::call('db:seed', array('--class' => 'DishesTableSeeder'));
        Artisan::call('db:seed', array('--class' => 'MenusTableSeeder'));
    }

    /**
     * Test getLastMenus method
     *
     * @return void
     */
    public function testGetLastMenusMethod()
    {
        $user = App\User::find(1);

        $menuModel = new Menu();
        $this->assertCount(60, $menuModel->getLastMenus($user));
    }

    /**
     * Test getRecommendation method
     *
     * @return void
     */
    public function testGetRecommendationMethod()
    {
        $user = User::find(1);

        $menuModel = new Menu();
        $recommendedDish = $menuModel->getRecommendation($user);
        $this->assertEquals(4, $recommendedDish->id);
    }

    /**
     * Test getRecommendation method returns null if no results is found
     *
     * @return void
     */
    public function testGetRecommendationReturnsNullIfNoResulFound()
    {
        // Create a new user, without any related dishes
        $user = factory(User::class)->create();

        $menuModel = new Menu();
        $recommendedDish = $menuModel->getRecommendation($user);
        $this->assertTrue($recommendedDish == null);
    }

    /**
     * Test getRecommendation() method signature throws error exception
     *
     * @return void
     */
    public function testGetRecommendationMethodSignatureThrowsErrorExceptionWithNonIntValue()
    {
        $menuModel = new Menu();

        $this->setExpectedException('ErrorException');
        $recommendedDish = $menuModel->getRecommendation($user, '12');
    }
}
