<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Currency;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run():void{
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $this->call(CurrencySeeder::class);
        $currencies = Currency::all();
        Product::factory(10)->recycle($currencies)->create();
    }
}