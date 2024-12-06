<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
            [
                'name' => 'DS1',
                'image' => '01J97HA26P84GH28171DX2G7RC.jpeg',
                'price' => '500000',
                'quantity' => '100',
                'time' => '1',
            ],
            [
                'name' => 'DS2',
                'image' => '01J97HAFJPFJJWK06C4BHDY88Q.jpeg',
                'price' => '100000',
                'quantity' => '50',
                'time' => '1',
            ],
            [
                'name' => 'DS3',
                'image' => '01J97HB2NKFK6MXEJGKN6MCAJP.jpeg',
                'price' => '150000',
                'quantity' => '25',
                'time' => '1',
            ],

        ]);
    }
}
