<?php

namespace Tests\Feature\GraphQL;

use App\PlatformAccount;
use App\User;
use Illuminate\Support\Facades\Auth;

class OrderTest extends GraphQLTestCase
{
    protected $user;
    protected $gold;
    protected $platformAccount;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'api_token' => str_random(60),
        ]);
        // $this->gold = factory(Gold::class)->create([
        //     'user_id' => $this->user->id,
        //     'wallet_id' => $this->user->wallet_id,
        // ]);
        $this->platformAccount = factory(PlatformAccount::class)->create();
    }

    //测试下单接口
    public function testMakeOrder()
    {
        $query = file_get_contents(__DIR__ . '/Order/Mutation/makeOrder.gql');

        $platformAccount = $this->platformAccount;
        $user            = User::find($this->user->id);
        //查询信息准备
        $token = $user->api_token;
        \App\Gold::makeIncome($user, 1000, 'test');
        //请求头
        $headers = $this->getHeader($token);

        Auth::loginUsingId($user->id);
        $variables = [
            'product_id' => $platformAccount->product_id,
            'dimension'  => $platformAccount->dimension,
            'dimension2' => $platformAccount->dimension2,
        ];

        $this->startGraphQL($query, $variables, $headers);
    }

    //测试我的商品订单接口
    public function testMyOrders()
    {
        $query = file_get_contents(__DIR__ . '/Order/Query/myOrders.gql');

        Auth::loginUsingId($this->user->id);
        $variables = [
        ];
        $this->startGraphQL($query, $variables);
    }

    /**
     * 设置请求头
     *
     * @param $token
     * @return array
     */
    public function getHeader($token): array
    {
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
        return $headers;
    }

    protected function tearDown(): void
    {
        $this->user->forceDelete();
        $this->platformAccount->forceDelete();
        parent::tearDown();
    }
}
