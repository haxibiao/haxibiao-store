<?php
namespace Database\Seeders;

use Haxibiao\Store\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //先清理掉旧数据
        Item::truncate();
        Item::create([
            'name'        => "200金币抵用券",
            'description' => '下单时，可抵用200金币',
            'value'       => '200',
            'price'       => 0,
            'status'      => 1,
        ]);
    }
}
