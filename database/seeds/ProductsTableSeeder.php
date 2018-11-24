<?php

use Faker\Generator;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Generator $faker
     * @param Product   $product
     *
     * @return void
     */
    public function run(Generator $faker, Product $product)
    {
        $products = [];

        for ($i = 0; $i < 100; $i++) {
            $products[] = [
                'name' => $faker->realText(100),
                'description' => $faker->text(700),
                'quantity' => $faker->numberBetween(0, 100),
                'price' => $faker->randomFloat(2, 50, 200),
                'discount' => $faker->randomFloat(2, 0, 20),
                'discount_type' => array_random(['FIXED', 'PERCENT']),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            ];
        }

        $product->insert($products);
    }
}
