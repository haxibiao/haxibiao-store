<?php
namespace Database\Factories;

use Haxibiao\Store\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id'     => 0,
            'name'        => '测试商店',
            'description' => 0,
            'status'      => 1,
        ];
    }
}
