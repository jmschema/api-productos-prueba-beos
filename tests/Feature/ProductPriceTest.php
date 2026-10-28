<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductPriceTest extends TestCase{
    use RefreshDatabase;

    private Currency $usd;
    private Currency $clp;
    private Currency $eur;
    private Product $product;

    protected function setUp():void{
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());

        $this->usd = Currency::factory()->create(['name' => 'USD', 'symbol' => '$', 'exchange_rate' => 1]);
        $this->clp = Currency::factory()->create(['name' => 'CLP', 'symbol' => '$', 'exchange_rate' => 970]);
        $this->eur = Currency::factory()->create(['name' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.90]);

        $this->product = Product::factory()->create([
            'price' => 175,
            'currency_id' => $this->usd->id,
        ]);
    }

    public function test_it_lists_product_prices():void{
        $this->product->prices()->create(['currency_id' => $this->eur->id, 'price' => 160]);

        $this->getJson("/api/products/{$this->product->id}/prices")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.price', '160.00');
    }

    public function test_it_creates_a_price_with_a_manual_value():void{
        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->eur->id,
            'price' => 160,
        ])
            ->assertCreated()
            ->assertJsonPath('data.price', '160.00');

        $this->assertDatabaseHas('product_prices', [
            'product_id' => $this->product->id,
            'currency_id' => $this->eur->id,
        ]);
    }

    public function test_it_converts_the_price_when_no_value_is_sent():void{
        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->clp->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.price', '169750.00');
    }

    public function test_it_rejects_a_duplicate_currency():void{
        $this->product->prices()->create(['currency_id' => $this->eur->id, 'price' => 160]);

        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->eur->id,
            'price' => 170,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('currency_id');
    }

    public function test_it_rejects_the_base_currency():void{
        $this->postJson("/api/products/{$this->product->id}/prices", [
            'currency_id' => $this->usd->id,
            'price' => 175,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('currency_id');
    }
}