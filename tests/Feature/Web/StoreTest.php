<?php

namespace Haxibiao\Store\Tests\Feature\Web;

use App\Store;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $store;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user  = User::factory()->create();
        $this->store = Store::factory(['user_id' => $this->user->id])->create();

    }

    public function testStoreIndex()
    {
        $user     = $this->user;
        $response = $this->actingAs($user)->get("/store");
        $response->assertStatus(200);
    }

    public function testStoreShow()
    {
        $id       = $this->store->id;
        $response = $this->get("/store/{$id}");
        $response->assertStatus(200);
    }

    protected function tearDown(): void
    {
        $this->store->forceDelete();
        $this->user->forceDelete();
        parent::tearDown();
    }

}
