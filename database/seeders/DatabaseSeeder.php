<?php

namespace Database\Seeders;

use App\Models\ProductMaterial;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();



        $this->call([
            MaterialSeeder::class,
            ShieldSeeder::class,
            ProductSeeder::class,
//            ProductMaterialSeeder::class,
        ]);

        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => 'qwe',
        ]);

        $user->assignRole('super_admin');

    }
}
