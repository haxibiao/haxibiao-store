<?php
namespace Database\Factories;

use Haxibiao\Store\PlatformAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlatformAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlatformAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id'   => 1, //最好传入产品的id
            'order_status' => 0,
            'platform'     => "测试",
            'dimension'    => "至尊宝" . random_int(1, 1000),
            'dimension2'   => random_int(1, 5),
            'price'        => 1,
            // password
            'account'      => Str::random(10),
            'password'     => Str::random(10),
        ];

    }
}
