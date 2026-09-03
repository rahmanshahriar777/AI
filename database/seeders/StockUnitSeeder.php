<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stock_units')->insert([
            ['name' => 'Pcs', 'slug' => 'pcs'],
            ['name' => 'Kg', 'slug' => 'kg'],
            ['name' => 'Ltr', 'slug' => 'ltr'],
            ['name' => 'Box', 'slug' => 'box'],
            ['name' => 'Dozen', 'slug' => 'dozen'],
            ['name' => 'Pack', 'slug' => 'pack'],
            ['name' => 'Bundle', 'slug' => 'bundle'],
            ['name' => 'Roll', 'slug' => 'roll'],
            ['name' => 'Sheet', 'slug' => 'sheet'],
            ['name' => 'Gram', 'slug' => 'gram'],
            ['name' => 'Meter', 'slug' => 'meter'],
            ['name' => 'Yard', 'slug' => 'yard'],
            ['name' => 'Cubic Meter', 'slug' => 'cubic-meter'],
            ['name' => 'Cubic Feet', 'slug' => 'cubic-feet'],
        ]);
    }
}
