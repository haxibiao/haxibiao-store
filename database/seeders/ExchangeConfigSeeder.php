<?php

namespace Database\Seeders;

use Haxibiao\Store\ExchangeConfig;
use Illuminate\Database\Seeder;

class ExchangeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //先清理掉旧数据
        ExchangeConfig::truncate();

        $config = ExchangeConfig::Create([
            'num'    => 1,
            'name'   => '充值1元',
            'value'  => '700',
            'status' => 1,
        ]);
        $config->save();

        $config = ExchangeConfig::Create([
            'num'    => 2,
            'name'   => '充值2元',
            'value'  => '1500',
            'status' => 1,
        ]);
        $config->save();

        $config = ExchangeConfig::Create([
            'num'    => 5,
            'name'   => '充值5元',
            'value'  => '4000',
            'status' => 1,
        ]);

        $config->save();
        $config = ExchangeConfig::Create([
            'num'    => 10,
            'name'   => '充值10元',
            'value'  => '9000',
            'status' => 1,
        ]);
        $config->save();

        $config = ExchangeConfig::Create([
            'num'    => 20,
            'name'   => '充值20元',
            'value'  => '20000',
            'status' => 1,
        ]);
        $config->save();
    }
}
