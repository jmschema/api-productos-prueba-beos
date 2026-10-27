<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition():array{
        return [
            'name'=>ucfirst(fake()->words(3,true)),
            'description'=>fake()->sentence(12),
            'price'=>fake()->randomFloat(2,10,2000),
            'currency_id'=>Currency::factory(),
            'tax_cost'=>fake()->randomFloat(2,1,300),
            'manufacturing_cost'=>fake()->randomFloat(2,5,1000)
        ];
    }
}