<?php

namespace Tests\Feature\GraphQL;

use App\Category;
use App\PlatformAccount;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Auth;

class ProductTest extends GraphQLTestCase
{
    protected $user;

    //平台游戏账户
    protected $platformAccount;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'api_token' => str_random(60),
        ]);

        $this->platformAccount = factory(PlatformAccount::class)->create();
    }

    //测试根据type查询分类列表接口
    public function testCategoryType()
    {
        $query = file_get_contents(__DIR__ . '/Product/Query/categoryType.gql');

        $variables = [
            'type' => "product",
        ];

        $this->startGraphQL($query, $variables);
    }

    //测试根据分类id查询商品列表接口
    public function testProducts()
    {
        $query = file_get_contents(__DIR__ . '/Product/Query/products.gql');

        $category          = new Category();
        $category->user_id = $this->user->id;
        $category->name    = 'test';
        $category->type    = 'product';
        $category->save();

        $category = Category::where("type", "product")->first();
        Auth::loginUsingId($this->user->id);
        $variables = [
            'category_id' => $category->id,
        ];

        $this->startGraphQL($query, $variables);
    }

    //测试根据商品id查询商品规格接口
    public function testDimension()
    {
        $query = file_get_contents(__DIR__ . '/Product/Query/dimension.gql');

        $product   = Product::inRandomOrder()->first();
        $variables = [
            'product_id' => $product->id,
            'type'       => \random_int(1, 2) / 2 == 0 ? "DIMENSION" : "DIMENSION2",
        ];

        $this->startGraphQL($query, $variables);
    }

    //测试根据商品规格查询该规格的数量和价格接口
    public function testPlatformAccountPrice()
    {
        $query = file_get_contents(__DIR__ . '/Product/Query/platformAccountPrice.gql');

        $platformAccount = $this->platformAccount;
        $variables       = [
            'product_id' => $platformAccount->product_id,
            'dimension'  => $platformAccount->dimension,
            'dimension2' => (int) $platformAccount->dimension2,
        ];

        $this->startGraphQL($query, $variables);
    }

    protected function tearDown(): void
    {
        $this->user->forceDelete();
        $this->platformAccount->forceDelete();
        parent::tearDown();
    }

}
