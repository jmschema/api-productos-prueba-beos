<?php

namespace Tests\Feature;

use App\Models\Currency;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase{
    use RefreshDatabase;

    protected function setUp():void{
        parent::setUp();
        Sanctum::actingAs(User::factory()->create());
    }

    private function validPayload(array $overrides=[]):array{
        return array_merge([
            'name' => 'Silla Gamer',
            'description' => 'Silla Gamer con soporte lumbar',
            'price' => 180.00,
            'currency_id' => Currency::factory()->create()->id,
            'tax_cost' => 42.50,
            'manufacturing_cost' => 80.00,
        ], $overrides);
    }

    public function test_it_lists_products_paginated():void{
        Product::factory()->count(3)->create();

        $this->getJson('/api/products')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => ['*' => ['id', 'name', 'price', 'currency']],
                'links',
                'meta',
            ]);
    }

    public function test_it_creates_a_product():void{
        $this->postJson('/api/products', $this->validPayload())->assertCreated()->assertJsonPath('data.name', 'Silla Gamer')->assertJsonPath('data.price', '180.00');

        $this->assertDatabaseHas('products', ['name' => 'Silla Gamer']);
    }

    public function test_it_validates_required_fields():void{
        $this->postJson('/api/products', [])->assertUnprocessable()->assertJsonValidationErrors(['name', 'price', 'currency_id', 'tax_cost', 'manufacturing_cost']);
    }

    public function test_it_rejects_negative_price_and_unknown_currency():void{
        $this->postJson('/api/products', $this->validPayload([
            'price' => -5,
            'currency_id' => 999,
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price', 'currency_id']);
    }

    public function test_it_shows_a_product_with_its_currency_and_prices():void{
        $product = Product::factory()->create();

        $this->getJson("/api/products/{$product->id}")->assertOk()->assertJsonPath('data.id', $product->id)->assertJsonStructure(['data' => ['currency' => ['id', 'name', 'symbol'], 'prices']]);
    }

    public function test_it_returns_404_for_a_missing_product():void{
        $this->getJson('/api/products/99999')->assertNotFound();
    }

    public function test_it_updates_only_the_sent_fields():void{
        $product = Product::factory()->create(['name' => 'Original', 'price' => 100]);

        $this->putJson("/api/products/{$product->id}", ['price' => 175])->assertOk()->assertJsonPath('data.price', '175.00')->assertJsonPath('data.name', 'Original');
    }

    public function test_it_soft_deletes_a_product():void{
        $product = Product::factory()->create();

        $this->deleteJson("/api/products/{$product->id}")->assertNoContent();
        $this->assertSoftDeleted($product);
        $this->getJson("/api/products/{$product->id}")->assertNotFound();
    }
}