<?php

namespace Tests\Unit;

use App\Models\Currency;
use App\Services\CurrencyConverter;
use InvalidArgumentException;
use Tests\TestCase;

class CurrencyConverterTest extends TestCase{
    private CurrencyConverter $converter;

    protected function setUp():void{
        parent::setUp();
        $this->converter = new CurrencyConverter();
    }

    public function test_it_converts_from_base_currency():void{
        $usd = new Currency(['exchange_rate' => 1]);
        $clp = new Currency(['exchange_rate' => 970]);
        $this->assertSame('169750.00', $this->converter->convert('175.00', $usd, $clp));
    }

    public function test_it_converts_between_two_non_base_currencies():void{
        $eur = new Currency(['exchange_rate' => 0.90]);
        $clp = new Currency(['exchange_rate' => 970]);
        $this->assertSame('97000.00', $this->converter->convert(90, $eur, $clp));
    }

    public function test_it_rounds_to_two_decimals():void{
        $usd = new Currency(['exchange_rate' => 1]);
        $eur = new Currency(['exchange_rate' => 0.90]);
        $this->assertSame('9.50', $this->converter->convert('10.555', $usd, $eur));
    }

    public function test_output_has_no_thousands_separator():void{
        $usd = new Currency(['exchange_rate' => 1]);
        $clp = new Currency(['exchange_rate' => 970]);

        $this->assertSame('1261000.00', $this->converter->convert(1300, $usd, $clp));
    }

    public function test_it_rejects_an_invalid_source_rate():void{
        $this->expectException(InvalidArgumentException::class);

        $this->converter->convert(
            100,
            new Currency(['exchange_rate' => 0]),
            new Currency(['exchange_rate' => 970])
        );
    }
}