<?php

namespace Database\Seeders;

use App\Models\ProductMaterial;
use Illuminate\Database\Seeder;

class ProductMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductMaterial::insert([
            [
                'order_id' => 1,
                'material_id' => 1,
                'quantity' => 3,
            ],
            [
                'order_id' => 1,
                'material_id' => 2,
                'quantity' => 5,
            ],
            [
                'order_id' => 2,
                'material_id' => 1,
                'quantity' => 2,
            ],
            [
                'order_id' => 2,
                'material_id' => 2,
                'quantity' => 2,
            ],
        ]);
    }
}
