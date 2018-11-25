<?php

use Carbon\Carbon;
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

        for ($i = 0; $i < 15; $i++) {
            $products[] = [
                'name' => $faker->realText(100),
                'description' => $faker->text(700),
                'quantity' => $faker->numberBetween(0, 100),
                'price' => $faker->randomFloat(2, 50, 200),
                'discount' => $faker->randomFloat(2, 0, 20),
                'discount_type' => array_random([Product::DISCOUNT_FIXED, Product::DISCOUNT_PERCENT]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        $product->insert($products);
    }
}
