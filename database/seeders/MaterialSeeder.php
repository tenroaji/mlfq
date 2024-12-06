<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Material::insert([
            [
                'name' => 'Benang',
                'quantity' => '100',
                'type' => 'Roll',
            ],
            [
                'name' => 'Kain',
                'quantity' => '100',
                'type' => 'Meter',
            ],
            [
                'name' => 'Resleting',
                'quantity' => '100',
                'type' => 'piece',
            ],
        ]);

    }
}
