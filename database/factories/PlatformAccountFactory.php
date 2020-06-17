<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\PlatformAccount;
use App\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(\App\PlatformAccount::class, function (Faker $faker) {
    $product              = new Product();
    $product->user_id     = \App\User::query()->first();
    $product->name        = 'test';
    $product->description = 'test';
    $product->status      = 1;
    $product->save();
    return [
        'product_id'   => $product->id,
        'order_status' => 0,
        'platform'     => "测试",
        'dimension'    => "至尊宝" . random_int(1, 1000),
        'dimension2'   => random_int(1, 5),
        'price'        => 1,
        // password
        'account'      => Str::random(10),
        'password'     => Str::random(10),
    ];
});
