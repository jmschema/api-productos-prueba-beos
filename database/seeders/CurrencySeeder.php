<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run():void{
        $currencies = [
            ['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1],
            ['name' => 'CLP', 'symbol' => '$', 'exchange_rate' => 970],
            ['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.90],
            ['name' => 'MXN', 'symbol' => '$', 'exchange_rate' => 18]
        ];

        foreach($currencies as $currency){
            Currency::updateOrCreate(['name'=>$currency['name']], $currency);
        }
    }
}