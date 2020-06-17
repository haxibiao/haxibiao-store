<?php

namespace Tests\Feature\GraphQL;

use App\Store;
use App\User;
use Illuminate\Support\Facades\Auth;

class StoreTest extends GraphQLTestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'api_token' => str_random(60),
        ]);
    }

    //测试我的商品订单接口
    public function testStore()
    {
        $query = file_get_contents(__DIR__ . '/Store/Query/store.gql');

        $store = Store::create([
            'user_id' => $this->user->id,
            'status'  => 1,
        ]);
        Auth::loginUsingId($store->user_id);

        $variables = [
            "user_id" => $store->user_id,
        ];

        $this->startGraphQL($query, $variables);
    }
}
