<?php
namespace App\Services;

use App\Models\Currency;
use InvalidArgumentException;

class CurrencyConverter{
    public function convert(string|float $amount, Currency $from, Currency $to):string{
        if((float)$from->exchange_rate<=0){
            throw new InvalidArgumentException('Tasa de cambio inválida');
        }
        $converted=(float)$amount/(float)$from->exchange_rate*(float)$to->exchange_rate;
        return number_format($converted,2,'.','');
    }
}