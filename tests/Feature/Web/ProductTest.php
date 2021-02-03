<?php

namespace Haxibiao\Store\Tests\Feature\Web;

use App\Product;
use App\Store;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $store;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user    = User::factory()->create();
        $this->store   = Store::factory(['user_id' => $this->user->id])->create();
        $this->product = Product::factory([
            'user_id'  => $this->user->id,
            'store_id' => $this->store->id,
        ])->create();
    }

    public function testStoreIndex()
    {
        $user     = $this->user;
        $response = $this->actingAs($user)->get("/product");
        $response->assertStatus(200);
    }

    public function testStoreShow()
    {
        $id       = $this->product->id;
        $response = $this->get("/product/{$id}");
        $response->assertStatus(200);
    }

    protected function tearDown(): void
    {
        $this->product->forceDelete();
        $this->user->forceDelete();
        parent::tearDown();
    }

}
