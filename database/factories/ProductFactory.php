<?php
namespace Database\Factories;

use Haxibiao\Store\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'     => 0,
            'store_id'    => 0,
            'video_id'    => 0,
            'name'        => '测试商品',
            'description' => '测试商品介绍',
            'price'       => 1,
        ];

    }
}
